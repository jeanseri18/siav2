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
        Schema::create('frais_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->decimal('montant_base', 15, 2)->comment('Montant sur lequel les frais généraux sont calculés');
            $table->decimal('pourcentage', 5, 2)->default(10.00)->comment('Pourcentage des frais généraux (généralement 10%)');
            $table->decimal('montant_total', 15, 2)->comment('Montant total des frais généraux');
            $table->text('description')->nullable();
            $table->date('date_calcul');
            $table->string('statut')->default('brouillon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frais_generals');
    }
};