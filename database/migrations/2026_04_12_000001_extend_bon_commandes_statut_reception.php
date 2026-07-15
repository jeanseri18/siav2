<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Étend l'enum statut pour les réceptions partielles / complètes (MySQL/MariaDB).
     * Sous SQLite les valeurs sont déjà stockées en chaîne.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE bon_commandes MODIFY COLUMN statut ENUM(
                'en attente',
                'confirmée',
                'livrée',
                'annulée',
                'partiellement_reçu',
                'reçu'
            ) NOT NULL DEFAULT 'en attente'");
        }
    }

    public function down(): void
    {
        // Ne pas réduire l'enum si des lignes utilisent déjà partiellement_reçu / reçu.
    }
};
