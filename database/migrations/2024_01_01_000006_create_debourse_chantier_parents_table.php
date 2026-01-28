<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debourse_chantier_parents', function (Blueprint $table) {
            $table->id();
            $table->string('ref');
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->string('statut')->default('brouillon');
            $table->unsignedBigInteger('dqe_id')->nullable();
            $table->unsignedBigInteger('contrat_id');
            $table->timestamps();

            $table->foreign('dqe_id')->references('id')->on('dqes')->onDelete('set null');
            $table->foreign('contrat_id')->references('id')->on('contrats')->onDelete('cascade');
            $table->index(['contrat_id', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debourse_chantier_parents');
    }
};