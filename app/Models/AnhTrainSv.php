<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AnhTrainSv extends Model
{
    use HasFactory;

    protected $table = 'anh_train_svs';

    protected $fillable = [
        'sinh_vien_id',
        'hinh_anh',
        'face_id',
        'file_hash',
        'file_name',
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

    public static function cleanupFailed()
    {
        return self::where('trang_thai', 'failed')
            ->where('created_at', '<', now()->subDays(3)) //->subMinutes(1))
            ->delete();
    }

    public static function cleanupFailedSafe()
    {
        if (Cache::has('cleanup_failed_ran')) {
            return;
        }

        self::cleanupFailed(); 

        // set 30 phút mới chạy lại
        Cache::put('cleanup_failed_ran', true, now()->addMinutes(30));
    }
}