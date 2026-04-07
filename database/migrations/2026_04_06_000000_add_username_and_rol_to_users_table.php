<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->default('')->after('name');
            $table->string('rol')->nullable()->after('is_super_admin'); // encargado, veterinario, supervisor
            $table->string('email')->nullable()->change();
        });

        // Populate username from name for existing users
        \App\Models\User::all()->each(function ($user) {
            $base = \Illuminate\Support\Str::slug($user->name, '_');
            $username = $base;
            $i = 1;
            while (\App\Models\User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $base . '_' . $i++;
            }
            $user->update(['username' => $username]);
        });

        // Now add unique index
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'rol']);
            $table->string('email')->nullable(false)->change();
        });
    }
};
