<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement("ALTER TABLE skills MODIFY COLUMN type ENUM('technical', 'soft', 'language')");
    }
    
    public function down(): void
    {
        \DB::statement("ALTER TABLE skills MODIFY COLUMN type ENUM('technical', 'soft')");
    }
};
