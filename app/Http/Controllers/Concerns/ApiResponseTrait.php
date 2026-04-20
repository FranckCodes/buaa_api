<?php

namespace App\Http\Controllers\Concerns;

use App\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Opération réussie.',
        int $status = 200,
        ?array $meta = null
    ): JsonResponse {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function errorResponse(
        string $message = 'Une erreur est survenue.',
        int $status = 400,
        mixed $errors = null,
        mixed $data = null,
        ?array $meta = null
    ): JsonResponse {
        return ApiResponse::error($message, $status, $errors, $data, $meta);
    }

    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = 'Liste récupérée avec succès.',
        ?callable $transformer = null
    ): JsonResponse {
        return ApiResponse::paginated($paginator, $message, $transformer);
    }
}
