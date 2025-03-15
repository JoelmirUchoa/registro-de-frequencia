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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // 'brother' ou 'visitor'
            $table->unsignedBigInteger('user_id'); // Relacionamento com 'brothers' ou 'visitors'
            $table->string('name'); // Nome do usuário
            $table->string('loja')->nullable(); // Nome ou número da loja
            $table->timestamp('date')->nullable(); // Data da presença
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
