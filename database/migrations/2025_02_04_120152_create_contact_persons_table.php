<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('contact_persons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_fournisseur_id');
            $table->enum('civilite', ['M.', 'Mme', 'Mlle']);
            $table->string('nom');
            $table->string('prenoms');
            $table->string('fonction')->nullable();
            $table->string('telephone_1')->nullable();
            $table->string('telephone_2')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->enum('statut', ['Actif', 'Inactif'])->default('Actif');
            $table->boolean('contact_principal')->default(false);
            $table->timestamps();
            
            $table->foreign('client_fournisseur_id')->references('id')->on('client_fournisseurs')->onDelete('cascade');
            $table->index('client_fournisseur_id');
        });
    }

    public function down() {
        Schema::dropIfExists('contact_persons');
    }
};