<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('corp_metiers', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique(); // Nom unique pour chaque corps de métier
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('corp_metiers');
    }
};
