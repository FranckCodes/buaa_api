<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ApproveOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['client.user', 'type', 'status', 'treatedBy'])
            ->latest()->paginate(15);

        return response()->json(['message' => 'Liste des commandes.', 'data' => $orders]);
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        $order = $orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Commande créée avec succès.',
            'data'    => $order->load(['client.user', 'type', 'status']),
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['client.user', 'type', 'status', 'treatedBy', 'trackingSteps', 'documents']);

        return response()->json(['message' => 'Détail de la commande.', 'data' => $order]);
    }

    public function approve(ApproveOrderRequest $request, Order $order, OrderService $orderService): JsonResponse
    {
        $result = $orderService->approveOrder($order, $request->integer('processed_by'));

        return response()->json(['message' => 'Commande approuvée avec succès.', 'data' => $result]);
    }

    public function reject(Request $request, Order $order, OrderService $orderService): JsonResponse
    {
        $request->validate(['processed_by' => ['required', 'integer', 'exists:users,id']]);

        $result = $orderService->rejectOrder($order, $request->integer('processed_by'));

        return response()->json(['message' => 'Commande rejetée.', 'data' => $result]);
    }

    public function deliver(Order $order, OrderService $orderService): JsonResponse
    {
        $result = $orderService->markOrderDelivered($order);

        return response()->json(['message' => 'Commande marquée comme livrée.', 'data' => $result]);
    }
}
