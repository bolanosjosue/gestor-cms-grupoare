<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesajes_leche', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animales')->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('peso_am', 8, 2)->nullable();
            $table->decimal('peso_pm', 8, 2)->nullable();
            $table->string('observacion', 500)->nullable();
            $table->timestamps();

            $table->index(['animal_id', 'fecha']);
            $table->unique(['animal_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesajes_leche');
    }
};
