# BUAA API — Documentation OpenAPI

## Structure complète

```
docs/
├── openapi.yaml              ← Point d'entrée principal (index + $ref)
├── paths/
│   ├── auth.yaml             ← POST /auth/login, register, me, logout
│   ├── credits.yaml          ← GET/POST /credits, approve, reject, register payment
│   ├── orders.yaml           ← GET/POST /orders, approve, reject, deliver
│   ├── reports.yaml          ← GET/POST /reports, moderate
│   ├── support.yaml          ← GET/POST /support-tickets, assign, resolve, close
│   ├── posts.yaml            ← GET/POST /posts, moderate, like, save, comments
│   ├── notifications.yaml    ← list, unread-count, read, read-all, delete
│   ├── messaging.yaml        ← conversations, messages, markAsRead
│   ├── documents.yaml        ← attach, show, delete
│   ├── insurances.yaml       ← CRUD + activate, claims, approveClaim, rejectClaim
│   ├── adhesions.yaml        ← unions, requests, approve, reject, adhesions
│   └── dashboard.yaml        ← admin overview/trends/kpis, supervisor, client
└── schemas/
    ├── common.yaml           ← ApiResponse, PaginatedMeta, ReferenceValue
    ├── user.yaml             ← User, Client
    ├── credit.yaml           ← Credit, CreditPayment
    ├── order.yaml            ← Order, OrderTracking
    ├── insurance.yaml        ← Insurance, InsuranceClaim, InsuranceBeneficiary
    ├── adhesion.yaml         ← Adhesion, AdhesionRequest, Union, Cotisation
    ├── social.yaml           ← Post, Comment, Notification, Message, Conversation, Document, Report, SupportTicket
    ├── requests.yaml         ← Tous les schemas de requête (StoreCreditRequest, etc.)
    └── responses.yaml        ← Success, Created, Unauthorized, Forbidden, NotFound, ValidationError, BusinessError
```

## Utilisation rapide

### Option 1 — Swagger Editor (aucune installation)
1. Ouvre https://editor.swagger.io
2. Colle le contenu de `docs/openapi.yaml`
3. Visualise et teste les endpoints

> Note : Swagger Editor ne résout pas les `$ref` vers des fichiers locaux.
> Pour ça, utilise Redocly CLI (option 2).

### Option 2 — Redocly CLI (recommandé)
```bash
npx @redocly/cli preview-docs docs/openapi.yaml
```
Ouvre automatiquement http://localhost:8080 avec la doc complète et les `$ref` résolus.

### Option 3 — Bundle en un seul fichier
```bash
npx @redocly/cli bundle docs/openapi.yaml -o docs/openapi.bundle.yaml
```
Génère un fichier unique prêt pour Swagger UI ou Postman.

### Option 4 — Laravel L5-Swagger
```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

## Authentification

Tous les endpoints protégés nécessitent :
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
    "total": 60,
    "from": 1,
    "to": 15
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

## Compte de test

```
Email    : admin@buaa.test
Password : password
```

## Endpoints — résumé

| Module | Endpoints |
|--------|-----------|
| Auth | register, login, me, logout |
| Références | 17 endpoints GET publics |
| Clients | index, show, storeProfile |
| Crédits | index, store, show, approve, reject, registerPayment |
| Commandes | index, store, show, approve, reject, deliver |
| Rapports | index, store, show, moderate |
| Support | index, store, show, assign, resolve, close |
| Posts | index, store, show, moderate, like, save, comments |
| Notifications | index, show, unreadCount, markAsRead, markAllAsRead, delete |
| Messagerie | index, startConversation, show, sendMessage, markAsRead |
| Documents | attach, show, delete |
| Assurances | index, store, show, activate, claimsIndex, storeClaim, approveClaim, rejectClaim |
| Adhésions | unionsIndex, storeUnion, requestsIndex, storeRequest, approveRequest, rejectRequest, index, show |
| Dashboard | adminOverview, adminTrends, adminKpis, adminRecentActivity, supervisorOverview, clientOverview |
