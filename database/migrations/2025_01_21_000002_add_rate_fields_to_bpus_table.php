<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateFieldsToBpusTable extends Migration
{
    public function up()
    {
        Schema::table('bpus', function (Blueprint $table) {
            $table->decimal('taux_mo', 5, 2)->default(0)->after('materiaux'); // Taux main d'œuvre (%)
            $table->decimal('taux_mat', 5, 2)->default(0)->after('main_oeuvre'); // Taux matériel (%)
            $table->decimal('taux_fc', 5, 2)->default(0)->after('debourse_sec'); // Taux frais chantier (%)
            $table->decimal('taux_fg', 5, 2)->default(0)->after('frais_chantier'); // Taux frais généraux (%)
            $table->decimal('taux_benefice', 5, 2)->default(0)->after('frais_general'); // Taux bénéfice (%)
        });
    }

    public function down()
    {
        Schema::table('bpus', function (Blueprint $table) {
            $table->dropColumn(['taux_mo', 'taux_mat', 'taux_fc', 'taux_fg', 'taux_benefice']);
        });
    }
}