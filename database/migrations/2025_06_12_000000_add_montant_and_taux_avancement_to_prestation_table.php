<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('prestation', function (Blueprint $table) {
            $table->decimal('montant', 15, 2)->nullable()->after('detail');
            $table->integer('taux_avancement')->nullable()->default(0)->after('montant');
        });
    }

    public function down() {
        Schema::table('prestation', function (Blueprint $table) {
            $table->dropColumn(['montant', 'taux_avancement']);
        });
    }
};