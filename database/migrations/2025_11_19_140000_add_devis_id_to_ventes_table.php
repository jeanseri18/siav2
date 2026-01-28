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
        Schema::table('ventes', function (Blueprint $table) {
            // Only add devis_id since other columns already exist
            if (!Schema::hasColumn('ventes', 'devis_id')) {
                $table->unsignedBigInteger('devis_id')->nullable()->after('client_id');
                
                // Add foreign key constraint for devis_id
                $table->foreign('devis_id')->references('id')->on('devis')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (Schema::hasColumn('ventes', 'devis_id')) {
                $table->dropForeign(['devis_id']);
                $table->dropColumn('devis_id');
            }
        });
    }
};