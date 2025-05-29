<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('artisan', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('id_corpmetier')->constrained('corp_metiers')->onDelete('cascade'); 
            $table->enum('type', ['artisan', 'travailleur']);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('artisan');
    }
};
