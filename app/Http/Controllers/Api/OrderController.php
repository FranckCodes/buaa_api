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

        return OrderResource::collection(
            Order::with(['client.user', 'type', 'status'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des commandes.'])->response();
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        $this->authorize('create', Order::class);
        $order = $orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Commande créée avec succès.',
            'data'    => new OrderResource($order->load(['client.user', 'type', 'status'])),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return response()->json([
            'message' => 'Détail de la commande.',
            'data'    => new OrderResource($order->load(['client.user', 'type', 'status', 'treatedBy', 'trackingSteps', 'documents'])),
        ]);
    }

    public function approve(ApproveOrderRequest $request, Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('approve', $order);
        $result = $orderService->approveOrder($order, $request->integer('processed_by'));

        return response()->json(['message' => 'Commande approuvée avec succès.', 'data' => new OrderResource($result)]);
    }

    public function reject(Request $request, Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('reject', $order);
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);
        $result = $orderService->rejectOrder($order, $request->integer('processed_by'));

        return response()->json(['message' => 'Commande rejetée.', 'data' => new OrderResource($result)]);
    }

    public function deliver(Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('deliver', $order);
        $result = $orderService->markOrderDelivered($order);

        return response()->json(['message' => 'Commande marquée comme livrée.', 'data' => new OrderResource($result)]);
    }
}
