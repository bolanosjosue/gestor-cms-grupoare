<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buffalo_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breed_id')->constrained('buffalo_breeds')->restrictOnDelete();
            $table->string('animal_name');
            $table->unsignedTinyInteger('age_years');
            $table->decimal('weight_kg', 8, 2);
            $table->string('father');
            $table->string('status')->default('available'); // available | reserved | sold
            $table->unsignedBigInteger('price_crc');
            $table->string('phone', 13);
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buffalo_sales');
    }
};
