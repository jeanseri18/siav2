<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('corps_de_metiers', function (Blueprint $table) {
            // Ajouter la référence au corps de métier de la table corp_metiers
            $table->unsignedBigInteger('id_corpmetier')->nullable()->after('id_localisation');
            $table->foreign('id_corpmetier')->references('id')->on('corp_metiers')->onDelete('set null');
            
            // Rendre nom_corpsdemetier nullable car on va utiliser la référence
            $table->string('nom_corpsdemetier')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('corps_de_metiers', function (Blueprint $table) {
            $table->dropForeign(['id_corpmetier']);
            $table->dropColumn('id_corpmetier');
        });
    }
};
