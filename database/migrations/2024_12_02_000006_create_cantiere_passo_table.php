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
        Schema::create('cantiere_passo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantiere_attivita_id')->constrained('cantiere_attivita')->onDelete('cascade');
            $table->foreignId('passo_attivita_id')->constrained('passi_attivita')->onDelete('cascade');
            $table->boolean('completato')->default(false);
            $table->timestamp('completato_at')->nullable();
            $table->foreignId('completato_da_user_id')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantiere_passo');
    }
};

