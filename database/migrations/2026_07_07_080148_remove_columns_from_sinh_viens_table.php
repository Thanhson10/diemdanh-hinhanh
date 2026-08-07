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
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->dropColumn(['da_train_khuon_mat', 'hinh_anh', 'face_ids']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_viens', function (Blueprint $table) {
            Schema::table('sinh_viens', function (Blueprint $table) {
            $table->boolean('da_train_khuon_mat')->default(false);
            $table->string('hinh_anh')->nullable();
            $table->json('face_ids')->nullable(); 
        });
        });
    }
};
