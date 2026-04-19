<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\AttachDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Adhesion;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Document;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\Order;
use App\Models\Report;
use App\Services\DocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    protected function resolveDocumentable(string $type, string $id): Model
    {
        return match ($type) {
            'client'    => Client::findOrFail($id),
            'credit'    => Credit::findOrFail($id),
            'adhesion'  => Adhesion::findOrFail($id),
            'insurance' => Insurance::findOrFail($id),
            'claim'     => InsuranceClaim::findOrFail($id),
            'order'     => Order::findOrFail($id),
            'report'    => Report::findOrFail($id),
            default     => abort(404, 'Type documentable non supporté.'),
        };
    }

    public function attach(AttachDocumentRequest $request, string $type, string $id, DocumentService $documentService): JsonResponse
    {
        $this->authorize('create', Document::class);

        $documentable = $this->resolveDocumentable($type, $id);
        $document     = $documentService->attachTo($documentable, $request->validated());

        return $this->successResponse(
            new DocumentResource($document->load('uploadedBy')),
            'Document attaché avec succès.',
            201
        );
    }

    public function show(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        return $this->successResponse(
            new DocumentResource($document->load('uploadedBy')),
            'Détail du document récupéré avec succès.'
        );
    }

    public function destroy(Document $document, DocumentService $documentService): JsonResponse
    {
        $this->authorize('delete', $document);

        $documentService->deleteDocument($document);

        return $this->successResponse(null, 'Document supprimé avec succès.');
    }
}
