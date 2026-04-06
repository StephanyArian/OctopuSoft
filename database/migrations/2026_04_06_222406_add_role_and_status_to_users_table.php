<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se agregan después del campo password
            $table->enum('role', ['user', 'admin'])
                  ->default('user')
                  ->after('password');

            $table->enum('status', ['active', 'inactive', 'pending'])
                  ->default('pending')
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status']);
        });
    }
};