@extends('layouts.main-layout')

@section('content')
<div class="container-fluid px-3 py-2">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="{{ route('monhoc.index') }}" class="btn btn-sm btn-edu btn-edu-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách môn học
                </a>
            </div>

            <!-- Form Card -->
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Chỉnh Sửa Môn Học</h4>
                            <p class="text-muted small mb-0">Cập nhật thông tin học phần: <strong class="text-primary">{{ $monHoc->ten_mon }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('monhoc.update', $monHoc->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">
                                Mã môn học <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-barcode"></i></span>
                                <input type="text" name="ma_mon" class="form-control @error('ma_mon') is-invalid @enderror"
                                    value="{{ old('ma_mon', $monHoc->ma_mon) }}" required>
                            </div>
                            @error('ma_mon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">
                                Tên môn học <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-book-open"></i></span>
                                <input type="text" name="ten_mon" class="form-control @error('ten_mon') is-invalid @enderror"
                                    value="{{ old('ten_mon', $monHoc->ten_mon) }}" required>
                            </div>
                            @error('ten_mon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('monhoc.index') }}" class="btn btn-edu btn-edu-secondary">
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

