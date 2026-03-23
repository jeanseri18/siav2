<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bu_budget_excel_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bu_budget_excel_id')->constrained('bu_budget_excels')->cascadeOnDelete();
            $table->string('sheet', 50);
            $table->string('key', 100);
            $table->decimal('value_decimal', 15, 2)->nullable();
            $table->text('value_text')->nullable();
            $table->timestamps();

            $table->unique(['bu_budget_excel_id', 'sheet', 'key']);
            $table->index(['bu_budget_excel_id', 'sheet']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bu_budget_excel_values');
    }
};
