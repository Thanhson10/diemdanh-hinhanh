@extends('layouts.main-layout')

@section('content')
<div class="container-fluid px-3 py-2">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="{{ route('sinhvien.index', request()->query()) }}" class="btn btn-sm btn-edu btn-edu-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>

            <!-- Form Card -->
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Cập Nhật Thông Tin Sinh Viên</h4>
                            <p class="text-muted small mb-0">Chỉnh sửa hồ sơ sinh viên: <strong class="text-primary">{{ $sinhvien->ho_ten }} ({{ $sinhvien->ma_sv }})</strong></p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('sinhvien.update', $sinhvien->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Mã sinh viên (MSSV) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="ma_sv" value="{{ old('ma_sv', $sinhvien->ma_sv) }}" 
                                        class="form-control @error('ma_sv') is-invalid @enderror" required>
                                </div>
                                @error('ma_sv')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Họ và tên sinh viên <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text" name="ho_ten" value="{{ old('ho_ten', $sinhvien->ho_ten) }}" 
                                        class="form-control @error('ho_ten') is-invalid @enderror" required>
                                </div>
                                @error('ho_ten')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Lớp sinh hoạt <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-graduation-cap"></i></span>
                                    <input type="text" name="lop" value="{{ old('lop', $sinhvien->lop) }}" 
                                        class="form-control @error('lop') is-invalid @enderror" required>
                                </div>
                                @error('lop')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Địa chỉ Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" value="{{ old('email', $sinhvien->email) }}" 
                                        class="form-control @error('email') is-invalid @enderror" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('sinhvien.index', request()->query()) }}" class="btn btn-edu btn-edu-secondary">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-edu btn-edu-primary px-4">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

