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
        Schema::create('cantiere_scadenze', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantiere_id')->constrained('cantieri')->onDelete('cascade');
            $table->foreignId('attivita_id')->constrained('attivita');
            $table->date('data_scadenza');
            $table->date('data_completamento')->nullable();
            $table->boolean('inviato_alert')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['cantiere_id', 'data_scadenza']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantiere_scadenze');
    }
};

