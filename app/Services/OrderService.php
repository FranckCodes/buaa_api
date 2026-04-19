<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Reference\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected IdGeneratorService $idGenerator,
        protected NotificationService $notificationService
    ) {}

    public function createOrder(array $data): Order
    {
        $status = OrderStatus::where('code', 'en_attente')->firstOrFail();

        return Order::create([
            'id'              => $this->idGenerator->generateOrderId(),
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

    public function approveOrder(Order $order, string $processedBy): Order
    {
        if (in_array($order->status?->code, ['approuve', 'livre'])) {
            throw new BusinessException('Cette commande ne peut plus être approuvée.', 422);
        }

        if ($order->status?->code === 'rejete') {
            throw new BusinessException('Cette commande a déjà été rejetée.', 422);
        }

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

            $this->notificationService->create([
                'user_id'  => $order->client_id,
                'category' => 'app',
                'type'     => 'success',
                'title'    => 'Commande approuvée',
                'body'     => 'Votre commande a été approuvée.',
            ]);

            return $order->fresh(['status', 'trackingSteps', 'type', 'client']);
        });
    }

    public function rejectOrder(Order $order, string $processedBy): Order
    {
        if ($order->status?->code === 'rejete') {
            throw new BusinessException('Cette commande est déjà rejetée.', 422);
        }

        if ($order->status?->code === 'livre') {
            throw new BusinessException('Une commande livrée ne peut pas être rejetée.', 422);
        }

        $status = OrderStatus::where('code', 'rejete')->firstOrFail();

        $order->update(['order_status_id' => $status->id, 'traite_par' => $processedBy]);

        $this->notificationService->create([
            'user_id'  => $order->client_id,
            'category' => 'app',
            'type'     => 'alert',
            'title'    => 'Commande rejetée',
            'body'     => 'Votre commande a été rejetée.',
        ]);

        return $order->fresh('status');
    }

    public function markOrderDelivered(Order $order): Order
    {
        if ($order->status?->code === 'livre') {
            throw new BusinessException('Cette commande est déjà livrée.', 422);
        }

        if ($order->status?->code === 'rejete') {
            throw new BusinessException('Une commande rejetée ne peut pas être livrée.', 422);
        }

        return DB::transaction(function () use ($order) {
            $status = OrderStatus::where('code', 'livre')->firstOrFail();

            $order->update(['order_status_id' => $status->id, 'progression' => 100]);

            $order->trackingSteps()->update(['done' => true]);

            $order->trackingSteps()->where('ordre', 5)->update([
                'done'      => true,
                'date_done' => now()->toDateString(),
            ]);

            $this->notificationService->create([
                'user_id'  => $order->client_id,
                'category' => 'app',
                'type'     => 'success',
                'title'    => 'Commande livrée',
                'body'     => 'Votre commande a été marquée comme livrée.',
            ]);

            return $order->fresh(['status', 'trackingSteps']);
        });
    }
}
