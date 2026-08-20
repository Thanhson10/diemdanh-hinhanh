@extends('layouts.main-layout')

@section('content')
<div class="container mt-4">
   @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
     @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <h2>Danh sách lịch thi</h2>
    
    @if(Auth::user()->vai_tro === 'admin')
     <div class="mb-3 text-end">
    <a href="{{ route('lichthi.create') }}" class="btn btn-success mb-3">+ Thêm mới</a>
    </div>
    @endif
    {{-- Bộ lọc nâng cao --}}
    <form method="GET" action="{{ route('lichthi.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-2">
                <input type="text" name="ma_sv" value="{{ request('ma_sv') }}" class="form-control" placeholder="MSSV">
            </div>
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
                        <a href="{{ route('lichthi.index') }}" class="btn btn-outline-secondary w-50">Xóa</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
    <br>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Tên môn</th>
                <th>Thời gian</th>
                <th>Thời lượng (phút)</th>
                <th>Phòng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lichthis as $lt)
            <tr>
                <td>{{ $lt->monHoc->ten_mon }}</td>
                <td>{{ $lt->thoi_gian_thi->format('d-m-Y H:i') }}</td>
                <td>{{ $lt->thoi_luong_thi }}</td>
                <td>{{ $lt->phong }}</td>
                <td style="position: relative; min-width: 160px;">

                    @if($lt->trang_thai === 'da_ket_thuc')
                        <span class="badge bg-danger">Đã kết thúc</span>
                    @elseif($lt->trang_thai === 'chua_dien_ra')
                        <span class="badge bg-warning text-dark">Chưa diễn ra</span>
                    @else
                        <span class="badge bg-success">Đang diễn ra</span>
                    @endif
                     @if(Auth::user()->vai_tro === 'admin')
                        <a href="{{ route('lichthi.phancong', $lt->id) }}" class="btn btn-info btn-sm">
                                    Phân công
                        </a>
                        <div class="d-none d-md-block" style="position:absolute; right:30px; top:3px; text-align:right; font-size:12px; line-height:1.2;">
                            <div>SL: {{ $lt->so_sinh_vien }} SV</div>
                            <div>GV đã PC: {{ $lt->so_giang_vien }} GV</div>
                        </div>
                        
                     @endif
                    @if($lt->trang_thai === 'dang_dien_ra')
                        @if(Auth::user()->vai_tro === 'admin')
                        <a href="{{ route('lichthi.show', $lt->id) }}"
                        class="btn btn-primary btn-sm viewSVBtn"
                        style="position:absolute; right:3px; top:25px;
                        padding:1px 4px; font-size:12px;
                        width:20px; height:20px;
                        display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                         <a href="{{ route('diemdanh.show', ['diemdanh' => $lt->id, 'type' => 'tientrinh']) }}" 
                            class="btn btn-info btn-sm">
                            📊 Xem tiến trình
                            </a>
                        @else
                        <a href="{{ route('lichthi.show', $lt->id) }}"
                        class="btn btn-primary btn-sm viewSVBtn"
                        style="position:absolute; right:3px; top:20px;
                        padding:1px 4px; font-size:12px;
                        width:20px; height:20px;
                        display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @endif
                    @else
                        @if(Auth::user()->vai_tro === 'admin')
                            @if($lt->trang_thai == 'chua_dien_ra')
                                    <form action="{{ route('lichthi.destroy', $lt->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Xóa lịch thi này?')">Xóa</button>
                                    </form>
                            @endif
                        @endif
                        @if($lt->trang_thai === 'da_ket_thuc')
                            @if(Auth::user()->vai_tro === 'admin')
                            <a href="{{ route('lichthi.ketqua', $lt->id) }}?{{ http_build_query(request()->query()) }}" class="btn btn-info btn-sm">
                                📊 Xem kết quả
                            </a>
                            <a href="{{ route('lichthi.show', $lt->id) }}"
                            class="btn btn-primary btn-sm viewSVBtn"
                            style="position:absolute; right:3px; top:25px;
                            padding:1px 4px; font-size:12px;
                            width:20px; height:20px;
                            display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @else
                            <a href="{{ route('lichthi.show', $lt->id) }}"
                            class="btn btn-primary btn-sm viewSVBtn"
                            style="position:absolute; right:3px; top:20px;
                            padding:1px 4px; font-size:12px;
                            width:20px; height:20px;
                            display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endif

                        @else
                            @if(Auth::user()->vai_tro === 'admin')

                                <a href="{{ route('lichthi.edit', $lt->id) }}" class="btn btn-warning btn-sm">
                                    Sửa
                                </a>

                                <button type="button"
                                    class="btn btn-success btn-sm addSVBtn"
                                    data-id="{{ $lt->id }}"
                                    style="position:absolute; right:3px; top:3px;
                                    padding:1px 4px; font-size:12px;
                                    width:20px; height:20px;
                                    display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>

                                <a href="{{ route('lichthi.show', $lt->id) }}"
                                class="btn btn-primary btn-sm viewSVBtn"
                                style="position:absolute; right:3px; top:25px;
                                padding:1px 4px; font-size:12px;
                                width:20px; height:20px;
                                display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                            @else

                                <a href="{{ route('lichthi.show', $lt->id) }}"
                                class="btn btn-primary btn-sm viewSVBtn"
                                style="position:absolute; right:3px; top:20px;
                                padding:1px 4px; font-size:12px;
                                width:20px; height:20px;
                                display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                            @endif
                        @endif
                    @endif

                </td>

            </tr>
            @endforeach
        </tbody>
        {{-- @if(Auth::user()->vai_tro === 'admin')
        <form action="{{ route('lichthi.import') }}" method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
            <button type="submit" class="btn btn-success">Import Excel</button>
        </form>
        @endif  --}}
    </table>

    {{-- Hiển thị phân trang --}}
    @if($lichthis->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if($lichthis->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichthis->previousPageUrl() }}" aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $current = $lichthis->currentPage();
                    $last = $lichthis->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                {{-- First Page Link --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichthis->url(1) }}">1</a>
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
                            <a class="page-link" href="{{ $lichthis->url($i) }}">{{ $i }}</a>
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
                        <a class="page-link" href="{{ $lichthis->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($lichthis->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $lichthis->nextPageUrl() }}" aria-label="Next">
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
        Hiển thị {{ ($lichthis->currentPage() - 1) * $lichthis->perPage() + 1 }} 
        đến {{ min($lichthis->currentPage() * $lichthis->perPage(), $lichthis->total()) }} 
        của {{ $lichthis->total() }} kết quả
    </div>
    @endif
