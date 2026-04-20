<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;

class DocumentService
{
    public function attachTo(Model $documentable, array $data): Document
    {
        return $documentable->documents()->create([
            'type_document' => $data['type_document'],
            'nom_fichier'   => $data['nom_fichier'],
            'url'           => $data['url'],
            'taille_bytes'  => $data['taille_bytes'] ?? null,
            'mime_type'     => $data['mime_type'] ?? null,
            'uploaded_by'   => $data['uploaded_by'] ?? null,
        ]);
    }

    public function deleteDocument(Document $document): bool
    {
        return (bool) $document->delete();
    }
}
