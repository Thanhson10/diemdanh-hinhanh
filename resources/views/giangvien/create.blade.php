@extends('layouts.main-layout')

@section('content')
<div class="container-fluid px-3 py-2">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="{{ route('giangvien.index') }}" class="btn btn-sm btn-edu btn-edu-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách giảng viên
                </a>
            </div>

            <!-- Form Card -->
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Thêm Giảng Viên Mới</h4>
                            <p class="text-muted small mb-0">Cấp tài khoản cán bộ coi thi hoặc quản trị viên đào tạo</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('giangvien.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Mã giảng viên <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" name="ma_gv" value="{{ old('ma_gv') }}" 
                                        class="form-control @error('ma_gv') is-invalid @enderror" 
                                        placeholder="Ví dụ: GV001" required>
                                </div>
                                @error('ma_gv')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Họ và tên giảng viên <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-user-tie"></i></span>
                                    <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" 
                                        class="form-control @error('ho_ten') is-invalid @enderror" 
                                        placeholder="Ví dụ: ThS. Trần Văn Hùng" required>
                                </div>
                                @error('ho_ten')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Địa chỉ Email <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" value="{{ old('email') }}" 
                                        class="form-control @error('email') is-invalid @enderror" 
                                        placeholder="giangvien@school.edu.vn" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Mật khẩu đăng nhập <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        placeholder="Tối thiểu 6 ký tự" required>
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">
                                Vai trò & Phân quyền <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-shield-alt"></i></span>
                                <select name="vai_tro" class="form-select @error('vai_tro') is-invalid @enderror" required>
                                    <option value="giang_vien" {{ old('vai_tro') == 'giang_vien' ? 'selected' : '' }}>
                                        Cán bộ coi thi / Giảng viên thường
                                    </option>
                                    <option value="admin" {{ old('vai_tro') == 'admin' ? 'selected' : '' }}>
                                        Quản trị viên hệ thống (Admin)
                                    </option>
                                </select>
                            </div>
                            @error('vai_tro')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('giangvien.index') }}" class="btn btn-edu btn-edu-secondary">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-edu btn-edu-primary px-4">
                                <i class="fas fa-user-check me-1"></i> Lưu thông tin cán bộ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

