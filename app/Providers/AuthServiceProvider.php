<?php

namespace App\Providers;

use App\Models\Adhesion;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Credit;
use App\Models\Document;
use App\Models\Insurance;
use App\Models\Order;
use App\Models\Post;
use App\Models\Report;
use App\Models\SupportTicket;
use App\Policies\AdhesionPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\CreditPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\InsurancePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PostPolicy;
use App\Policies\ReportPolicy;
use App\Policies\SupportTicketPolicy;use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Client::class        => ClientPolicy::class,
        Credit::class        => CreditPolicy::class,
        Order::class         => OrderPolicy::class,
        Report::class        => ReportPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
        Post::class          => PostPolicy::class,
        Notification::class  => NotificationPolicy::class,
        Conversation::class  => ConversationPolicy::class,
        Document::class      => DocumentPolicy::class,
        Insurance::class     => InsurancePolicy::class,
        Adhesion::class      => AdhesionPolicy::class,    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
