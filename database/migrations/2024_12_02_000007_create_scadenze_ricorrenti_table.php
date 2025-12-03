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
        Schema::create('scadenze_ricorrenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attivita_id')->constrained('attivita')->onDelete('cascade');
            $table->enum('frequenza', ['annuale', 'biennale', 'mensile', 'trimestrale', 'custom']);
            $table->integer('intervallo_giorni')->nullable(); // Per frequenza custom
            $table->integer('giorni_preavviso')->default(30); // Alert prima scadenza
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scadenze_ricorrenti');
    }
};

