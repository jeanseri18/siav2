<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Étend l'enum `users.role` pour caissier et controleur_caisse (aligné sur User::roleOptionsForUserManagement).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM(
            'admin',
            'dg',
            'chef_projet',
            'conducteur_travaux',
            'chef_chantier',
            'comptable',
            'magasinier',
            'acheteur',
            'controleur_gestion',
            'caissier',
            'controleur_caisse',
            'secretaire',
            'chauffeur',
            'gardien',
            'employe'
        ) NOT NULL DEFAULT 'employe'");
    }

    /**
     * Rétablit l'enum sans caissier / controleur_caisse (échoue si des lignes utilisent encore ces rôles).
     */
    public function down(): void
    {
        DB::table('users')->whereIn('role', ['caissier', 'controleur_caisse'])->update(['role' => 'employe']);

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM(
            'admin',
            'dg',
            'chef_projet',
            'conducteur_travaux',
            'chef_chantier',
            'comptable',
            'magasinier',
            'acheteur',
            'controleur_gestion',
            'secretaire',
            'chauffeur',
            'gardien',
            'employe'
        ) NOT NULL DEFAULT 'employe'");
    }
};
