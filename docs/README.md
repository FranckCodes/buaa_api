# BUAA API — Documentation OpenAPI

## Structure

```
docs/
├── openapi.yaml          # Fichier principal (schemas + responses)
├── paths/
│   ├── auth.yaml         # POST /auth/login, register, me, logout
│   ├── credits.yaml      # GET/POST /credits, approve, reject, register payment
│   ├── orders.yaml       # GET/POST /orders, approve, reject, deliver
│   ├── reports.yaml      # GET/POST /reports, moderate
│   ├── support.yaml      # GET/POST /support-tickets, assign, resolve, close
│   ├── posts.yaml        # GET/POST /posts, moderate, like, save, comments
│   ├── notifications.yaml # GET /notifications, unread-count, read, read-all, delete
│   ├── messaging.yaml    # GET/POST /conversations, messages, read
│   ├── documents.yaml    # POST/GET/DELETE /documents/{type}/{id}
│   ├── insurances.yaml   # GET/POST /insurances, activate, claims, approveClaim
│   ├── adhesions.yaml    # GET/POST /unions, adhesion-requests, adhesions
│   └── dashboard.yaml    # GET /dashboard/admin/*, supervisor/*, client/*
└── README.md
```

## Utilisation

### Option 1 — Swagger Editor (rapide)
1. Ouvre https://editor.swagger.io
2. Colle le contenu de `openapi.yaml`
3. Visualise et teste les endpoints

### Option 2 — Swagger UI local
```bash
npx @redocly/cli preview-docs docs/openapi.yaml
```

### Option 3 — Laravel avec L5-Swagger
```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

## Authentification

Tous les endpoints protégés nécessitent un header :
```
Authorization: Bearer {token}
```

Obtenez le token via `POST /api/auth/login`.

## Format de réponse standard

```json
{
  "success": true,
  "message": "Opération réussie.",
  "data": {},
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 60
  },
  "errors": null
}
```

## Rôles

| Rôle | Accès |
|------|-------|
| `super_admin` | Accès total |
| `admin` | Gestion administrative complète |
| `superviseur` | Portefeuille clients assignés |
| `client` | Ses propres données uniquement |

## Super admin de test

```
Email    : admin@buaa.test
Password : password
```
