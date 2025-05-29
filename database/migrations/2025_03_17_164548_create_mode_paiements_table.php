<?php
// database/migrations/XXXX_XX_XX_create_mode_paiements_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('mode_paiements', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('mode_paiements');
    }
};
