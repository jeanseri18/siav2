<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            // Séparer nom et prénom
            $table->string('prenom')->after('name')->nullable();
            $table->renameColumn('name', 'nom');
            
            // Informations professionnelles
            $table->string('poste')->after('email')->nullable(); // Chef chantier, conducteur travaux, comptable, etc.
            $table->string('telephone')->after('poste')->nullable();
            $table->string('adresse')->after('telephone')->nullable();
            $table->date('date_embauche')->after('adresse')->nullable();
            $table->decimal('salaire', 10, 2)->after('date_embauche')->nullable();
            $table->string('numero_cnss')->after('salaire')->nullable();
            
            // Informations personnelles
            $table->date('date_naissance')->after('numero_cnss')->nullable();
            $table->enum('sexe', ['M', 'F'])->after('date_naissance')->nullable();
            $table->string('lieu_naissance')->after('sexe')->nullable();
            $table->string('nationalite')->after('lieu_naissance')->nullable();
            $table->enum('situation_matrimoniale', ['célibataire', 'marié(e)', 'divorcé(e)', 'veuf/veuve'])->after('nationalite')->nullable();
            
            // Documents
            $table->string('numero_cni')->after('situation_matrimoniale')->nullable();
            $table->string('numero_passeport')->after('numero_cni')->nullable();
            
            // Mise à jour des rôles
            $table->dropColumn('role');
        });
        
        // Recréer la colonne role avec les nouveaux rôles
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin', 
                'dg', 
                'chef_projet', 
                'conducteur_travaux', 
                'chef_chantier', 
                'comptable', 
                'magasinier', 
                'acheteur',
                'controleur_gestion',
                'secretaire',
                'chauffeur',
                'gardien',
                'employe'
            ])->after('email')->default('employe');
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'prenom', 'poste', 'telephone', 'adresse', 'date_embauche', 
                'salaire', 'numero_cnss', 'date_naissance', 'sexe', 
                'lieu_naissance', 'nationalite', 'situation_matrimoniale',
                'numero_cni', 'numero_passeport'
            ]);
            $table->renameColumn('nom', 'name');
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['utilisateur', 'admin','dg','chefprojet'])->after('email');
        });
    }
};