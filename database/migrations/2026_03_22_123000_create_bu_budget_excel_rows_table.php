<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bu_budget_excel_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bu_budget_excel_id')->constrained('bu_budget_excels')->cascadeOnDelete();
            $table->string('sheet', 50);
            $table->string('label');
            $table->decimal('amount_decimal', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['bu_budget_excel_id', 'sheet']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bu_budget_excel_rows');
    }
};
