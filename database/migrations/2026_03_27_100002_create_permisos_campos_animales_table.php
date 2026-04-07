<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_campos_animales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('campo');
            $table->boolean('puede_editar')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'campo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_campos_animales');
    }
};
