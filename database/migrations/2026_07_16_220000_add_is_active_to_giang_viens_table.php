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
        Schema::table('giang_viens', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('vai_tro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giang_viens', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
