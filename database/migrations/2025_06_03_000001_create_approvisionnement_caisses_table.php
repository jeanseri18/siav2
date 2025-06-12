<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approvisionnement_caisses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('bus')->onDelete('cascade');
            $table->decimal('montant', 15, 2);
            $table->string('motif');
            $table->enum('mode_paiement', ['cheque', 'espece']);
            $table->date('date_appro');
            $table->foreignId('banque_id')->nullable()->constrained('banques')->onDelete('set null');
            $table->string('reference_cheque')->nullable();
            $table->string('origine_fonds')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvisionnement_caisses');
    }
};