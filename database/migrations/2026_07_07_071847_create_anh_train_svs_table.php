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
        Schema::create('anh_train_svs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_viens')->onDelete('restrict');
            $table->string('hinh_anh')->nullable();
            $table->text('face_id')->nullable();
            $table->enum('trang_thai', ['pending', 'trained', 'failed'])
            ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anh_train_svs');
    }
};
