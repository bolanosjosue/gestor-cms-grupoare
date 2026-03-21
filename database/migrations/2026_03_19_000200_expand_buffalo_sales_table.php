<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buffalo_sales', function (Blueprint $table) {
            // Sección 1: Identificación
            $table->string('code', 30)->unique()->after('id');

            // Make previously-required fields nullable
            $table->string('animal_name', 255)->nullable()->change();
            $table->string('father', 150)->nullable()->change();
            $table->foreignId('breed_id')->nullable()->change();
            $table->unsignedTinyInteger('age_years')->nullable()->change();
            $table->decimal('weight_kg', 8, 2)->nullable()->change();
            $table->string('phone', 15)->change(); // +506XXXXXXXX

            // Sección 2: Datos del animal
            $table->string('sex', 10)->default('female')->after('breed_id'); // female | male

            // Sección 3: Genética
            $table->string('mother', 150)->nullable()->after('father');
            $table->text('genetic_notes')->nullable()->after('mother');

            // Sección 4: Reproducción
            $table->string('reproductive_status', 20)->default('empty')->after('genetic_notes'); // empty | pregnant | producing
            $table->unsignedTinyInteger('gestation_months')->nullable()->after('reproductive_status');
            $table->unsignedTinyInteger('births_count')->nullable()->after('gestation_months');
            $table->decimal('milk_production', 6, 2)->nullable()->after('births_count'); // litros/día

            // Sección 5: Salud
            $table->boolean('vaccines_up_to_date')->default(false)->after('milk_production');
            $table->string('feeding_type', 20)->default('grazing')->after('vaccines_up_to_date'); // grazing | supplement | mixed
            $table->string('animal_condition', 20)->default('good')->after('feeding_type'); // excellent | good | regular

            // Sección 6: Ubicación
            $table->string('farm_name', 150)->nullable()->after('animal_condition');
            $table->string('location', 200)->nullable()->after('farm_name');

            // Sección 7: Contacto
            $table->string('seller_name', 150)->nullable()->after('phone');

            // Sección 8: Multimedia – galería adicional
            $table->json('gallery')->nullable()->after('photo_path');

            // Sección 9: Descripción
            $table->text('description')->nullable()->after('gallery');
        });
    }

    public function down(): void
    {
        Schema::table('buffalo_sales', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'sex',
                'mother',
                'genetic_notes',
                'reproductive_status',
                'gestation_months',
                'births_count',
                'milk_production',
                'vaccines_up_to_date',
                'feeding_type',
                'animal_condition',
                'farm_name',
                'location',
                'seller_name',
                'gallery',
                'description',
            ]);
        });
    }
};