</div>
    
    <!-- Modal Thêm Sinh Viên -->
    <div class="modal fade" id="addStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sinh viên vào ca thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="lichThiId">

                    <div class="row">
                        <!-- Nhập mã số sinh viên -->
                        <div class="col-6">
                            <label class="form-label">Nhập MSSV:</label>
                            <textarea id="mssvInput" class="form-control" rows="11"></textarea>
                        </div>

                        <!-- Danh sách tên hiển thị -->
                        <div class="col-6">
                            <label class="form-label">Tên sinh viên:</label>
                            <div id="studentNames"
                                    style="height: 275px; overflow-y: auto; border: 1px solid #ccc;
                                    padding: 8px; border-radius: 5px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="addStudentsBtn">Thêm toàn bộ</button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
let selectedLichThiId = null;

// Mở modal nhập MSSV
document.querySelectorAll('.addSVBtn').forEach(btn => {
    btn.addEventListener('click', function () {
        selectedLichThiId = this.getAttribute('data-id');
        document.getElementById('lichThiId').value = selectedLichThiId;

        document.getElementById('mssvInput').value = "";
        document.getElementById('studentNames').innerHTML = "<i>Nhập MSSV để hiển thị...</i>";

        new bootstrap.Modal(document.getElementById('addStudentsModal')).show();
    });
});

// Hiển thị danh sách sinh viên khi nhập MSSV
document.getElementById('mssvInput').addEventListener('input', function () {
    const mssvList = this.value.trim().split('\n').filter(x => x.trim() !== '');

    fetch("{{ route('sinhvien.searchByList') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ mssv: mssvList })
    })
    .then(res => res.json())
    .then(data => {
        let html = '';
        data.forEach(sv => html += `<div><b>${sv.ma_sv}</b> – ${sv.ho_ten}</div>`);
        document.getElementById('studentNames').innerHTML = html || "<i>Không tìm thấy sinh viên</i>";
    });
});

// Thêm sinh viên
document.getElementById('addStudentsBtn').addEventListener('click', async function () {
    const mssvList = document.getElementById('mssvInput').value
                        .trim()
                        .split('\n')
                        .filter(x => x.trim() !== '');

    if (mssvList.length === 0) {
        Swal.fire("Thiếu dữ liệu", "Bạn chưa nhập MSSV", "warning");
        return;
    }

    const confirmResult = await Swal.fire({
        title: "Xác nhận",
        html: `Bạn có chắc muốn thêm <b>${mssvList.length}</b> sinh viên vào ca thi này?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Có, thêm ngay",
        cancelButtonText: "Hủy"
    });

    if (!confirmResult.isConfirmed) return;

    const res = await fetch(`/lichthi/${selectedLichThiId}/add-students`, {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ mssv: mssvList })
    });

    const data = await res.json();
    const added = data.added || [];
    const skipped = data.skipped || [];

    if (!added.length && !skipped.length) {
        Swal.fire("Thông báo", "Không có sinh viên hợp lệ để thêm.", "warning");
        return;
    }

    const result = await Swal.fire({
        title: "Kết quả thêm sinh viên",
        html: `
            ✅ Đã thêm <b>${added.length}</b> sinh viên.<br>
            ⚠️ Bỏ qua <b>${skipped.length}</b> sinh viên.
        `,
        showCancelButton: skipped.length > 0,
        confirmButtonText: "OK",
        cancelButtonText: "Xem sinh viên bỏ qua",
        icon: "success"
    });

    // Nếu bấm xem sinh viên bỏ qua thì đợi popup đó đóng
    if (result.dismiss === Swal.DismissReason.cancel) {
        await Swal.fire({
            title: 'Sinh viên bị bỏ qua',
            html: `<div style="text-align: left;">${skipped.join('<hr>')}</div>`,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }

    location.reload();
});

</script>
@endsection
