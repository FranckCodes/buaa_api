<?php

use App\Http\Controllers\Api\AdhesionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Api\MessagingController;
use App\Http\Controllers\Api\OrderController;
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

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    // Clients
    Route::get('clients', [ClientController::class, 'index']);
    Route::get('clients/{client}', [ClientController::class, 'show']);
    Route::post('users/{user}/client-profile', [ClientController::class, 'storeProfile']);
    Route::put('clients/{client}/profile', [ClientController::class, 'updateProfile']);

    // Adhésions
    Route::get('adhesion-requests', [AdhesionController::class, 'requestsIndex']);
    Route::post('adhesion-requests', [AdhesionController::class, 'storeRequest']);
    Route::post('adhesion-requests/{adhesionRequest}/approve', [AdhesionController::class, 'approveRequest']);
    Route::post('adhesion-requests/{adhesionRequest}/reject', [AdhesionController::class, 'rejectRequest']);
    Route::get('adhesions', [AdhesionController::class, 'index']);
    Route::get('adhesions/{adhesion}', [AdhesionController::class, 'show']);

    // Crédits
    Route::get('credits', [CreditController::class, 'index']);
    Route::post('credits', [CreditController::class, 'store']);
    Route::get('credits/{credit}', [CreditController::class, 'show']);
    Route::post('credits/{credit}/approve', [CreditController::class, 'approve']);
    Route::post('credits/{credit}/reject', [CreditController::class, 'reject']);
    Route::post('credit-payments/{payment}/register', [CreditController::class, 'registerPayment']);

    // Assurances
    Route::get('insurances', [InsuranceController::class, 'index']);
    Route::post('insurances', [InsuranceController::class, 'store']);
    Route::get('insurances/{insurance}', [InsuranceController::class, 'show']);
    Route::post('insurances/{insurance}/activate', [InsuranceController::class, 'activate']);
    Route::get('insurance-claims', [InsuranceController::class, 'claimsIndex']);
    Route::post('insurance-claims', [InsuranceController::class, 'storeClaim']);
    Route::post('insurance-claims/{claim}/approve', [InsuranceController::class, 'approveClaim']);
    Route::post('insurance-claims/{claim}/reject', [InsuranceController::class, 'rejectClaim']);

    // Commandes
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver']);

    // Rapports
    Route::get('reports', [ReportController::class, 'index']);
    Route::post('reports', [ReportController::class, 'store']);
    Route::get('reports/{report}', [ReportController::class, 'show']);
    Route::post('reports/{report}/moderate', [ReportController::class, 'moderate']);

    // Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{post}', [PostController::class, 'show']);
    Route::post('posts/{post}/moderate', [PostController::class, 'moderate']);
    Route::post('posts/{post}/like', [PostController::class, 'toggleLike']);
    Route::post('posts/{post}/save', [PostController::class, 'toggleSave']);
    Route::post('posts/{post}/comments', [PostController::class, 'addComment']);

    // Messagerie
    Route::get('conversations', [MessagingController::class, 'index']);
    Route::post('conversations', [MessagingController::class, 'startConversation']);
    Route::get('conversations/{conversation}', [MessagingController::class, 'show']);
    Route::post('conversations/{conversation}/messages', [MessagingController::class, 'sendMessage']);
    Route::post('conversations/{conversation}/read', [MessagingController::class, 'markAsRead']);

    // Support
    Route::get('support-tickets', [SupportTicketController::class, 'index']);
    Route::post('support-tickets', [SupportTicketController::class, 'store']);
    Route::get('support-tickets/{supportTicket}', [SupportTicketController::class, 'show']);
    Route::post('support-tickets/{supportTicket}/assign', [SupportTicketController::class, 'assign']);
    Route::post('support-tickets/{supportTicket}/resolve', [SupportTicketController::class, 'resolve']);
    Route::post('support-tickets/{supportTicket}/close', [SupportTicketController::class, 'close']);

    // Documents
    Route::post('documents/{type}/{id}', [DocumentController::class, 'attach']);
    Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
});
