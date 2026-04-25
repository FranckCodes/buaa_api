# Guide d'intégration Frontend — BUAA API

> **Base URL :** `http://192.168.1.254:8000/api`
> **Auth :** Bearer Token (Sanctum) — à inclure dans le header `Authorization: Bearer {token}`

---

## Sommaire

1. [Authentification](#1-authentification)
2. [Routes de référence (dropdowns)](#2-routes-de-référence-dropdowns)
3. [Créer un Client](#3-créer-un-client)
4. [Créer un Superviseur](#4-créer-un-superviseur)
5. [Créer un Admin](#5-créer-un-admin)
6. [Logique géographique](#6-logique-géographique)

---

## 1. Authentification

### A. Connexion par email ou login_code

```
POST /auth/login
```

```json
{
  "login":    "superadmin@buaa.cd",
  "password": "password"
}
```

> `login` accepte : email **ou** login_code (ex: `CLT-A3K9P22026`)

**Réponse :**
```json
{
  "success": true,
  "data": {
    "user": {
      "id":         "ADM-001",
      "nom":        "Administrateur",
      "postnom":    null,
      "prenom":     "Super",
      "nom_complet":"Administrateur Super",
      "email":      "superadmin@buaa.cd",
      "login_code": "SUPA9K3R2026",
      "telephone":  "+243827029543",
      "roles":      [{ "code": "super_admin", "label": "Super administrateur" }],
      "status":     { "code": "actif", "label": "Actif" }
    },
    "token": "1|abc123..."
  }
}
```

---

### B. Connexion OTP téléphone (2 étapes)

**Étape 1 — Demander le code**
```
POST /auth/otp/request
```
```json
{ "telephone": "+243827029543" }
```

**Étape 2 — Vérifier et connecter**
```
POST /auth/otp/verify
```
```json
{
  "telephone": "+243827029543",
  "code":      "482915"
}
```

---

### C. Profil connecté

```
GET /auth/me
Authorization: Bearer {token}
```

---

### D. Déconnexion

```
POST /auth/logout
Authorization: Bearer {token}
```

---

## 2. Routes de référence (dropdowns)

> Toutes ces routes sont **publiques** (pas de token requis).

### Géographie

| Route | Usage dans le formulaire |
|-------|--------------------------|
| `GET /geo/pays` | Champ `nationalite` (liste des pays) |
| `GET /geo/provinces` | Champ `province_id` |
| `GET /geo/provinces/{id}/territoires` | Champ `territoire_id` (après sélection province) |
| `GET /geo/territoires/{id}/secteurs` | Champ `secteur_id` (après sélection territoire) |
| `GET /geo/provinces/{id}/villes` | Champ `ville_id` (après sélection province) |
| `GET /geo/villes/{id}/communes` | Champ `commune_id` (après sélection ville) |
| `GET /geo/provinces/{id}/communes` | Champ `commune_id` pour **Kinshasa** (directement) |

**Exemple réponse `GET /geo/provinces` :**
```json
{
  "success": true,
  "data": [
    { "id": 1, "designation": "Kinshasa", "pays_id": 40 },
    { "id": 2, "designation": "Kongo Central", "pays_id": 40 },
    ...
  ]
}
```

> **Logique Kinshasa :** Si `province.designation` contient "Kinshasa", afficher les communes via `GET /geo/provinces/{id}/communes` au lieu des territoires.

---

### Références métier

| Route | Usage |
|-------|-------|
| `GET /references/client-activity-types` | Champ `client_activity_type_id` |
| `GET /references/client-structure-types` | Champ `client_structure_type_id` |
| `GET /references/roles` | Champ `role_codes` |
| `GET /references/user-statuses` | Statut du compte |
| `GET /references/payment-modes` | Mode de paiement |

---

### Superviseurs disponibles (pour assigner à un client)

```
GET /users?role=superviseur
Authorization: Bearer {token}
```

---

## 3. Créer un Client

> **Flux :** 2 appels API

### Étape 1 — Créer le compte utilisateur

```
POST /auth/register
```

```json
{
  "nom":                    "Kabongo",
  "postnom":                "Mutombo",
  "prenom":                 "Pierre",
  "email":                  "pierre.kabongo@email.com",
  "telephone":              "+243812345678",
  "password":               "password123",
  "password_confirmation":  "password123",
  "role_codes":             ["client"]
}
```

> Le `login_code` est **généré automatiquement** (ex: `CLT-A3K9P22026`). Il est retourné dans la réponse.

**Réponse :**
```json
{
  "data": {
    "user": {
      "id":         "CLT-00042",
      "login_code": "CLT-A3K9P22026",
      ...
    },
    "token": "2|xyz..."
  }
}
```

---

### Étape 2 — Créer le profil client

```
POST /users/{user_id}/client-profile
Authorization: Bearer {token}
```

```json
{
  "date_naissance":  "1990-05-15",
  "lieu_naissance":  "Lubumbashi",
  "sexe":            "M",
  "etat_civil":      "marie",
  "nationalite":     "Congolaise",

  "adresse_complete": "Avenue Kasavubu, N°12",
  "province_id":      3,
  "territoire_id":    10,
  "secteur_id":       null,
  "ville_id":         null,
  "commune_id":       null,

  "client_activity_type_id":  2,
  "client_structure_type_id": 1,
  "profession_detaillee":     "Agriculteur maraîcher",
  "experience_annees":        8,
  "superficie_exploitation":  2.5,
  "type_culture":             "Légumes",
  "nombre_animaux":           0,

  "revenus_mensuels":       450.00,
  "autres_sources_revenus": "Vente de bois",
  "banque_principale":      "Rawbank",
  "numero_compte":          "CD123456789",

  "ref_nom":       "Jean Mukendi",
  "ref_telephone": "+243812345678",
  "ref_relation":  "Frère",

  "superviseur_id": "SUP-00001"
}
```

---

### Champs du formulaire client — détail

| Champ | Type | Requis | Source des options |
|-------|------|--------|--------------------|
| `nom` | string | ✅ | — |
| `postnom` | string | ❌ | — |
| `prenom` | string | ✅ | — |
| `email` | email | ✅ | — |
| `telephone` | string | ❌ | — |
| `password` | string (min 8) | ✅ | — |
| `date_naissance` | date YYYY-MM-DD | ❌ | — |
| `lieu_naissance` | string | ❌ | — |
| `sexe` | `M` ou `F` | ❌ | — |
| `etat_civil` | enum | ❌ | `celibataire`, `marie`, `divorce`, `veuf` |
| `nationalite` | string | ❌ | `GET /geo/pays` → champ `designation` |
| `adresse_complete` | string | ❌ | — |
| `province_id` | integer | ❌ | `GET /geo/provinces` |
| `territoire_id` | integer | ❌ | `GET /geo/provinces/{id}/territoires` |
| `secteur_id` | integer | ❌ | `GET /geo/territoires/{id}/secteurs` |
| `ville_id` | integer | ❌ | `GET /geo/provinces/{id}/villes` |
| `commune_id` | integer | ❌ | `GET /geo/villes/{id}/communes` ou `GET /geo/provinces/{id}/communes` (Kinshasa) |
| `client_activity_type_id` | integer | ❌ | `GET /references/client-activity-types` |
| `client_structure_type_id` | integer | ❌ | `GET /references/client-structure-types` |
| `profession_detaillee` | string | ❌ | — |
| `experience_annees` | integer | ❌ | — |
| `superficie_exploitation` | decimal | ❌ | — |
| `type_culture` | string | ❌ | — |
| `nombre_animaux` | integer | ❌ | — |
| `revenus_mensuels` | decimal | ❌ | — |
| `autres_sources_revenus` | string | ❌ | — |
| `banque_principale` | string | ❌ | — |
| `numero_compte` | string | ❌ | — |
| `ref_nom` | string | ❌ | — |
| `ref_telephone` | string | ❌ | — |
| `ref_relation` | string | ❌ | — |
| `superviseur_id` | string | ❌ | `GET /users?role=superviseur` |

---

## 4. Créer un Superviseur

> **Flux :** 1 appel API (endpoint dédié)

```
POST /superviseurs
Authorization: Bearer {token}
```

```json
{
  "nom":      "Mukendi",
  "postnom":  "Kalala",
  "prenom":   "Jean",
  "email":    "jean.mukendi@buaa.cd",
  "telephone": "+243812345678",
  "password":  "password123",
  "password_confirmation": "password123",

  "matricule":        "MAT-2026-001",
  "telephone_pro":    "+243899000001",
  "notes":            "Superviseur zone Kongo Central",
  "is_active":        true,

  "date_naissance":  "1985-03-20",
  "lieu_naissance":  "Matadi",
  "sexe":            "M",
  "etat_civil":      "marie",
  "nationalite":     "Congolaise",

  "adresse_complete": "Avenue de la Paix, N°5",
  "province_id":      2,
  "territoire_id":    8,
  "secteur_id":       null,
  "ville_id":         null,
  "commune_id":       null,

  "niveau_etude":           "Licence",
  "specialite":             "Agronomie",
  "experience_annees":      6,
  "type_piece_identite":    "CIN",
  "numero_piece_identite":  "CD-123456789",

  "zones": [
    { "province_id": 2, "territoire_id": 8,    "secteur_id": null, "commune_id": null },
    { "province_id": 2, "territoire_id": 9,    "secteur_id": null, "commune_id": null },
    { "province_id": 1, "territoire_id": null, "secteur_id": null, "commune_id": 3   }
  ]
}
```

---

### Champs du formulaire superviseur — détail

| Champ | Type | Requis | Source des options |
|-------|------|--------|--------------------|
| `nom` | string | ✅ | — |
| `postnom` | string | ❌ | — |
| `prenom` | string | ✅ | — |
| `email` | email | ✅ | — |
| `telephone` | string | ❌ | — |
| `password` | string (min 8) | ✅ | — |
| `matricule` | string | ❌ | — |
| `telephone_pro` | string | ❌ | — |
| `notes` | string | ❌ | — |
| `is_active` | boolean | ❌ | défaut `true` |
| `date_naissance` | date YYYY-MM-DD | ❌ | — |
| `lieu_naissance` | string | ❌ | — |
| `sexe` | `M` ou `F` | ❌ | — |
| `etat_civil` | enum | ❌ | `celibataire`, `marie`, `divorce`, `veuf` |
| `nationalite` | string | ❌ | `GET /geo/pays` → champ `designation` |
| `adresse_complete` | string | ❌ | — |
| `province_id` | integer | ❌ | `GET /geo/provinces` |
| `territoire_id` | integer | ❌ | `GET /geo/provinces/{id}/territoires` |
| `secteur_id` | integer | ❌ | `GET /geo/territoires/{id}/secteurs` |
| `ville_id` | integer | ❌ | `GET /geo/provinces/{id}/villes` |
| `commune_id` | integer | ❌ | `GET /geo/villes/{id}/communes` |
| `niveau_etude` | string | ❌ | — |
| `specialite` | string | ❌ | — |
| `experience_annees` | integer | ❌ | — |
| `type_piece_identite` | string | ❌ | `CIN`, `Passeport`, `Permis de conduire` |
| `numero_piece_identite` | string | ❌ | — |
| `zones[].province_id` | integer | ✅ par zone | `GET /geo/provinces` |
| `zones[].territoire_id` | integer | ❌ | `GET /geo/provinces/{id}/territoires` |
| `zones[].secteur_id` | integer | ❌ | `GET /geo/territoires/{id}/secteurs` |
| `zones[].commune_id` | integer | ❌ | `GET /geo/provinces/{id}/communes` (Kinshasa) |

> **Règles zones :**
> - `secteur_id` nécessite `territoire_id`
> - Sur Kinshasa : utiliser `commune_id`, pas `territoire_id`
> - Un superviseur peut avoir plusieurs zones

---

## 5. Créer un Admin

> **Flux :** 1 appel API (endpoint dédié)

```
POST /admins
Authorization: Bearer {token}
```

```json
{
  "nom":      "Kabila",
  "postnom":  null,
  "prenom":   "Marie",
  "email":    "marie.kabila@buaa.cd",
  "telephone": "+243812345678",
  "password":  "password123",
  "password_confirmation": "password123",

  "matricule":        "ADM-2026-001",
  "telephone_pro":    "+243899000002",
  "notes":            "Admin régional Ouest",
  "is_active":        true,

  "date_naissance":  "1980-07-10",
  "lieu_naissance":  "Kinshasa",
  "sexe":            "F",
  "etat_civil":      "marie",
  "nationalite":     "Congolaise",

  "adresse_complete": "Boulevard du 30 Juin, N°18",
  "province_id":      1,
  "territoire_id":    null,
  "secteur_id":       null,
  "ville_id":         null,
  "commune_id":       3,

  "niveau_etude":           "Master",
  "specialite":             "Gestion de projets",
  "experience_annees":      10,
  "type_piece_identite":    "Passeport",
  "numero_piece_identite":  "CD-987654321",

  "provinces": [1, 2, 5]
}
```

---

### Champs du formulaire admin — détail

| Champ | Type | Requis | Source des options |
|-------|------|--------|--------------------|
| `nom` | string | ✅ | — |
| `postnom` | string | ❌ | — |
| `prenom` | string | ✅ | — |
| `email` | email | ✅ | — |
| `telephone` | string | ❌ | — |
| `password` | string (min 8) | ✅ | — |
| `matricule` | string | ❌ | — |
| `telephone_pro` | string | ❌ | — |
| `notes` | string | ❌ | — |
| `is_active` | boolean | ❌ | défaut `true` |
| `date_naissance` | date YYYY-MM-DD | ❌ | — |
| `lieu_naissance` | string | ❌ | — |
| `sexe` | `M` ou `F` | ❌ | — |
| `etat_civil` | enum | ❌ | `celibataire`, `marie`, `divorce`, `veuf` |
| `nationalite` | string | ❌ | `GET /geo/pays` → champ `designation` |
| `adresse_complete` | string | ❌ | — |
| `province_id` | integer | ❌ | `GET /geo/provinces` (résidence perso) |
| `territoire_id` | integer | ❌ | `GET /geo/provinces/{id}/territoires` |
| `secteur_id` | integer | ❌ | `GET /geo/territoires/{id}/secteurs` |
| `ville_id` | integer | ❌ | `GET /geo/provinces/{id}/villes` |
| `commune_id` | integer | ❌ | `GET /geo/villes/{id}/communes` |
| `niveau_etude` | string | ❌ | — |
| `specialite` | string | ❌ | — |
| `experience_annees` | integer | ❌ | — |
| `type_piece_identite` | string | ❌ | `CIN`, `Passeport`, `Permis de conduire` |
| `numero_piece_identite` | string | ❌ | — |
| `provinces` | integer[] | ❌ | `GET /geo/provinces` (provinces à charge) |

---

## 6. Logique géographique

### Cascade des dropdowns

```
province_id sélectionné
    ├── Si province = Kinshasa
    │       └── GET /geo/provinces/{id}/communes  → commune_id
    └── Si province ≠ Kinshasa
            ├── GET /geo/provinces/{id}/territoires → territoire_id
            │       └── GET /geo/territoires/{id}/secteurs → secteur_id
            └── GET /geo/provinces/{id}/villes → ville_id
                    └── GET /geo/villes/{id}/communes → commune_id
```

### Détecter Kinshasa

```javascript
const isKinshasa = province.designation.toLowerCase().includes('kinshasa');
```

### Exemple React/Vue — chargement en cascade

```javascript
// 1. Charger les provinces au montage
const provinces = await fetch('/api/geo/provinces').then(r => r.json());

// 2. Quand province change
async function onProvinceChange(provinceId) {
  const province = provinces.find(p => p.id === provinceId);
  const isKinshasa = province.designation.toLowerCase().includes('kinshasa');

  if (isKinshasa) {
    // Charger communes directement
    communes = await fetch(`/api/geo/provinces/${provinceId}/communes`).then(r => r.json());
    showCommunes = true;
    showTerritoires = false;
  } else {
    // Charger territoires
    territoires = await fetch(`/api/geo/provinces/${provinceId}/territoires`).then(r => r.json());
    showTerritoires = true;
    showCommunes = false;
  }
}

// 3. Quand territoire change
async function onTerritoireChange(territoireId) {
  secteurs = await fetch(`/api/geo/territoires/${territoireId}/secteurs`).then(r => r.json());
}
```

---

## Récapitulatif des routes créées

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| `POST` | `/auth/login` | ❌ | Connexion email/login_code |
| `POST` | `/auth/otp/request` | ❌ | Demander OTP |
| `POST` | `/auth/otp/verify` | ❌ | Vérifier OTP |
| `GET` | `/auth/me` | ✅ | Profil connecté |
| `POST` | `/auth/logout` | ✅ | Déconnexion |
| `GET` | `/geo/pays` | ❌ | Liste des pays |
| `GET` | `/geo/provinces` | ❌ | Liste des provinces |
| `GET` | `/geo/provinces/{id}/territoires` | ❌ | Territoires d'une province |
| `GET` | `/geo/provinces/{id}/villes` | ❌ | Villes d'une province |
| `GET` | `/geo/provinces/{id}/communes` | ❌ | Communes d'une province (Kinshasa) |
| `GET` | `/geo/territoires/{id}/secteurs` | ❌ | Secteurs d'un territoire |
| `GET` | `/geo/villes/{id}/communes` | ❌ | Communes d'une ville |
| `GET` | `/references/client-activity-types` | ❌ | Types d'activité |
| `GET` | `/references/client-structure-types` | ❌ | Types de structure |
| `GET` | `/references/roles` | ❌ | Rôles |
| `GET` | `/users?role=superviseur` | ✅ | Liste des superviseurs |
| `POST` | `/auth/register` | ❌ | Créer compte utilisateur |
| `POST` | `/users/{id}/client-profile` | ✅ | Créer profil client |
| `POST` | `/superviseurs` | ✅ | Créer superviseur |
| `POST` | `/admins` | ✅ | Créer admin |
