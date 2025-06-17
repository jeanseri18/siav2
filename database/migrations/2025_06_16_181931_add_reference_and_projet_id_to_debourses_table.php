<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('debourses', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('id');
            $table->foreignId('projet_id')->nullable()->constrained('projets')->onDelete('set null')->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debourses', function (Blueprint $table) {
            $table->dropForeign(['projet_id']);
            $table->dropColumn(['reference', 'projet_id']);
        });
    }
};
