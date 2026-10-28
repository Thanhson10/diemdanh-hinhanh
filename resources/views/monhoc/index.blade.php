@extends('layouts.main-layout') 

@section('title', 'Quản lý Danh mục Môn học')

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
                <i class="fa-solid fa-book-open text-primary me-2"></i>Danh Mục Môn Học
            </h3>
            <p class="text-muted small mb-0">Quản lý các học phần, mã môn phục vụ lập lịch và tổ chức thi</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('monhoc.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Thêm Môn Học</span>
            </a>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('monhoc.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control border-start-0 ps-0" placeholder="Tìm theo mã môn hoặc tên môn học...">
                    @if(request('search'))
                        <a href="{{ route('monhoc.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                    <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                </div>
            </div>
            <div class="col-12 col-md-4 text-md-end text-muted small">
                Tổng cộng: <strong class="text-primary">{{ $monHocs->total() ?? count($monHocs) }}</strong> môn học
            </div>
        </form>
    </div>

    {{-- Table Container --}}
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">STT</th>
                        <th style="width: 180px;">Mã Môn</th>
                        <th>Tên Môn Học</th>
                        <th class="text-center" style="width: 160px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monHocs as $index => $mon)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ method_exists($monHocs, 'firstItem') ? $monHocs->firstItem() + $index : $index + 1 }}
                            </td>
                            <td>
                                <span class="edu-badge edu-badge-primary">
                                    <i class="fa-solid fa-hashtag me-1 opacity-75"></i>{{ $mon->ma_mon }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $mon->ten_mon }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('monhoc.edit', $mon->id) }}" class="btn-action-soft btn-action-soft-warning" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>

                                    <form action="{{ route('monhoc.destroy', $mon->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirmDelete('{{ $mon->ten_mon }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-soft btn-action-soft-danger" title="Xóa môn">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-5 text-center">
                                <div class="empty-state-card border-0 py-2">
                                    <i class="fa-solid fa-book-bookmark mb-2"></i>
                                    <h4>Chưa có dữ liệu môn học</h4>
                                    <p class="text-muted mb-3">
                                        @if(request('search'))
                                            Không tìm thấy môn học nào khớp với từ khóa "{{ request('search') }}".
                                        @else
                                            Hệ thống hiện tại chưa có môn học nào trong danh mục.
                                        @endif
                                    </p>
                                    <a href="{{ route('monhoc.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-plus me-1"></i> Thêm môn học đầu tiên
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if(method_exists($monHocs, 'hasPages') && $monHocs->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $monHocs->firstItem() }}</strong> - <strong>{{ $monHocs->lastItem() }}</strong> trên tổng <strong>{{ $monHocs->total() }}</strong> môn
                </div>
                <div>
                    {{ $monHocs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function confirmDelete(name) {
    return confirm(`Bạn có chắc chắn muốn xóa môn học "${name}" không?`);
}
</script>
@endsection
