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
        Schema::create('cantieri', function (Blueprint $table) {
            $table->id();
            $table->string('codice')->unique(); // Es: CANT-2024-001
            $table->string('nome');
            $table->text('indirizzo')->nullable();
            $table->string('committente');
            $table->date('data_inizio')->nullable();
            $table->date('data_fine_prevista')->nullable();
            $table->decimal('importo_lavori', 12, 2)->nullable();
            $table->enum('stato', ['pianificazione', 'apertura', 'attivo', 'sospeso', 'completato', 'chiuso'])->default('pianificazione');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantieri');
    }
};


