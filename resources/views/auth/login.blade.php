@extends('layouts.main-layout')

@section('title', 'Đăng nhập Cán bộ Giảng viên')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 180px);">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
            <div class="card border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                {{-- Card Header Gradient --}}
                <div class="p-4 text-center text-white" style="background: var(--edu-gradient);">
                    <div class="mb-2">
                        <i class="fa-solid fa-graduation-cap" style="font-size: 38px;"></i>
                    </div>
                    <h4 class="fw-bold mb-1" style="font-size: 19px; letter-spacing: -0.01em;">CỔNG ĐIỂM DANH THI</h4>
                    <p class="mb-0 opacity-75 small">Đăng nhập dành cho Cán bộ & Giảng viên</p>
                </div>

                {{-- Card Body --}}
                <div class="card-body p-4 p-sm-5 bg-white">
                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                            <i class="fa-solid fa-circle-check"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Email công tác</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" value="{{ old('email') }}" name="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       placeholder="ví dụ: giangvien@school.edu.vn" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label fw-semibold small text-muted mb-0">Mật khẩu</label>
                                <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary">Quên mật khẩu?</a>
                            </div>
                            <div class="input-group mt-1">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="loginPassword"
                                       class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror"
                                       placeholder="••••••••" required>
                                <button type="button" class="input-group-text bg-light border-start-0 text-muted" onclick="togglePassVisibility()" tabindex="-1">
                                    <i class="fa-solid fa-eye" id="togglePassIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($errors->has('login_error'))
                            <div class="alert alert-danger py-2 small mb-3 text-center">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                {{ $errors->first('login_error') }}
                            </div>
                        @endif

                        <button id="loginBtn" type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-2 shadow-sm" style="border-radius: 10px;">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
                            <span id="btnText">Đăng Nhập</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span> Đang xác thực...
                            </span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top text-muted small">
                        <i class="fa-solid fa-shield-halved me-1 text-success"></i> Hệ thống xác thực bảo mật nội bộ
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassVisibility() {
    const input = document.getElementById('loginPassword');
    const icon = document.getElementById('togglePassIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
