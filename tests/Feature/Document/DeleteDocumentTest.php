<?php

namespace Tests\Feature\Document;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class DeleteDocumentTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_uploader_can_delete_document(): void
    {
        $this->seed();

        $user   = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $user->id]);

        $document = $client->documents()->create([
            'type_document' => 'piece_identite',
            'nom_fichier'   => 'id.pdf',
            'url'           => 'https://example.com/docs/id.pdf',
            'taille_bytes'  => 12345,
            'mime_type'     => 'application/pdf',
            'uploaded_by'   => $user->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/documents/{$document->id}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_other_user_cannot_delete_document(): void
    {
        $this->seed();

        $owner  = $this->createUserWithRole('client');
        $other  = $this->createUserWithRole('client');
        $client = Client::factory()->create(['id' => $owner->id]);

        $document = $client->documents()->create([
            'type_document' => 'piece_identite',
            'nom_fichier'   => 'id.pdf',
            'url'           => 'https://example.com/docs/id.pdf',
            'uploaded_by'   => $owner->id,
        ]);

        $this->actingAs($other, 'sanctum')
            ->deleteJson("/api/documents/{$document->id}")
            ->assertForbidden();
    }
}
