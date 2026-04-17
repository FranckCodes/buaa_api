<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Opération réussie.',
        int $status = 200,
        ?array $meta = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
            'errors'  => null,
        ], $status);
    }

    public static function error(
        string $message = 'Une erreur est survenue.',
        int $status = 400,
        mixed $errors = null,
        mixed $data = null,
        ?array $meta = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
            'errors'  => $errors,
        ], $status);
    }

    public static function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Liste récupérée avec succès.',
        ?callable $transformer = null
    ): JsonResponse {
        $items = $transformer
            ? collect($paginator->items())->map($transformer)->values()
            : $paginator->items();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'errors' => null,
        ]);
    }
}
