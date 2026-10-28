@extends('layouts.main-layout')

@section('title', 'Trang chủ - Phòng thi đang diễn ra')

@section('content')
    {{-- Flash Notifications --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Hero Welcome Banner --}}
    <div class="hero-banner">
        <div>
            @if(Auth::guard('giangvien')->check())
                <h2>Xin chào, Thầy/Cô {{ Auth::guard('giangvien')->user()->ho_ten }} 👋</h2>
                <p>Chúc Thầy/Cô một ca coi thi làm việc thuận lợi, chính xác và hiệu quả!</p>
            @else
                <h2>Cổng Thông Tin Điểm Danh Thi Thông Minh 🎓</h2>
                <p>Hệ thống giám sát và điểm danh phòng thi bằng công nghệ nhận diện khuôn mặt AI.</p>
            @endif
        </div>
        <div class="text-md-end text-white">
            <div class="badge bg-white text-primary px-3 py-2 fw-semibold" style="font-size: 13px; border-radius: 20px;">
                <i class="fa-regular fa-calendar-check me-1"></i>
                Hôm nay: {{ now()->translatedFormat('l, d/m/Y') }}
            </div>
            @guest('giangvien')
                <div class="mt-2">
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm fw-bold px-3 py-1 text-primary">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Đăng nhập ngay
                    </a>
                </div>
            @endguest
        </div>
    </div>

    {{-- Header Section: Title & Search --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: var(--edu-text); font-size: 20px;">
                <span class="pulse-dot"></span>
                <span>Phòng thi đang diễn ra</span>
                @if(isset($lichThis) && $lichThis->count() > 0)
                    <span class="badge bg-danger rounded-pill fs-7">{{ $lichThis->total() ?? $lichThis->count() }}</span>
                @endif
            </h3>
            <p class="text-muted small mb-0">Các ca thi hiện tại cần cán bộ coi thi tiến hành điểm danh sinh viên</p>
        </div>

        {{-- Thanh tìm kiếm phòng thi --}}
        <form method="GET" action="{{ route('home.index') }}" class="d-flex gap-2" style="max-width: 400px; width: 100%;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control border-start-0 ps-0" placeholder="Tìm theo phòng hoặc tên môn...">
                @if(request('search'))
                    <a href="{{ route('home.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
                <button class="btn btn-primary" type="submit">Tìm</button>
            </div>
        </form>
    </div>

    {{-- Danh sách Card Phòng Thi Đang Diễn Ra --}}
    @if(isset($lichThis) && $lichThis->count() > 0)
        <div class="row g-3">
            @foreach($lichThis as $lich)
                @if($lich->trang_thai === 'dang_dien_ra')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="exam-card h-100">
                        <div class="exam-card-header">
                            <h4 class="exam-card-title" title="{{ $lich->monHoc->ten_mon ?? 'Môn thi' }}">
                                <i class="fa-solid fa-book-bookmark me-1 opacity-75"></i>
                                {{ $lich->monHoc->ten_mon ?? 'Chưa rõ môn' }}
                            </h4>
                            <div class="exam-card-badge">
                                <span class="pulse-dot"></span> ĐANG DIỄN RA
                            </div>
                        </div>

                        <div class="exam-card-body">
                            <div class="exam-info-item">
                                <i class="fa-solid fa-door-open"></i>
                                <span>Phòng thi:</span>
                                <strong class="fs-6 text-primary">{{ $lich->phong }}</strong>
                            </div>
                            <div class="exam-info-item">
                                <i class="fa-regular fa-clock"></i>
                                <span>Giờ thi:</span>
                                <strong>{{ \Carbon\Carbon::parse($lich->gio_thi)->format('H:i') }} ({{ $lich->thoi_luong_thi }} phút)</strong>
                            </div>
                            <div class="exam-info-item">
                                <i class="fa-regular fa-calendar-days"></i>
                                <span>Ngày thi:</span>
                                <strong>{{ \Carbon\Carbon::parse($lich->ngay_thi)->format('d/m/Y') }}</strong>
                            </div>
                            @if($lich->ky_thi)
                            <div class="exam-info-item">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span>Kỳ thi:</span>
                                <strong>{{ $lich->ky_thi }} ({{ $lich->nam_hoc }})</strong>
                            </div>
                            @endif
                        </div>

                        <div class="exam-card-footer">
                            <a href="{{ route('diemdanh.show', $lich->id) }}" class="btn-exam-action">
                                <i class="fa-solid fa-camera"></i>
                                <span>Vào Điểm Danh Ngay</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Phân trang nếu có --}}
        @if(method_exists($lichThis, 'hasPages') && $lichThis->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $lichThis->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        {{-- Empty State Card --}}
        <div class="empty-state-card mt-2">
            <div class="mb-3">
                <i class="fa-solid fa-calendar-check text-primary" style="font-size: 54px; opacity: 0.85;"></i>
            </div>
            <h4>Hiện tại không có phòng thi nào đang diễn ra</h4>
            <p class="text-muted mx-auto" style="max-width: 500px;">
                @if(request('search'))
                    Không tìm thấy phòng thi nào khớp với từ khóa "<strong>{{ request('search') }}</strong>". Vui lòng kiểm tra lại từ khóa.
                @elseif(Auth::guard('giangvien')->check())
                    Tất cả các phòng thi bạn được phân công đã hoàn tất hoặc chưa tới giờ bắt đầu. Thầy/Cô có thể xem toàn bộ lịch trình sắp tới.
                @else
                    Vui lòng đăng nhập để xem danh sách phòng thi được phân công coi thi của Quý Thầy/Cô.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                @if(request('search'))
                    <a href="{{ route('home.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa tìm kiếm
                    </a>
                @endif
                <a href="{{ route('lichthi.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-calendar-days me-1"></i> Xem lịch thi toàn trường
                </a>
                @if(Auth::guard('giangvien')->check())
                    <a href="{{ route('diemdanh.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-clipboard-user me-1"></i> Phòng thi cá nhân của tôi
                    </a>
                @endif
            </div>
        </div>
    @endif
@endsection