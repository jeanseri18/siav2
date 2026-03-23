<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bu_budget_excels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bu_id')->constrained('bus')->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->timestamps();

            $table->unique(['bu_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bu_budget_excels');
    }
};
