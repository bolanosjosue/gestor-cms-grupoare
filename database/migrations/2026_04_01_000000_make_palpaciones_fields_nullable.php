<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('palpaciones', function (Blueprint $table) {
            $table->decimal('cc', 3, 2)->nullable()->change();
            $table->string('od', 10)->nullable()->change();
            $table->string('oi', 10)->nullable()->change();
            $table->string('ut', 10)->nullable()->change();
            $table->string('diagnostico', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('palpaciones', function (Blueprint $table) {
            $table->decimal('cc', 3, 2)->nullable(false)->change();
            $table->string('od', 10)->nullable(false)->change();
            $table->string('oi', 10)->nullable(false)->change();
            $table->string('ut', 10)->nullable(false)->change();
            $table->string('diagnostico', 10)->nullable(false)->change();
        });
    }
};
