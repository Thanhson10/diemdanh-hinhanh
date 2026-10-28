@extends('layouts.main-layout')

@section('title', 'Quản lý Giảng viên & Cán bộ Coi thi')

@section('content')
<div class="container-fluid px-0">
    {{-- Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fa-solid fa-circle-exclamation fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--edu-text); font-size: 20px;">
                <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>Danh Sách Giảng Viên
            </h3>
            <p class="text-muted small mb-0">Quản lý hồ sơ cán bộ coi thi, phân công giám thị và tài khoản</p>
        </div>
        @if(Auth::user()->vai_tro === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('giangvien.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fa-solid fa-user-plus"></i>
                <span>Thêm Giảng Viên</span>
            </a>
        </div>
        @endif
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('giangvien.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control border-start-0 ps-0" placeholder="Tìm theo họ tên, mã giảng viên hoặc email...">
                    @if(request('search'))
                        <a href="{{ route('giangvien.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                </div>
            </div>
            <div class="col-12 col-md-4 text-md-end text-muted small">
                Tổng cộng: <strong class="text-primary">{{ $giangviens->total() ?? count($giangviens) }}</strong> giảng viên
            </div>
        </form>
    </div>

    {{-- Table Container --}}
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th style="width: 140px;">Mã GV</th>
                        <th>Họ Và Tên</th>
                        <th>Email Công Tác</th>
                        <th class="text-center" style="width: 130px;">Trạng Thái</th>
                        <th class="text-center" style="width: 250px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($giangviens as $gv)
                        <tr>
                            <td>
                                <span class="edu-badge edu-badge-primary">
                                    <i class="fa-solid fa-id-badge me-1 opacity-75"></i>{{ $gv->ma_gv }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $gv->ho_ten }}</div>
                                @if($gv->vai_tro === 'admin')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size: 11px;">Admin</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $gv->email }}</td>
                            <td class="text-center">
                                @if($gv->is_active)
                                    <span class="edu-badge edu-badge-success">
                                        <i class="fa-solid fa-circle-check"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="edu-badge edu-badge-danger">
                                        <i class="fa-solid fa-lock"></i> Đã khóa
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex flex-wrap gap-1 justify-content-center">
                                    <a href="{{ route('giangvien.edit', $gv->id) }}?{{ http_build_query(request()->query()) }}" class="btn-action-soft btn-action-soft-primary" title="Chỉnh sửa thông tin">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>

                                    @if($gv->is_active)
                                    <a href="{{ route('giangvien.phancong', $gv->id) }}" class="btn-action-soft btn-action-soft-info" title="Phân công ca thi">
                                        <i class="fa-solid fa-calendar-plus"></i> Phân công
                                    </a>
                                    @endif

                                    @if(auth()->id() != $gv->id)
                                    <form action="{{ route('giangvien.toggle', $gv->id) }}" method="POST" class="d-inline m-0">
                                        @csrf @method('PATCH')
                                        @if($gv->is_active)
                                            <button type="submit" class="btn-action-soft btn-action-soft-warning" title="Khóa tài khoản" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?')">
                                                <i class="fa-solid fa-lock"></i> Khóa
                                            </button>
                                        @else
                                            <button type="submit" class="btn-action-soft btn-action-soft-success" title="Mở khóa tài khoản" onclick="return confirm('Mở khóa tài khoản này?')">
                                                <i class="fa-solid fa-lock-open"></i> Mở
                                            </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('giangvien.destroy', $gv->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giảng viên {{ $gv->ho_ten }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-soft btn-action-soft-danger" title="Xóa giảng viên">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-5 text-center">
                                <div class="empty-state-card border-0 py-2">
                                    <i class="fa-solid fa-users-slash mb-2"></i>
                                    <h4>Chưa có dữ liệu giảng viên</h4>
                                    <p class="text-muted mb-3">
                                        @if(request('search'))
                                            Không tìm thấy giảng viên nào khớp với từ khóa "{{ request('search') }}".
                                        @else
                                            Hệ thống hiện tại chưa có thông tin cán bộ giảng viên.
                                        @endif
                                    </p>
                                    @if(Auth::user()->vai_tro === 'admin')
                                    <a href="{{ route('giangvien.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-user-plus me-1"></i> Thêm giảng viên đầu tiên
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
        @if(method_exists($giangviens, 'hasPages') && $giangviens->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $giangviens->firstItem() }}</strong> - <strong>{{ $giangviens->lastItem() }}</strong> trên tổng <strong>{{ $giangviens->total() }}</strong> giảng viên
                </div>
                <div>
                    {{ $giangviens->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
