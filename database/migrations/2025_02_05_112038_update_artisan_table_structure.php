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
            // Ajouter seulement les champs manquants
            $table->string('fonction')->after('type');
            $table->string('localisation')->after('fonction');
            $table->string('rcc')->nullable()->after('localisation');
            $table->string('rccm')->nullable()->after('rcc');
            $table->string('boite_postale')->nullable()->after('rccm');
            $table->string('tel1')->after('boite_postale');
            $table->string('tel2')->nullable()->after('tel1');
            $table->string('mail')->nullable()->after('tel2');
            $table->string('reference')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisan', function (Blueprint $table) {
            // Supprimer les nouveaux champs
            $table->dropColumn([
                'civilite',
                'prenoms',
                'type_piece',
                'numero_piece',
                'date_naissance',
                'nationalite',
                'fonction',
                'localisation',
                'rcc',
                'rccm',
                'boite_postale',
                'tel1',
                'tel2',
                'mail'
            ]);
            
            // Remettre les anciens champs
            $table->unsignedBigInteger('id_corpmetier')->after('nom');
            $table->enum('type', ['artisan', 'travailleur'])->after('id_corpmetier');
            $table->string('telephone')->nullable()->after('type');
            $table->string('email')->nullable()->after('telephone');
            $table->text('adresse')->nullable()->after('email');
            $table->string('numero_licence')->nullable()->after('adresse');
            $table->integer('annees_experience')->nullable()->after('numero_licence');
            $table->decimal('tarif_horaire', 10, 2)->nullable()->after('annees_experience');
            $table->enum('disponibilite', ['disponible', 'occupe', 'en_conge'])->default('disponible')->after('tarif_horaire');
            $table->string('numero_siret')->nullable()->after('disponibilite');
            $table->date('date_embauche')->nullable()->after('numero_siret');
            $table->text('notes')->nullable()->after('date_embauche');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('notes');
            //nullable
            $table->foreign('id_corpmetier')->references('id')->on('corp_metiers');
        });
    }
};