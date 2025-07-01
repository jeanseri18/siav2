<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('client_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('categorie', ['Entreprise', 'Particulier'])->nullable();
            $table->string('nom_raison_sociale')->nullable();
            $table->string('prenoms')->nullable(); // Uniquement pour Particulier
            $table->enum('type', ['Client', 'Fournisseur'])->nullable();
            $table->string('n_rccm')->nullable(); // Uniquement pour Entreprise
            $table->string('n_cc')->nullable(); // Uniquement pour Entreprise
            $table->string('regime_imposition')->nullable();
            $table->integer('delai_paiement')->nullable();
            $table->enum('mode_paiement', ['Virement', 'Chèque', 'Espèces'])->nullable();
            $table->string('adresse_localisation')->nullable();
            $table->string('boite_postale')->nullable();
            $table->string('secteur_activite')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->enum('statut', ['Actif', 'Inactif'])->default('Actif');
            $table->unsignedBigInteger('id_bu')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('client_fournisseurs');
    }
};
