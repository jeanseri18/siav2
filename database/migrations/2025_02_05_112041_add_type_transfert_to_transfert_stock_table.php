<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('transfert_stock', function (Blueprint $table) {
            $table->enum('type_transfert', ['normal', 'retour_hive'])->default('normal')->after('quantite');
        });
    }

    public function down() {
        Schema::table('transfert_stock', function (Blueprint $table) {
            $table->dropColumn('type_transfert');
        });
    }
};