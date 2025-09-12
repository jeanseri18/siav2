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
        Schema::table('artisan', function (Blueprint $table) {
            $table->enum('civilite', ['Monsieur', 'Madame', 'Mademoiselle'])->after('nom');
            $table->string('prenoms')->nullable()->after('civilite');
            $table->enum('type_piece', ['CNI', 'Passeport', 'Permis'])->after('prenoms');
            $table->string('numero_piece')->after('type_piece');
            $table->date('date_naissance')->nullable()->after('numero_piece');
            $table->string('nationalite')->nullable()->after('date_naissance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisan', function (Blueprint $table) {
            $table->dropColumn([
                'civilite',
                'prenoms',
                'type_piece',
                'numero_piece',
                'date_naissance',
                'nationalite'
            ]);
        });
    }
};
