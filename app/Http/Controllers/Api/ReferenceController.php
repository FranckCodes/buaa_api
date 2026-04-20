<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reference\ReferenceValueResource;
use App\Models\Reference\AdhesionStatus;
use App\Models\Reference\AdhesionType;
use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use App\Models\Reference\CreditStatus;
use App\Models\Reference\CreditType;
use App\Models\Reference\InsuranceStatus;
use App\Models\Reference\InsuranceType;
use App\Models\Reference\OrderStatus;
use App\Models\Reference\OrderType;
use App\Models\Reference\PaymentMode;
use App\Models\Reference\PostStatus;
use App\Models\Reference\PostTag;
use App\Models\Reference\ReportStatus;
use App\Models\Reference\ReportType;
use App\Models\Reference\Role;
use App\Models\Reference\SupportCategory;
use App\Models\Reference\UserStatus;
use Illuminate\Http\JsonResponse;

class ReferenceController extends Controller
{
    protected function list(string $model, string $message): JsonResponse
    {
        return $this->successResponse(
            ReferenceValueResource::collection(
                $model::where('is_active', true)->orderBy('sort_order')->get()
            ),
            $message
        );
    }

    public function roles(): JsonResponse { return $this->list(Role::class, 'Rôles récupérés avec succès.'); }
    public function userStatuses(): JsonResponse { return $this->list(UserStatus::class, 'Statuts utilisateur récupérés avec succès.'); }
    public function clientActivityTypes(): JsonResponse { return $this->list(ClientActivityType::class, "Types d'activité récupérés avec succès."); }
    public function clientStructureTypes(): JsonResponse { return $this->list(ClientStructureType::class, 'Types de structure récupérés avec succès.'); }
    public function adhesionTypes(): JsonResponse { return $this->list(AdhesionType::class, "Types d'adhésion récupérés avec succès."); }
    public function adhesionStatuses(): JsonResponse { return $this->list(AdhesionStatus::class, "Statuts d'adhésion récupérés avec succès."); }
    public function creditTypes(): JsonResponse { return $this->list(CreditType::class, 'Types de crédit récupérés avec succès.'); }
    public function creditStatuses(): JsonResponse { return $this->list(CreditStatus::class, 'Statuts de crédit récupérés avec succès.'); }
    public function insuranceTypes(): JsonResponse { return $this->list(InsuranceType::class, "Types d'assurance récupérés avec succès."); }
    public function insuranceStatuses(): JsonResponse { return $this->list(InsuranceStatus::class, "Statuts d'assurance récupérés avec succès."); }
    public function orderTypes(): JsonResponse { return $this->list(OrderType::class, 'Types de commande récupérés avec succès.'); }
    public function orderStatuses(): JsonResponse { return $this->list(OrderStatus::class, 'Statuts de commande récupérés avec succès.'); }
    public function reportTypes(): JsonResponse { return $this->list(ReportType::class, 'Types de rapport récupérés avec succès.'); }
    public function reportStatuses(): JsonResponse { return $this->list(ReportStatus::class, 'Statuts de rapport récupérés avec succès.'); }
    public function postTags(): JsonResponse { return $this->list(PostTag::class, 'Tags de publication récupérés avec succès.'); }
    public function postStatuses(): JsonResponse { return $this->list(PostStatus::class, 'Statuts de publication récupérés avec succès.'); }
    public function supportCategories(): JsonResponse { return $this->list(SupportCategory::class, 'Catégories de support récupérées avec succès.'); }
    public function paymentModes(): JsonResponse { return $this->list(PaymentMode::class, 'Modes de paiement récupérés avec succès.'); }
}
