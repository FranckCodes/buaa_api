<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    protected function list(string $model): JsonResponse
    {
        return response()->json([
            'message' => 'Référentiel chargé avec succès.',
            'data'    => $model::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function roles(): JsonResponse { return $this->list(Role::class); }
    public function userStatuses(): JsonResponse { return $this->list(UserStatus::class); }
    public function clientActivityTypes(): JsonResponse { return $this->list(ClientActivityType::class); }
    public function clientStructureTypes(): JsonResponse { return $this->list(ClientStructureType::class); }
    public function adhesionTypes(): JsonResponse { return $this->list(AdhesionType::class); }
    public function adhesionStatuses(): JsonResponse { return $this->list(AdhesionStatus::class); }
    public function creditTypes(): JsonResponse { return $this->list(CreditType::class); }
    public function creditStatuses(): JsonResponse { return $this->list(CreditStatus::class); }
    public function insuranceTypes(): JsonResponse { return $this->list(InsuranceType::class); }
    public function insuranceStatuses(): JsonResponse { return $this->list(InsuranceStatus::class); }
    public function orderTypes(): JsonResponse { return $this->list(OrderType::class); }
    public function orderStatuses(): JsonResponse { return $this->list(OrderStatus::class); }
    public function reportTypes(): JsonResponse { return $this->list(ReportType::class); }
    public function reportStatuses(): JsonResponse { return $this->list(ReportStatus::class); }
    public function postTags(): JsonResponse { return $this->list(PostTag::class); }
    public function postStatuses(): JsonResponse { return $this->list(PostStatus::class); }
    public function supportCategories(): JsonResponse { return $this->list(SupportCategory::class); }
    public function paymentModes(): JsonResponse { return $this->list(PaymentMode::class); }
}
