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
        Schema::create('fasi_procedurali', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Es: "Apertura Nuovo Cantiere"
            $table->text('descrizione')->nullable();
            $table->string('icona')->nullable(); // Per UI
            $table->enum('tipologia', ['apertura', 'ricorrente', 'formazione', 'ordinaria']); // Tipologia fase
            $table->integer('ordine')->default(0); // Per ordinamento
            $table->boolean('is_attiva')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasi_procedurali');
    }
};

