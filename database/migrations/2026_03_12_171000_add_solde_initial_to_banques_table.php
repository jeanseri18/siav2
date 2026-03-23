<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->decimal('solde_initial', 15, 2)->default(0)->after('bu_id');
        });
    }

    public function down(): void
    {
        Schema::table('banques', function (Blueprint $table) {
            $table->dropColumn('solde_initial');
        });
    }
};
