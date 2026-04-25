<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Superviseur;
use App\Models\SupervisorZone;

class SupervisorZoneService
{
    /**
     * Vérifie si un superviseur peut être assigné à un client
     * en fonction de la couverture géographique de ses zones.
     *
     * Règle : la zone du superviseur doit couvrir la zone du client.
     * Plus une zone est précise (champs remplis), plus elle est restrictive.
     * Un champ null dans la zone = "toute la zone à ce niveau".
     */
    public function canSupervise(Superviseur $superviseur, Client $client): bool
    {
        $zones = $superviseur->activeZones()->get();

        foreach ($zones as $zone) {
            if ($this->zoneCoversClient($zone, $client)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si une zone couvre un client.
     *
     * Logique de matching hiérarchique :
     * - province_id doit toujours correspondre
     * - Province normale :
     *     si zone.territoire_id → client doit être dans ce territoire
     *     si zone.secteur_id    → client doit être dans ce secteur
     * - Kinshasa (ville-province) :
     *     si zone.commune_id    → client doit être dans cette commune
     *     sinon                 → toute Kinshasa est couverte
     * - Un champ null dans la zone = pas de restriction à ce niveau
     */
    public function zoneCoversClient(SupervisorZone $zone, Client $client): bool
    {
        // Province — toujours obligatoire
        if ($zone->province_id !== $client->province_id) {
            return false;
        }

        $isKinshasa = $this->isProvinceKinshasa($zone->province_id);

        if ($isKinshasa) {
            // Kinshasa : matching par commune uniquement
            if ($zone->commune_id !== null && $zone->commune_id !== $client->commune_id) {
                return false;
            }
        } else {
            // Province normale : matching par territoire → secteur
            if ($zone->territoire_id !== null) {
                if ($zone->territoire_id !== $client->territoire_id) {
                    return false;
                }

                if ($zone->secteur_id !== null) {
                    if ($zone->secteur_id !== $client->secteur_id) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Retourne tous les superviseurs qui couvrent la zone d'un client.
     */
    public function getSuperviseursPourClient(Client $client): \Illuminate\Support\Collection
    {
        return Superviseur::with(['user', 'activeZones'])
            ->where('is_active', true)
            ->get()
            ->filter(fn ($sup) => $this->canSupervise($sup, $client))
            ->values();
    }

    /**
     * Ajoute une zone à un superviseur.
     * Vérifie que la hiérarchie géographique est cohérente.
     */
    public function addZone(Superviseur $superviseur, array $data): SupervisorZone
    {
        $this->validateZoneHierarchy($data);

        return SupervisorZone::firstOrCreate(
            [
                'superviseur_id' => $superviseur->id,
                'province_id'    => $data['province_id'],
                'territoire_id'  => $data['territoire_id'] ?? null,
                'secteur_id'     => $data['secteur_id'] ?? null,
                'ville_id'       => $data['ville_id'] ?? null,
                'commune_id'     => $data['commune_id'] ?? null,
            ],
            ['is_active' => true]
        );
    }

    /**
     * Valide la cohérence hiérarchique d'une zone.
     *
     * Règles :
     * - province_id toujours obligatoire
     * - Province normale : peut avoir territoire_id (et secteur_id si territoire_id renseigné)
     * - Kinshasa (ville-province) : peut avoir commune_id directement, pas de territoire
     * - secteur_id sans territoire_id → invalide
     * - commune_id sans ville_id → invalide SAUF si c'est Kinshasa (province sans territoire)
     */
    private function validateZoneHierarchy(array $data): void
    {
        $territoireId = $data['territoire_id'] ?? null;
        $secteurId    = $data['secteur_id'] ?? null;
        $villeId      = $data['ville_id'] ?? null;
        $communeId    = $data['commune_id'] ?? null;
        $provinceId   = $data['province_id'];

        // Secteur sans territoire → invalide
        if ($secteurId && !$territoireId) {
            throw new \InvalidArgumentException(
                'Un secteur ne peut pas être défini sans territoire.'
            );
        }

        // Ville sans commune → ok (ville entière)
        // Commune sans ville → seulement autorisé si la province est Kinshasa (pas de territoire)
        if ($communeId && !$villeId) {
            $isKinshasa = $this->isProvinceKinshasa($provinceId);

            if (!$isKinshasa) {
                throw new \InvalidArgumentException(
                    'Une commune ne peut être définie sans ville que pour Kinshasa.'
                );
            }

            // Kinshasa + commune → pas de territoire autorisé
            if ($territoireId) {
                throw new \InvalidArgumentException(
                    'Kinshasa est une ville-province : pas de territoire, seulement des communes.'
                );
            }
        }

        // Territoire sur Kinshasa → invalide
        if ($territoireId) {
            $isKinshasa = $this->isProvinceKinshasa($provinceId);
            if ($isKinshasa) {
                throw new \InvalidArgumentException(
                    'Kinshasa est une ville-province : utilisez commune_id au lieu de territoire_id.'
                );
            }
        }
    }

    /**
     * Vérifie si une province est Kinshasa (ville-province sans territoire).
     */
    private function isProvinceKinshasa(int $provinceId): bool
    {
        return \App\Models\Province::where('id', $provinceId)
            ->whereRaw('LOWER(designation) LIKE ?', ['%kinshasa%'])
            ->exists();
    }
}
