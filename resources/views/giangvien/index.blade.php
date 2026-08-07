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
    <h2>Danh sách Giảng viên</h2>
    @if(Auth::user()->vai_tro === 'admin')
    <div class="mb-3 text-end">
    <a href="{{ route('giangvien.create') }}" class="btn btn-success mb-3">+ Thêm Giảng viên</a>
    </div> 
    @endif
    {{-- Thanh tìm kiếm --}}
    <form method="GET" action="{{ route('giangvien.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="🔍 Tìm theo họ tên giảng viên...">
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            @if(request('search'))
                <a href="{{ route('giangvien.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>
    <br>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Mã GV</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Thao tác</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($giangviens as $gv)
                <tr>
                    <td>{{ $gv->ma_gv }}</td>
                    <td>{{ $gv->ho_ten }}</td>
                    <td>{{ $gv->email }}</td>
                   
                    <td>
                        <a href="{{ route('giangvien.edit', $gv->id) }}?{{ http_build_query(request()->query()) }}" class="btn btn-primary btn-sm">Sửa</a>
                        <form action="{{ route('giangvien.destroy', $gv->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa giảng viên này?')">Xóa</button>
                        </form>
                        @if($gv->is_active)
                        <a href="{{ route('giangvien.phancong', $gv->id) }}" class="btn btn-info btn-sm">Phân công</a>
                        @endif
                        @if(auth()->id() != $gv->id)
                        <form action="{{ route('giangvien.toggle', $gv->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            @if($gv->is_active)
                                <button class="btn btn-warning btn-sm"
                                    onclick="return confirm('Khóa tài khoản này?')">
                                    Khóa
                                </button>
                            @else
                                <button class="btn btn-success btn-sm"
                                    onclick="return confirm('Mở khóa tài khoản này?')">
                                    Mở khóa
                                </button>
                            @endif
                        </form>
                        @endif
                    </td>
                    <td>
                        @if($gv->is_active)
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Đã khóa</span>
                        @endif
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
        <br>
        <!-- @if(Auth::user()->vai_tro === 'admin')
        <form action="{{ route('giangvien.import') }}" method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
            <button type="submit" class="btn btn-success">Import Excel</button>
        </form>
        @endif -->

    </table>
    {{-- Hiển thị phân trang --}}
    @if($giangviens->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if($giangviens->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $giangviens->previousPageUrl() }}" aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $current = $giangviens->currentPage();
                    $last = $giangviens->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                {{-- First Page Link --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $giangviens->url(1) }}">1</a>
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
                            <a class="page-link" href="{{ $giangviens->url($i) }}">{{ $i }}</a>
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
                        <a class="page-link" href="{{ $giangviens->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($giangviens->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $giangviens->nextPageUrl() }}" aria-label="Next">
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
        Hiển thị {{ ($giangviens->currentPage() - 1) * $giangviens->perPage() + 1 }} 
        đến {{ min($giangviens->currentPage() * $giangviens->perPage(), $giangviens->total()) }} 
        của {{ $giangviens->total() }} kết quả
    </div>
    @endif
</div>
@endsection
