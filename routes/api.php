<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Union\UnionController;
use App\Http\Controllers\Adhesion\AdhesionController;
use App\Http\Controllers\Adhesion\AdhesionRequestController;
use App\Http\Controllers\Cotisation\CotisationController;
use App\Http\Controllers\Credit\CreditController;
use App\Http\Controllers\Credit\CreditPaymentController;
use App\Http\Controllers\BusinessPlan\BusinessPlanController;
use App\Http\Controllers\Insurance\InsuranceController;
use App\Http\Controllers\Insurance\InsuranceClaimController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Conversation\ConversationController;
use App\Http\Controllers\Conversation\MessageController;
use App\Http\Controllers\SupportTicket\SupportTicketController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Reference\ReferenceController;

// Auth (public)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('unions', UnionController::class);
    Route::apiResource('adhesions', AdhesionController::class);
    Route::apiResource('adhesion-requests', AdhesionRequestController::class);
    Route::apiResource('cotisations', CotisationController::class);
    Route::apiResource('credits', CreditController::class);
    Route::apiResource('credit-payments', CreditPaymentController::class);
    Route::apiResource('business-plans', BusinessPlanController::class);
    Route::apiResource('insurances', InsuranceController::class);
    Route::apiResource('insurance-claims', InsuranceClaimController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('reports', ReportController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('conversations', ConversationController::class);
    Route::apiResource('messages', MessageController::class);
    Route::apiResource('support-tickets', SupportTicketController::class);
    Route::apiResource('documents', DocumentController::class);
    Route::apiResource('references', ReferenceController::class);
});
