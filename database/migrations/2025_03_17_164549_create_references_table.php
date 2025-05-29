<?php
// database/migrations/XXXX_XX_XX_create_references_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('ref')->unique();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('references');
    }
};
