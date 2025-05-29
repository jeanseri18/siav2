<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('config_global', function (Blueprint $table) {
            $table->id();
            $table->string('entete');
            $table->string('numdepatfacture');
            $table->string('pieddepage');
            $table->string('logo');
            $table->unsignedBigInteger('id_bu');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('config_global');
    }
};
