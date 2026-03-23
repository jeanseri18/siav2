<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->foreignId('bu_id')->nullable()->after('id')->constrained('bus')->nullOnDelete();
        });

        Schema::table('banques', function (Blueprint $table) {
            $table->dropUnique(['nom']);
            $table->unique(['bu_id', 'nom']);
        });
    }

    public function down(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->dropUnique(['bu_id', 'nom']);
            $table->unique(['nom']);
        });

        Schema::table('banques', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bu_id');
        });
    }
};
