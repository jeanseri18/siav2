<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bu_budget_excel_rows', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('sheet');
            $table->string('parametre')->nullable()->after('reference');
        });

        DB::table('bu_budget_excel_rows')
            ->whereNull('parametre')
            ->update([
                'parametre' => DB::raw('label'),
            ]);
    }

    public function down(): void
    {
        Schema::table('bu_budget_excel_rows', function (Blueprint $table) {
            $table->dropColumn(['reference', 'parametre']);
        });
    }
};
