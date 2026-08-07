<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AnhTrainSv extends Model
{
    use HasFactory;

    protected $table = 'anh_train_svs';

    protected $fillable = [
        'sinh_vien_id',
        'hinh_anh',
        'face_id',
        'trang_thai',
    ];

    /**
     * Mối quan hệ: Mỗi ảnh train thuộc về một sinh viên
     */
    public function sinhVien()
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id', 'id');
    }

    protected $casts = [
        'face_id' => 'array',
    ];
    
    public function getHinhAnhUrlAttribute()
    {
        return Storage::disk('s3')->url($this->hinh_anh);
    }

    public function getFaceIdAttribute($value)
    {
        return $value ?? []; 
    }
}