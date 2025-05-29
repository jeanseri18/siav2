<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('regime_impositions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('ref');
            $table->string('tva');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regime_impositions');
    }
};

