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
        Schema::table('artisan', function (Blueprint $table) {
            // Informations de contact
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            
            // Informations professionnelles
            $table->string('numero_licence')->nullable();
            $table->integer('annees_experience')->nullable();
            $table->decimal('tarif_horaire', 10, 2)->nullable();
            $table->enum('disponibilite', ['disponible', 'occupe', 'en_conge'])->default('disponible');
            
            // Informations administratives
            $table->string('numero_siret')->nullable();
            $table->date('date_embauche')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisan', function (Blueprint $table) {
            $table->dropColumn([
                'telephone',
                'email',
                'adresse',
                'numero_licence',
                'annees_experience',
                'tarif_horaire',
                'disponibilite',
                'numero_siret',
                'date_embauche',
                'notes',
                'statut'
            ]);
        });
    }
};
