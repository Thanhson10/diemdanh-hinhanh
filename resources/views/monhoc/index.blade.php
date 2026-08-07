@extends('layouts.main-layout') 

@section('content')
<div class="container mt-4">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📚 Danh sách môn học</h3>
        <a href="{{ route('monhoc.create') }}" class="btn btn-primary">
            ➕ Thêm môn học
        </a>
    </div>
    {{-- Thanh tìm kiếm --}}
    <form method="GET" action="{{ route('monhoc.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="🔍 Tìm theo mã môn hoặc tên môn...">
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            @if(request('search'))
                <a href="{{ route('monhoc.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>Mã môn</th>
                        <th>Tên môn</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($monHocs as $mon)
                        <tr class="text-center">
                            <td>{{ $mon->ma_mon }}</td>
                            <td class="text-start">{{ $mon->ten_mon }}</td>
                            <td>
                                <a href="{{ route('monhoc.edit', $mon->id) }}" class="btn btn-sm btn-warning">
                                    ✏️ Sửa
                                </a>

                                <form action="{{ route('monhoc.destroy', $mon->id) }}"
                                    method="POST"
                                    style="display:inline-block;"
                                    onsubmit="return confirmDelete();">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3">
                                Chưa có môn học nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
            {{-- Hiển thị phân trang --}}
            @if($monHocs->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($monHocs->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $monHocs->previousPageUrl() }}" aria-label="Previous">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $current = $monHocs->currentPage();
                            $last = $monHocs->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp

                        {{-- First Page Link --}}
                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $monHocs->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endif

                        {{-- Page Number Links --}}
                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $current)
                                <li class="page-item active">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $monHocs->url($i) }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Last Page Link --}}
                        @if($end < $last)
                            @if($end < $last - 1)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $monHocs->url($last) }}">{{ $last }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if($monHocs->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $monHocs->nextPageUrl() }}" aria-label="Next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>

            {{-- Hiển thị thông tin kết quả --}}
            <div class="text-center text-muted mt-2 small">
                Hiển thị {{ ($monHocs->currentPage() - 1) * $monHocs->perPage() + 1 }} 
                đến {{ min($monHocs->currentPage() * $monHocs->perPage(), $monHocs->total()) }} 
                của {{ $monHocs->total() }} kết quả
            </div>
            @endif
        </div>
    </div>
</div>
<script>
function confirmDelete() {
    return confirm('Bạn chắc chắn muốn xóa môn học này?');
}
</script>
@endsection
