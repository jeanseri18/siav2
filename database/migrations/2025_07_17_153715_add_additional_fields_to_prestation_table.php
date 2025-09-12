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
        Schema::table('prestation', function (Blueprint $table) {
            $table->date('date_affectation')->nullable()->after('id_artisan');
            $table->text('motif_remplacement')->nullable()->after('date_affectation');
            $table->timestamp('date_remplacement')->nullable()->after('motif_remplacement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestation', function (Blueprint $table) {
            $table->dropColumn(['date_affectation', 'motif_remplacement', 'date_remplacement']);
        });
    }
};
