<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buffalo_sales', function (Blueprint $table) {
            // Replace father/mother text with breed FK references
            $table->unsignedBigInteger('father_breed_id')->nullable()->after('breed_id');
            $table->unsignedBigInteger('mother_breed_id')->nullable()->after('father_breed_id');

            $table->foreign('father_breed_id')->references('id')->on('buffalo_breeds')->nullOnDelete();
            $table->foreign('mother_breed_id')->references('id')->on('buffalo_breeds')->nullOnDelete();

            // Drop removed columns
            $table->dropColumn([
                'animal_name',
                'father',
                'mother',
                'genetic_notes',
                'farm_name',
                'location',
                'seller_name',
                'gallery',
                'description',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('buffalo_sales', function (Blueprint $table) {
            $table->dropForeign(['father_breed_id']);
            $table->dropForeign(['mother_breed_id']);
            $table->dropColumn(['father_breed_id', 'mother_breed_id']);

            $table->string('animal_name')->nullable()->after('code');
            $table->string('father', 150)->nullable();
            $table->string('mother', 150)->nullable();
            $table->text('genetic_notes')->nullable();
            $table->string('farm_name', 150)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('seller_name', 150)->nullable();
            $table->json('gallery')->nullable();
            $table->text('description')->nullable();
        });
    }
};
