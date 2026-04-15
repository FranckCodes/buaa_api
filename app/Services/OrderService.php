<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Reference\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function createOrder(array $data): Order
    {
        $status = OrderStatus::where('code', 'en_attente')->firstOrFail();

        return Order::create([
            'client_id'       => $data['client_id'],
            'order_type_id'   => $data['order_type_id'],
            'order_status_id' => $status->id,
            'montant'         => $data['montant'] ?? null,
            'description'     => $data['description'] ?? null,
            'justification'   => $data['justification'] ?? null,
            'quantite'        => $data['quantite'] ?? null,
            'unite'           => $data['unite'] ?? null,
            'priorite'        => $data['priorite'] ?? 'moyenne',
            'progression'     => 0,
            'date_soumission' => $data['date_soumission'] ?? now()->toDateString(),
        ]);
    }

    public function approveOrder(Order $order, int $processedBy): Order
    {
        return DB::transaction(function () use ($order, $processedBy) {
            $status = OrderStatus::where('code', 'approuve')->firstOrFail();

            $order->update([
                'order_status_id' => $status->id,
                'traite_par'      => $processedBy,
                'progression'     => 10,
            ]);

            $steps = [
                1 => 'Réception de la demande',
                2 => 'Validation',
                3 => 'Traitement',
                4 => 'Préparation',
                5 => 'Livraison',
            ];

            foreach ($steps as $ordre => $label) {
                OrderTracking::firstOrCreate(
                    ['order_id' => $order->id, 'ordre' => $ordre],
                    [
                        'label'     => $label,
                        'done'      => $ordre === 1,
                        'date_done' => $ordre === 1 ? now()->toDateString() : null,
                    ]
                );
            }

            return $order->fresh('trackingSteps', 'status');
        });
    }

    public function rejectOrder(Order $order, int $processedBy): Order
    {
        $status = OrderStatus::where('code', 'rejete')->firstOrFail();

        $order->update(['order_status_id' => $status->id, 'traite_par' => $processedBy]);

        return $order->fresh('status');
    }

    public function markOrderDelivered(Order $order): Order
    {
        $status = OrderStatus::where('code', 'livre')->firstOrFail();

        $order->update(['order_status_id' => $status->id, 'progression' => 100]);

        $order->trackingSteps()->where('ordre', 5)->update([
            'done'      => true,
            'date_done' => now()->toDateString(),
        ]);

        return $order->fresh('status', 'trackingSteps');
    }
}
