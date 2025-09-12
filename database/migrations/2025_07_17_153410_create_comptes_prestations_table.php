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
        Schema::create('comptes_prestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestation_id')->constrained('prestation')->onDelete('cascade');
            $table->enum('type_compte', ['materiel', 'main_oeuvre', 'transport', 'autres']);
            $table->decimal('montant', 15, 2);
            $table->text('description');
            $table->date('date_compte');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['prestation_id', 'type_compte']);
            $table->index('date_compte');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes_prestations');
    }
};
