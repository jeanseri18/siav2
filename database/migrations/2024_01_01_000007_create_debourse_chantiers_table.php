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
        Schema::create('debourse_chantiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('rubrique_id')->nullable();
            $table->string('designation');
            $table->string('unite')->nullable();
            $table->decimal('quantite', 10, 2)->default(0);
            $table->decimal('pu_ht', 15, 2)->default(0);
            $table->decimal('montant_ht', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('debourse_chantier_parents')->onDelete('cascade');
            $table->foreign('rubrique_id')->references('id')->on('rubriques')->onDelete('set null');
            $table->index(['parent_id', 'rubrique_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debourse_chantiers');
    }
};