<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ Thống Điểm Danh Thi - Đại Học</title>

    <!-- Google Fonts: Plus Jakarta Sans & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- School Theme Design System -->
    <link rel="stylesheet" href="{{ asset('css/school-theme.css') }}">
    
    <style>
        body {
            background-color: var(--bg-main, #f8fafc);
            font-family: 'Be Vietnam Pro', sans-serif;
            min-height: 100vh;
        }

        .camera-header-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--primary, #1e40af);
            font-size: 1.15rem;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Top Minimalist Exam Header -->
    <header class="bg-white border-bottom sticky-top shadow-sm py-2 px-3">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home.index') }}" class="camera-header-brand d-flex align-items-center gap-2">
                    <span class="rounded-3 bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 16px;">
                        <i class="fas fa-graduation-cap"></i>
                    </span>
                    <span>HỆ THỐNG COI THI & ĐIỂM DANH</span>
                </a>
                <span class="badge-edu badge-edu-info ms-2 d-none d-sm-inline-flex">
                    <i class="fas fa-video me-1"></i>AI Face Recognition
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(Auth::guard('giangvien')->check())
                    <span class="text-muted small d-none d-md-inline">
                        Cán bộ: <strong class="text-dark">{{ Auth::guard('giangvien')->user()->ho_ten }}</strong>
                    </span>
                    <a href="{{ route('home.index') }}" class="btn btn-sm btn-edu btn-edu-secondary">
                        <i class="fas fa-home me-1"></i> Trang chủ
                    </a>
                @endif
            </div>
        </div>
    </header>

    <div class="container-fluid py-3 px-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Tìm kiếm...",
                allowClear: true
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    @yield('scripts')
</body>
</html>

