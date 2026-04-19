<?php

namespace Tests\Feature\Document;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class AttachDocumentTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_attach_document_to_own_profile(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/documents/client/{$client->id}", [
                'type_document' => 'piece_identite',
                'nom_fichier'   => 'id.pdf',
                'url'           => 'https://example.com/docs/id.pdf',
                'taille_bytes'  => 204800,
                'mime_type'     => 'application/pdf',
                'uploaded_by'   => $user->id,
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('documents', [
            'type_document' => 'piece_identite',
            'nom_fichier'   => 'id.pdf',
            'uploaded_by'   => $user->id,
        ]);
    }

    public function test_admin_can_attach_document_to_credit(): void
    {
        $this->seed();

        $admin      = $this->createUserWithRole('admin');
        $clientUser = $this->createUserWithRole('client');
        $client     = Client::factory()->create(['id' => $clientUser->id]);
        $credit     = \App\Models\Credit::factory()->create(['client_id' => $client->id]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/documents/credit/{$credit->id}", [
                'type_document' => 'contrat',
                'nom_fichier'   => 'contrat.pdf',
                'url'           => 'https://example.com/docs/contrat.pdf',
                'uploaded_by'   => $admin->id,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('documents', [
            'documentable_type' => 'App\\Models\\Credit',
            'documentable_id'   => $credit->id,
        ]);
    }
}
