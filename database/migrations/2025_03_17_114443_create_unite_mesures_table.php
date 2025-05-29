<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unite_mesures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('ref');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unite_mesures');
    }
};

