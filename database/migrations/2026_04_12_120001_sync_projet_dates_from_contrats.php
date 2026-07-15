<?php

use App\Models\Projet;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Projet::query()->each(function (Projet $projet) {
            $projet->syncDatesFromContrats();
        });
    }

    public function down(): void
    {
        //
    }
};
