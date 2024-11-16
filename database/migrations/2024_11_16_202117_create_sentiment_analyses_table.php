<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('sentiment_analyses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relacionamento com o usuário
        $table->string('text'); // Texto analisado
        $table->string('label'); // Sentimento (ex: "5 stars", "4 stars", etc.)
        $table->decimal('score', 8, 6); // Pontuação
        $table->timestamps(); // Data de criação e atualização
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiment_analyses');
    }
};
