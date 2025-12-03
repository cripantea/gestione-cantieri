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
        Schema::create('passi_attivita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attivita_id')->constrained('attivita')->onDelete('cascade');
            $table->integer('numero_passo');
            $table->text('descrizione');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passi_attivita');
    }
};

