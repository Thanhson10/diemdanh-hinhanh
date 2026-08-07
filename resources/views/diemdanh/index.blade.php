@extends('layouts.main-layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📅 Danh sách phòng thi</h2>

   {{-- Bộ lọc nâng cao --}}
    <form method="GET" action="{{ route('diemdanh.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-2">
                <input type="text" name="ten_mon" value="{{ request('ten_mon') }}" class="form-control" placeholder="Tên môn">
            </div>
            <div class="col-md-2">
                <input type="text" name="phong" value="{{ request('phong') }}" class="form-control" placeholder="Phòng">
            </div>
            <div class="col-md-2">
                <input type="date" name="ngay" value="{{ request('ngay') }}" class="form-control" placeholder="Ngày thi">
            </div>
            <!-- <div class="col-md-2">
                <input type="time" name="gio" value="{{ request('gio') }}" class="form-control" placeholder="Giờ thi">
            </div> -->
            <div class="col-md-2">
                <input type="text" name="ky_thi" value="{{ request('ky_thi') }}" class="form-control" placeholder="Kỳ thi">
            </div>
            <div class="col-md-2">
                <input type="text" name="nam_hoc" value="{{ request('nam_hoc') }}" class="form-control" placeholder="Năm học">
            </div>
            <div class="col-md-2">
                <select name="trang_thai" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="chua_dien_ra" {{ request('trang_thai') == 'chua_dien_ra' ? 'selected' : '' }}>Chưa diễn ra</option>
                    <option value="dang_dien_ra" {{ request('trang_thai') == 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="da_ket_thuc" {{ request('trang_thai') == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-1">
                    <button class="btn btn-primary w-50">Lọc</button>
                    @if(count(request()->all()) > 0)
                        <a href="{{ route('diemdanh.index') }}" class="btn btn-outline-secondary w-50">Xóa</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Bảng danh sách --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tên môn</th>
                <th>Ngày thi</th>
                <th>Phòng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lichThis as $lich)
                <tr>
                    <td>{{ $loop->iteration + ($lichThis->currentPage() - 1) * $lichThis->perPage() }}</td>
                    <td>{{ $lich->monHoc->ten_mon  }}</td>
                    <td>{{ $lich->thoi_gian_thi->format('d/m/Y H:i') }}</td>
                    <td>{{ $lich->phong }}</td>
                    <td>
                        @if($lich->trang_thai === 'da_ket_thuc')
                            <span class="badge bg-secondary">Đã kết thúc</span>
                        @elseif($lich->trang_thai === 'dang_dien_ra')
                            <span class="badge bg-success">Đang diễn ra</span>
                        @elseif($lich->trang_thai === 'chua_dien_ra')
                            <span class="badge bg-warning text-dark">Chưa diễn ra</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('diemdanh.show', $lich->id) }}" class="btn btn-primary btn-sm">
                            👁️ Xem sinh viên
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Không có lịch thi nào</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Hiển thị phân trang --}}
    @if($lichThis->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if($lichThis->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichThis->previousPageUrl() }}" aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $current = $lichThis->currentPage();
                    $last = $lichThis->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                {{-- First Page Link --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichThis->url(1) }}">1</a>
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
                            <a class="page-link" href="{{ $lichThis->url($i) }}">{{ $i }}</a>
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
                        <a class="page-link" href="{{ $lichThis->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($lichThis->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichThis->nextPageUrl() }}" aria-label="Next">
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
        Hiển thị {{ ($lichThis->currentPage() - 1) * $lichThis->perPage() + 1 }} 
        đến {{ min($lichThis->currentPage() * $lichThis->perPage(), $lichThis->total()) }} 
        của {{ $lichThis->total() }} kết quả
    </div>
    @endif
</div>
@endsection
