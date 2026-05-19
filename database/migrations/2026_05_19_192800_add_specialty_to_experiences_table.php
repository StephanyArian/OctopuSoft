<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('specialty')->nullable()->after('title');
            // 'specialty' en inglés para mantener consistencia
        });
    }

    public function down()
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('specialty');
        });
    }

};
