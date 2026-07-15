<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brouillard_caisses', function (Blueprint $table) {
            if (! Schema::hasColumn('brouillard_caisses', 'date_operation')) {
                $table->date('date_operation')->nullable()->after('motif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('brouillard_caisses', function (Blueprint $table) {
            if (Schema::hasColumn('brouillard_caisses', 'date_operation')) {
                $table->dropColumn('date_operation');
            }
        });
    }
};
