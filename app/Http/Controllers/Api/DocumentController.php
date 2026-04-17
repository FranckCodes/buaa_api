<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\AttachDocumentRequest;
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
    protected function resolveDocumentable(string $type, int $id): Model
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

    public function attach(AttachDocumentRequest $request, string $type, int $id, DocumentService $documentService): JsonResponse
    {
        $documentable = $this->resolveDocumentable($type, $id);
        $document = $documentService->attachTo($documentable, $request->validated());

        return response()->json(['message' => 'Document attaché avec succès.', 'data' => $document], 201);
    }

    public function destroy(Document $document, DocumentService $documentService): JsonResponse
    {
        $documentService->deleteDocument($document);

        return response()->json(['message' => 'Document supprimé avec succès.']);
    }
}
