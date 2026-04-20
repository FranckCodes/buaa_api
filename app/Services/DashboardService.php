<?php

namespace App\Services;

use App\Models\Adhesion;
use App\Models\AdhesionRequest;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Post;
use App\Models\Report;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getAdminOverview(): array
    {
        return [
            'users' => [
                'total'   => User::count(),
                'clients' => User::whereHas('roles', fn ($q) => $q->where('code', 'client'))->count(),
                'staff'   => User::whereHas('roles', fn ($q) => $q->whereIn('code', ['super_admin', 'admin', 'superviseur']))->count(),
            ],

            'clients' => [
                'total'              => Client::count(),
                'with_supervisor'    => Client::whereNotNull('superviseur_id')->count(),
                'without_supervisor' => Client::whereNull('superviseur_id')->count(),
            ],

            'credits' => [
                'total'                  => Credit::count(),
                'en_analyse'             => Credit::whereHas('status', fn ($q) => $q->where('code', 'en_analyse'))->count(),
                'actifs'                 => Credit::whereHas('status', fn ($q) => $q->where('code', 'actif'))->count(),
                'rembourses'             => Credit::whereHas('status', fn ($q) => $q->where('code', 'rembourse'))->count(),
                'rejetes'                => Credit::whereHas('status', fn ($q) => $q->where('code', 'rejete'))->count(),
                'montant_demande_total'  => (float) Credit::sum('montant_demande'),
                'montant_approuve_total' => (float) Credit::sum('montant_approuve'),
                'montant_rembourse_total' => (float) Credit::sum('montant_rembourse'),
            ],

            'orders' => [
                'total'     => Order::count(),
                'en_attente' => Order::whereHas('status', fn ($q) => $q->where('code', 'en_attente'))->count(),
                'approuvees' => Order::whereHas('status', fn ($q) => $q->where('code', 'approuve'))->count(),
                'en_cours'  => Order::whereHas('status', fn ($q) => $q->where('code', 'en_cours'))->count(),
                'livrees'   => Order::whereHas('status', fn ($q) => $q->where('code', 'livre'))->count(),
                'rejetees'  => Order::whereHas('status', fn ($q) => $q->where('code', 'rejete'))->count(),
            ],

            'reports' => [
                'total'     => Report::count(),
                'submitted' => Report::whereHas('status', fn ($q) => $q->where('code', 'submitted'))->count(),
                'validated' => Report::whereHas('status', fn ($q) => $q->where('code', 'validated'))->count(),
                'revision'  => Report::whereHas('status', fn ($q) => $q->where('code', 'revision'))->count(),
                'rejected'  => Report::whereHas('status', fn ($q) => $q->where('code', 'rejected'))->count(),
            ],

            'insurances' => [
                'total'            => Insurance::count(),
                'actives'          => Insurance::whereHas('status', fn ($q) => $q->where('code', 'active'))->count(),
                'en_attente'       => Insurance::whereHas('status', fn ($q) => $q->where('code', 'en_attente'))->count(),
                'expirees'         => Insurance::whereHas('status', fn ($q) => $q->where('code', 'expiree'))->count(),
                'claims_total'     => InsuranceClaim::count(),
                'claims_en_analyse' => InsuranceClaim::where('statut', 'en_analyse')->count(),
                'claims_approuvees' => InsuranceClaim::where('statut', 'approuve')->count(),
                'claims_rejetees'  => InsuranceClaim::where('statut', 'rejete')->count(),
            ],

            'adhesions' => [
                'requests_total'       => AdhesionRequest::count(),
                'requests_en_attente'  => AdhesionRequest::where('statut', 'en_attente')->count(),
                'requests_approuvees'  => AdhesionRequest::where('statut', 'approuve')->count(),
                'requests_rejetees'    => AdhesionRequest::where('statut', 'rejete')->count(),
                'adhesions_total'      => Adhesion::count(),
                'adhesions_actives'    => Adhesion::whereHas('status', fn ($q) => $q->where('code', 'actif'))->count(),
                'adhesions_suspendues' => Adhesion::whereHas('status', fn ($q) => $q->where('code', 'suspendu'))->count(),
            ],

            'support' => [
                'tickets_total' => SupportTicket::count(),
                'ouverts'       => SupportTicket::where('statut', 'ouvert')->count(),
                'en_cours'      => SupportTicket::where('statut', 'en_cours')->count(),
                'resolus'       => SupportTicket::where('statut', 'resolu')->count(),
                'fermes'        => SupportTicket::where('statut', 'ferme')->count(),
            ],

            'feed' => [
                'posts_total'    => Post::count(),
                'posts_pending'  => Post::whereHas('status', fn ($q) => $q->where('code', 'pending'))->count(),
                'posts_approved' => Post::whereHas('status', fn ($q) => $q->where('code', 'approved'))->count(),
                'posts_rejected' => Post::whereHas('status', fn ($q) => $q->where('code', 'rejected'))->count(),
            ],

            'notifications' => [
                'total'    => Notification::count(),
                'non_lues' => Notification::where('is_read', false)->count(),
            ],
        ];
    }

    public function getAdminMonthlyTrends(int $months = 6): array
    {
        return [
            'credits'          => $this->monthlyCount('credits', 'created_at', $months),
            'orders'           => $this->monthlyCount('orders', 'created_at', $months),
            'reports'          => $this->monthlyCount('reports', 'created_at', $months),
            'posts'            => $this->monthlyCount('posts', 'created_at', $months),
            'support_tickets'  => $this->monthlyCount('support_tickets', 'created_at', $months),
            'insurance_claims' => $this->monthlyCount('insurance_claims', 'created_at', $months),
        ];
    }

    public function getAdminKpis(): array
    {
        return [
            ['key' => 'clients_total',      'label' => 'Clients',              'value' => Client::count()],
            ['key' => 'credits_actifs',      'label' => 'Crédits actifs',       'value' => Credit::whereHas('status', fn ($q) => $q->where('code', 'actif'))->count()],
            ['key' => 'orders_en_attente',   'label' => 'Commandes en attente', 'value' => Order::whereHas('status', fn ($q) => $q->where('code', 'en_attente'))->count()],
            ['key' => 'tickets_ouverts',     'label' => 'Tickets ouverts',      'value' => SupportTicket::where('statut', 'ouvert')->count()],
            ['key' => 'posts_pending',       'label' => 'Publications en attente', 'value' => Post::whereHas('status', fn ($q) => $q->where('code', 'pending'))->count()],
            ['key' => 'insurances_actives',  'label' => 'Assurances actives',   'value' => Insurance::whereHas('status', fn ($q) => $q->where('code', 'active'))->count()],
        ];
    }

    public function getAdminRecentActivity(): array
    {
        return [
            'recent_credits' => Credit::with('client.user', 'status')->latest()->limit(5)->get(),
            'recent_orders'  => Order::with('client.user', 'status')->latest()->limit(5)->get(),
            'recent_reports' => Report::with('client.user', 'status')->latest()->limit(5)->get(),
            'recent_tickets' => SupportTicket::with('client.user')->latest()->limit(5)->get(),
        ];
    }

    public function getSupervisorOverview(string $supervisorId): array
    {
        $clientIds = Client::where('superviseur_id', $supervisorId)->pluck('id');

        return [
            'clients' => ['total' => $clientIds->count()],

            'credits' => [
                'total'      => Credit::whereIn('client_id', $clientIds)->count(),
                'en_analyse' => Credit::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'en_analyse'))->count(),
                'actifs'     => Credit::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'actif'))->count(),
            ],

            'orders' => [
                'total'      => Order::whereIn('client_id', $clientIds)->count(),
                'en_attente' => Order::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'en_attente'))->count(),
                'livrees'    => Order::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'livre'))->count(),
            ],

            'reports' => [
                'total'     => Report::whereIn('client_id', $clientIds)->count(),
                'submitted' => Report::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'submitted'))->count(),
                'validated' => Report::whereIn('client_id', $clientIds)->whereHas('status', fn ($q) => $q->where('code', 'validated'))->count(),
            ],

            'support' => [
                'tickets_total' => SupportTicket::whereIn('client_id', $clientIds)->count(),
                'ouverts'       => SupportTicket::whereIn('client_id', $clientIds)->where('statut', 'ouvert')->count(),
            ],
        ];
    }

    public function getClientOverview(string $clientId): array
    {
        return [
            'credits' => [
                'total'  => Credit::where('client_id', $clientId)->count(),
                'actifs' => Credit::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'actif'))->count(),
            ],

            'orders' => [
                'total'      => Order::where('client_id', $clientId)->count(),
                'en_attente' => Order::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'en_attente'))->count(),
                'livrees'    => Order::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'livre'))->count(),
            ],

            'reports' => [
                'total'     => Report::where('client_id', $clientId)->count(),
                'validated' => Report::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'validated'))->count(),
            ],

            'insurances' => [
                'total'   => Insurance::where('client_id', $clientId)->count(),
                'actives' => Insurance::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'active'))->count(),
            ],

            'adhesions' => [
                'total'   => Adhesion::where('client_id', $clientId)->count(),
                'actives' => Adhesion::where('client_id', $clientId)->whereHas('status', fn ($q) => $q->where('code', 'actif'))->count(),
            ],

            'support' => [
                'tickets_total' => SupportTicket::where('client_id', $clientId)->count(),
                'ouverts'       => SupportTicket::where('client_id', $clientId)->where('statut', 'ouvert')->count(),
            ],

            'notifications' => [
                'non_lues' => Notification::where('user_id', $clientId)->where('is_read', false)->count(),
            ],
        ];
    }

    protected function monthlyCount(string $table, string $dateColumn, int $months): array
    {
        $rows = DB::table($table)
            ->selectRaw("DATE_FORMAT($dateColumn, '%Y-%m') as period, COUNT(*) as total")
            ->where($dateColumn, '>=', now()->subMonths($months - 1)->startOfMonth())
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return $rows->map(fn ($row) => [
            'period' => $row->period,
            'total'  => (int) $row->total,
        ])->values()->all();
    }
}
