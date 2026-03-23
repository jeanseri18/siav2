<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_bancaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bu_id')->constrained('bus')->cascadeOnDelete();
            $table->foreignId('banque_id')->constrained('banques')->cascadeOnDelete();
            $table->enum('type', ['entree', 'sortie']);
            $table->enum('mode', ['virement', 'cheque', 'espece']);
            $table->decimal('montant', 15, 2);
            $table->date('date_operation');
            $table->string('numero_piece')->nullable();
            $table->string('beneficiaire')->nullable();
            $table->text('libelle')->nullable();
            $table->boolean('est_passe')->default(false);
            $table->date('date_passage')->nullable();
            $table->timestamps();

            $table->index(['bu_id', 'banque_id']);
            $table->index(['bu_id', 'type']);
            $table->index(['bu_id', 'est_passe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_bancaires');
    }
};
