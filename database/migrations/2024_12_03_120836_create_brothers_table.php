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
        Schema::create('brothers', function (Blueprint $table) {
            $table->id();
            $table->string('sim')->unique(); // Número CIM único
            $table->string('name');          // Nome do irmão
            $table->string('position');      // Cargo do irmão
            $table->timestamps();            // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brothers');
    }
};
