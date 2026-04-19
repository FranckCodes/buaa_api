<?php

use App\Http\Controllers\Api\AdhesionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\MessagingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MessagingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SupportTicketController;
use Illuminate\Support\Facades\Route;

// Auth (public)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Références (public)
Route::prefix('references')->group(function () {
    Route::get('roles', [ReferenceController::class, 'roles']);
    Route::get('user-statuses', [ReferenceController::class, 'userStatuses']);
    Route::get('client-activity-types', [ReferenceController::class, 'clientActivityTypes']);
    Route::get('client-structure-types', [ReferenceController::class, 'clientStructureTypes']);
    Route::get('adhesion-types', [ReferenceController::class, 'adhesionTypes']);
    Route::get('adhesion-statuses', [ReferenceController::class, 'adhesionStatuses']);
    Route::get('credit-types', [ReferenceController::class, 'creditTypes']);
    Route::get('credit-statuses', [ReferenceController::class, 'creditStatuses']);
    Route::get('insurance-types', [ReferenceController::class, 'insuranceTypes']);
    Route::get('insurance-statuses', [ReferenceController::class, 'insuranceStatuses']);
    Route::get('order-types', [ReferenceController::class, 'orderTypes']);
    Route::get('order-statuses', [ReferenceController::class, 'orderStatuses']);
    Route::get('report-types', [ReferenceController::class, 'reportTypes']);
    Route::get('report-statuses', [ReferenceController::class, 'reportStatuses']);
    Route::get('post-tags', [ReferenceController::class, 'postTags']);
    Route::get('post-statuses', [ReferenceController::class, 'postStatuses']);
    Route::get('support-categories', [ReferenceController::class, 'supportCategories']);
    Route::get('payment-modes', [ReferenceController::class, 'paymentModes']);
});

// Staff : admin / superviseur
Route::middleware(['auth:sanctum', 'role:super_admin,admin,superviseur'])->group(function () {
    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{client}', [ClientController::class, 'show']);
    Route::get('adhesion-requests', [AdhesionController::class, 'requestsIndex']);
    Route::get('adhesions', [AdhesionController::class, 'index']);
    Route::get('adhesions/{adhesion}', [AdhesionController::class, 'show']);
    Route::get('credits', [CreditController::class, 'index']);
    Route::get('credits/{credit}', [CreditController::class, 'show']);
    Route::get('insurances', [InsuranceController::class, 'index']);
    Route::get('insurances/{insurance}', [InsuranceController::class, 'show']);
    Route::get('insurance-claims', [InsuranceController::class, 'claimsIndex']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/{report}', [ReportController::class, 'show']);
    Route::get('support-tickets', [SupportTicketController::class, 'index']);
    Route::get('support-tickets/{supportTicket}', [SupportTicketController::class, 'show']);
    Route::post('support-tickets/{supportTicket}/assign', [SupportTicketController::class, 'assign']);
    Route::post('support-tickets/{supportTicket}/resolve', [SupportTicketController::class, 'resolve']);
    Route::post('posts/{post}/moderate', [PostController::class, 'moderate']);
    Route::post('adhesion-requests/{adhesionRequest}/approve', [AdhesionController::class, 'approveRequest']);
    Route::post('adhesion-requests/{adhesionRequest}/reject', [AdhesionController::class, 'rejectRequest']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
    Route::post('reports/{report}/moderate', [ReportController::class, 'moderate']);
});

// Admin seulement
Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->group(function () {
    Route::post('users/{user}/client-profile', [ClientController::class, 'storeProfile']);
    Route::put('clients/{client}/profile', [ClientController::class, 'updateProfile']);
    Route::post('credits/{credit}/approve', [CreditController::class, 'approve']);
    Route::post('credits/{credit}/reject', [CreditController::class, 'reject']);
    Route::post('credit-payments/{payment}/register', [CreditController::class, 'registerPayment']);
    Route::post('insurances/{insurance}/activate', [InsuranceController::class, 'activate']);
    Route::post('insurance-claims/{claim}/approve', [InsuranceController::class, 'approveClaim']);
    Route::post('insurance-claims/{claim}/reject', [InsuranceController::class, 'rejectClaim']);
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver']);
    Route::post('support-tickets/{supportTicket}/close', [SupportTicketController::class, 'close']);
    Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
});

// Client seulement
Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
    Route::post('credits', [CreditController::class, 'store']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::post('reports', [ReportController::class, 'store']);
    Route::post('support-tickets', [SupportTicketController::class, 'store']);
    Route::post('adhesion-requests', [AdhesionController::class, 'storeRequest']);
    Route::post('insurances', [InsuranceController::class, 'store']);
    Route::post('insurance-claims', [InsuranceController::class, 'storeClaim']);
});

// Tous les utilisateurs authentifiés
Route::middleware('auth:sanctum')->group(function () {
    // Notifications
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{post}', [PostController::class, 'show']);
    Route::post('posts', [PostController::class, 'store']);
    Route::post('posts/{post}/like', [PostController::class, 'toggleLike']);
    Route::post('posts/{post}/save', [PostController::class, 'toggleSave']);
    Route::post('posts/{post}/comments', [PostController::class, 'addComment']);

    Route::get('conversations', [MessagingController::class, 'index']);
    Route::post('conversations', [MessagingController::class, 'startConversation']);
    Route::get('conversations/{conversation}', [MessagingController::class, 'show']);
    Route::post('conversations/{conversation}/messages', [MessagingController::class, 'sendMessage']);
    Route::post('conversations/{conversation}/read', [MessagingController::class, 'markAsRead']);

    Route::post('documents/{type}/{id}', [DocumentController::class, 'attach']);

    // Conversations
    Route::get('conversations', [MessagingController::class, 'index']);
    Route::post('conversations', [MessagingController::class, 'startConversation']);
    Route::get('conversations/{conversation}', [MessagingController::class, 'show']);
    Route::post('conversations/{conversation}/messages', [MessagingController::class, 'sendMessage']);
    Route::post('conversations/{conversation}/read', [MessagingController::class, 'markAsRead']);
});
