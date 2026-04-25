<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Pays;
use App\Models\Province;
use App\Models\Secteur;
use App\Models\Territoire;
use App\Models\Ville;
use Illuminate\Http\JsonResponse;

class GeoController extends Controller
{
    public function pays(): JsonResponse
    {
        $pays = Pays::orderBy('designation')->get(['id', 'code', 'designation']);

        return $this->successResponse($pays, 'Pays récupérés avec succès.');
    }

    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('designation')->get(['id', 'designation', 'pays_id']);

        return $this->successResponse($provinces, 'Provinces récupérées avec succès.');
    }

    public function territoires(Province $province): JsonResponse
    {
        $territoires = $province->territoires()->orderBy('designation')->get(['id', 'designation', 'province_id']);

        return $this->successResponse($territoires, 'Territoires récupérés avec succès.');
    }

    public function secteurs(Territoire $territoire): JsonResponse
    {
        $secteurs = $territoire->secteurs()->orderBy('designation')->get(['id', 'designation', 'territoire_id']);

        return $this->successResponse($secteurs, 'Secteurs récupérés avec succès.');
    }

    public function villes(Province $province): JsonResponse
    {
        $villes = $province->villes()->orderBy('designation')->get(['id', 'designation', 'province_id']);

        return $this->successResponse($villes, 'Villes récupérées avec succès.');
    }

    public function communes(Ville $ville): JsonResponse
    {
        $communes = $ville->communes()->orderBy('designation')->get(['id', 'designation', 'ville_id']);

        return $this->successResponse($communes, 'Communes récupérées avec succès.');
    }

    public function communesParProvince(Province $province): JsonResponse
    {
        // Cas Kinshasa : communes directement liées aux villes de la province
        $communes = Commune::whereHas('ville', fn ($q) => $q->where('province_id', $province->id))
            ->with('ville:id,designation')
            ->orderBy('designation')
            ->get(['id', 'ville_id', 'designation']);

        return $this->successResponse($communes, 'Communes récupérées avec succès.');
    }
}
