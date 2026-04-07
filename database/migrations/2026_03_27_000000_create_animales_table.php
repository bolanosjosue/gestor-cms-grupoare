<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animales', function (Blueprint $table) {
            $table->id();
            $table->string('agropecuaria');
            $table->string('codigo_practico');
            $table->string('estado');
            $table->string('identificacion_electronica')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('padre_nombre')->nullable();
            $table->string('codigo_madre')->nullable();
            $table->string('ultima_locacion')->nullable();
            $table->string('composicion_racial')->nullable();
            $table->string('clasificacion_asociacion')->nullable();
            $table->decimal('ultimo_peso', 10, 2)->nullable();
            $table->string('estandarizacion_produccion')->nullable();
            $table->date('fecha_ultimo_servicio')->nullable();
            $table->string('estado_reproductivo')->nullable();
            $table->integer('numero_revisiones')->nullable();
            $table->date('fecha_ultimo_parto')->nullable();
            $table->date('fecha_secado')->nullable();
            $table->string('nombre')->nullable();
            $table->string('sexo')->nullable();
            $table->string('codigo_reproductor')->nullable();
            $table->string('codigo')->nullable();
            $table->string('codigo_nombre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animales');
    }
};
