@extends('layouts.main-layout')

@section('title', 'Phòng thi cá nhân của tôi')

@section('content')
<div class="container-fluid px-0">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fa-solid fa-circle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Title --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--edu-text); font-size: 20px;">
                <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Danh Sách Phòng Thi Cá Nhân
            </h3>
            <p class="text-muted small mb-0">Các ca thi Thầy/Cô được phân công làm cán bộ coi thi & điểm danh</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('home.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-house"></i>
                <span>Màn hình chính</span>
            </a>
        </div>
    </div>

    {{-- Bộ Lọc Nâng Cao --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('diemdanh.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Môn thi</label>
                    <input type="text" name="ten_mon" value="{{ request('ten_mon') }}" class="form-control" placeholder="Tên môn học...">
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Phòng thi</label>
                    <input type="text" name="phong" value="{{ request('phong') }}" class="form-control" placeholder="Ví dụ: P101...">
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Ngày thi</label>
                    <input type="date" name="ngay" value="{{ request('ngay') }}" class="form-control">
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Trạng thái ca thi</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="chua_dien_ra" {{ request('trang_thai') == 'chua_dien_ra' ? 'selected' : '' }}>Chưa diễn ra</option>
                        <option value="dang_dien_ra" {{ request('trang_thai') == 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                        <option value="da_ket_thuc" {{ request('trang_thai') == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="d-flex gap-1">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fa-solid fa-filter me-1"></i> Lọc ca thi
                        </button>
                        @if(count(request()->all()) > 0)
                            <a href="{{ route('diemdanh.index') }}" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Bảng Phòng Thi Của Tôi --}}
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">STT</th>
                        <th>Học Phần / Môn Thi</th>
                        <th>Thời Gian Bắt Đầu</th>
                        <th class="text-center">Phòng Thi</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-center" style="width: 170px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lichThis as $index => $lich)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ method_exists($lichThis, 'firstItem') ? $lichThis->firstItem() + $index : $index + 1 }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $lich->monHoc->ten_mon ?? 'Chưa rõ môn' }}</div>
                                <div class="small text-muted mt-1">
                                    <span class="badge bg-light text-secondary border me-1">{{ $lich->ky_thi ?? 'GK/CK' }}</span>
                                    <span>Thời lượng: {{ $lich->thoi_luong_thi }} phút</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $lich->thoi_gian_thi->format('d/m/Y') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="fa-regular fa-clock me-1"></i>{{ $lich->thoi_gian_thi->format('H:i') }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="edu-badge edu-badge-primary fw-bold fs-7">
                                    <i class="fa-solid fa-door-open me-1"></i>{{ $lich->phong }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($lich->trang_thai === 'dang_dien_ra')
                                    <span class="edu-badge edu-badge-success">
                                        <span class="pulse-dot"></span> Đang diễn ra
                                    </span>
                                @elseif($lich->trang_thai === 'chua_dien_ra')
                                    <span class="edu-badge edu-badge-warning">
                                        <i class="fa-regular fa-clock"></i> Chưa diễn ra
                                    </span>
                                @else
                                    <span class="edu-badge edu-badge-secondary">
                                        <i class="fa-solid fa-check"></i> Đã kết thúc
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('diemdanh.show', $lich->id) }}?{{ http_build_query(request()->query()) }}" class="btn-action-soft btn-action-soft-primary w-100 justify-content-center">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Vào phòng thi</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-5 text-center">
                                <div class="empty-state-card border-0 py-2">
                                    <i class="fa-solid fa-calendar-xmark mb-2"></i>
                                    <h4>Không có ca thi nào được phân công</h4>
                                    <p class="text-muted mb-3">
                                        @if(count(request()->all()) > 0)
                                            Không tìm thấy ca thi nào khớp với bộ lọc tìm kiếm hiện tại.
                                        @else
                                            Thầy/Cô hiện chưa có lịch phân công coi thi nào trong đợt này.
                                        @endif
                                    </p>
                                    @if(count(request()->all()) > 0)
                                        <a href="{{ route('diemdanh.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                                        </a>
                                    @else
                                        <a href="{{ route('lichthi.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-calendar-days me-1"></i> Xem lịch thi chung toàn trường
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if(method_exists($lichThis, 'hasPages') && $lichThis->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $lichThis->firstItem() }}</strong> - <strong>{{ $lichThis->lastItem() }}</strong> trên tổng <strong>{{ $lichThis->total() }}</strong> ca thi
                </div>
                <div>
                    {{ $lichThis->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
