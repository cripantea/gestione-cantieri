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
        Schema::create('cantiere_attivita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cantiere_id')->constrained('cantieri')->onDelete('cascade');
            $table->foreignId('attivita_id')->constrained('attivita')->onDelete('cascade');
            $table->enum('stato', ['da_fare', 'in_corso', 'completata', 'non_applicabile'])->default('da_fare');
            $table->date('data_scadenza')->nullable();
            $table->date('data_completamento')->nullable();
            $table->foreignId('completata_da_user_id')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['cantiere_id', 'attivita_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantiere_attivita');
    }
};

