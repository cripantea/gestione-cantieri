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
        Schema::create('documenti_cantiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantiere_id')->constrained('cantieri')->onDelete('cascade');
            $table->foreignId('attivita_id')->nullable()->constrained('attivita'); // Collegamento opzionale
            $table->string('nome_file');
            $table->string('path');
            $table->string('tipo'); // POS, DURC, certificato, etc
            $table->date('data_scadenza')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documenti_cantiere');
    }
};

