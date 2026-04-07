<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones_animales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('session_uuid')->unique();
            $table->string('nombre_archivo')->nullable();
            $table->unsignedInteger('total_registros')->default(0);
            $table->unsignedInteger('insertados')->default(0);
            $table->unsignedInteger('actualizados')->default(0);
            $table->unsignedInteger('con_error')->default(0);
            $table->unsignedSmallInteger('lotes_total')->default(1);
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones_animales');
    }
};
