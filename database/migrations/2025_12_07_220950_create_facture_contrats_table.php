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
        Schema::create('facture_contrats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dqe_id');
            $table->decimal('montant_a_payer', 15, 2);
            $table->decimal('montant_verse', 15, 2)->default(0);
            $table->timestamps();
            
            $table->foreign('dqe_id')->references('id')->on('dqes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_contrats');
    }
};
