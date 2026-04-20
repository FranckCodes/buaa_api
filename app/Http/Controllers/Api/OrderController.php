<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ApproveOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        return $this->paginatedResponse(
            Order::with(['client.user', 'type', 'status', 'treatedBy'])->latest()->paginate(15),
            'Liste des commandes récupérée avec succès.',
            fn ($order) => new OrderResource($order)
        );
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $orderService->createOrder($request->validated());

        return $this->successResponse(
            new OrderResource($order->load(['client.user', 'type', 'status'])),
            'Commande créée avec succès.',
            201
        );
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->successResponse(
            new OrderResource($order->load(['client.user', 'type', 'status', 'treatedBy', 'trackingSteps'])),
            'Détail de la commande récupéré avec succès.'
        );
    }

    public function approve(ApproveOrderRequest $request, Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('approve', $order);

        return $this->successResponse(
            new OrderResource($orderService->approveOrder($order, $request->string('processed_by')->toString())),
            'Commande approuvée avec succès.'
        );
    }

    public function reject(Request $request, Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('reject', $order);
        $request->validate(['processed_by' => ['required', 'string', 'exists:users,id']]);

        return $this->successResponse(
            new OrderResource($orderService->rejectOrder($order, $request->string('processed_by')->toString())),
            'Commande rejetée avec succès.'
        );
    }

    public function deliver(Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('deliver', $order);

        return $this->successResponse(
            new OrderResource($orderService->markOrderDelivered($order)),
            'Commande marquée comme livrée.'
        );
    }
}
