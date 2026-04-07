<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('palpaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animales')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('cc', 3, 2);           // condición corporal 1.0–5.0
            $table->string('od', 10);               // ovario derecho
            $table->string('oi', 10);               // ovario izquierdo
            $table->string('ut', 10);               // útero
            $table->string('diagnostico', 10);      // diagnóstico
            $table->string('observacion', 500)->nullable();
            $table->timestamps();

            $table->index(['animal_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('palpaciones');
    }
};
