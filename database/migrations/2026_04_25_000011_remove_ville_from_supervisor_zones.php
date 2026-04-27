<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Cette migration est un no-op.
 * ville_id a été retiré directement de la migration de création (000009).
 * Conservée pour ne pas casser l'historique des migrations déjà exécutées en local.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Rien à faire — ville_id n'existe pas dans supervisor_zones en production
    }

    public function down(): void
    {
        // Rien à faire
    }
};
