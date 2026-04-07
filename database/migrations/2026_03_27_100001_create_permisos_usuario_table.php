<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('modulo'); // dashboard, articulos, personal, razas, ventas, animales
            $table->boolean('puede_ver')->default(false);
            $table->boolean('puede_agregar')->default(false);
            $table->boolean('puede_editar')->default(false);
            $table->boolean('puede_eliminar')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_usuario');
    }
};
