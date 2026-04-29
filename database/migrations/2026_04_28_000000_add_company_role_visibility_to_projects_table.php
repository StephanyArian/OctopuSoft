<?php
// database/migrations/2026_04_28_000002_add_company_to_projects_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // SOLO agregar company (los demás campos ya existen)
            $table->string('company', 255)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('company');
        });
    }
};