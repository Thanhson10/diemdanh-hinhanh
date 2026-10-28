<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Hệ thống Điểm danh Thi Thông minh')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5.3 & Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- School Theme Design System -->
    <link rel="stylesheet" href="{{ asset('css/school-theme.css') }}">

    <!-- Additional CSS từ các views con -->
    @stack('styles')
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="logo">
            <a href="{{ route('home.index') }}" class="d-flex flex-column align-items-center text-decoration-none">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo Trường học" onerror="this.style.display='none'">
                <h1>Hệ Thống Điểm Danh</h1>
                <span class="logo-subtitle"><i class="fa-solid fa-graduation-cap me-1"></i> Cổng Khảo Thí & Coi Thi</span>
            </a>
        </div>

        <div class="menu-container">
            <div class="menu-section-title">Danh mục chính</div>
            <a href="{{ route('home.index') }}" class="menu-item {{ Request::routeIs('home.index') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Màn hình chính</span>
            </a>
            <a href="{{ route('lichthi.index') }}" class="menu-item {{ Request::routeIs('lichthi.*') && !Request::routeIs('lichthi.phancong*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Lịch thi toàn trường</span>
            </a>
            <a href="{{ route('diemdanh.index') }}" class="menu-item {{ Request::routeIs('diemdanh.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>Phòng thi cá nhân</span>
            </a>

            @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
            <div class="menu-section-title">Quản trị đào tạo</div>
            <a href="{{ route('sinhvien.index') }}" class="menu-item {{ Request::routeIs('sinhvien.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-graduate"></i>
                <span>Quản lý sinh viên</span>
            </a>
            <a href="{{ route('giangvien.index') }}" class="menu-item {{ Request::routeIs('giangvien.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Quản lý giảng viên</span>
            </a>
            <a href="{{ route('monhoc.index') }}" class="menu-item {{ Request::routeIs('monhoc.*') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open"></i>
                <span>Quản lý môn học</span>
            </a>
            <a href="{{ route('rekognition.uploadForm') }}" class="menu-item {{ Request::routeIs('rekognition.uploadForm') ? 'active' : '' }}">
                <i class="fa-solid fa-camera-rotate"></i>
                <span>Huấn luyện khuôn mặt</span>
            </a>
            @endif

            <div class="menu-section-title">Cá nhân</div>
            <a href="{{ route('auth.profile') }}" class="menu-item {{ Request::routeIs('auth.profile') || Request::routeIs('auth.changePassword') ? 'active' : '' }}">
                <i class="fa-solid fa-id-card"></i>
                <span>Thông tin tài khoản</span>
            </a>
        </div>

        @if(Auth::guard('giangvien')->check())
        @php
            $currentUser = Auth::guard('giangvien')->user();
            $shortName = mb_substr($currentUser->ho_ten ?? 'GV', 0, 1, 'UTF-8');
        @endphp
        <div class="sidebar-footer">
            <div class="user-avatar-badge">{{ $shortName }}</div>
            <div class="user-info-text">
                <div class="user-info-name" title="{{ $currentUser->ho_ten }}">{{ $currentUser->ho_ten }}</div>
                <span class="user-info-role">
                    {{ $currentUser->vai_tro === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}
                </span>
            </div>
        </div>
        @endif
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" id="hamburger" aria-label="Mở Menu" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumb-edu d-none d-sm-flex align-items-center">
                    <i class="fa-solid fa-school text-primary me-1"></i>
                    <span>Trường học</span>
                    <i class="fa-solid fa-chevron-right text-muted mx-2" style="font-size: 10px;"></i>
                    <span class="breadcrumb-main">@yield('title', 'Điểm danh phòng thi')</span>
                </div>
            </div>

            <div class="header-right">
                @if(Auth::guard('giangvien')->check())
                    <div class="d-none d-md-flex align-items-center me-2 text-muted" style="font-size: 13px;">
                        <span>Xin chào, <strong class="text-dark">{{ Auth::guard('giangvien')->user()->ho_ten }}</strong></span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-header-action btn-logout">
                            <i class="fas fa-arrow-right-from-bracket"></i>
                            <span class="d-none d-sm-inline">Đăng xuất</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-header-action text-primary border-primary">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        <span>Đăng nhập</span>
                    </a>
                @endif
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.getElementById('hamburger');
            const overlay = document.getElementById('sidebarOverlay');

            if (hamburger && sidebar && overlay) {
                hamburger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // Tự động khởi tạo Select2 nếu có
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    placeholder: "Tìm kiếm...",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>