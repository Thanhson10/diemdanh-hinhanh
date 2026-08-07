<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lich_this', function (Blueprint $table) {
             $table->integer('thoi_luong_thi')->default(60)->after('gio_thi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_this', function (Blueprint $table) {
            $table->dropColumn('thoi_luong_thi');
        });
    }
};
