<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sans doctrine/dbal, ->change() ne modifie pas les colonnes : date_debut restait NOT NULL.
 * Alignement avec ProjetController (création sans dates, sync depuis les contrats).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `projets` MODIFY `date_debut` DATE NULL');
        DB::statement('ALTER TABLE `projets` MODIFY `date_fin` DATE NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE `projets` SET `date_debut` = COALESCE(`date_debut`, DATE(`date_creation`), CURDATE()) WHERE `date_debut` IS NULL');
        DB::statement('ALTER TABLE `projets` MODIFY `date_debut` DATE NOT NULL');
    }
};
