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
        Schema::create('attivita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fase_procedurale_id')->constrained('fasi_procedurali')->onDelete('cascade');
            $table->string('titolo'); // Es: "EdilConnect - Verifica Congruità"
            $table->text('descrizione')->nullable();
            $table->boolean('is_critica')->default(false); // Flag per attività critiche
            $table->string('url_portale')->nullable(); // Link esterni
            $table->text('credenziali_note')->nullable(); // Hint per credenziali
            $table->integer('ordine')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attivita');
    }
};

