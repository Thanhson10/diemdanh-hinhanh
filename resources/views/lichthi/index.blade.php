@extends('layouts.main-layout')

@section('title', 'Lịch thi toàn trường')

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

    {{-- Top Action Bar --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--edu-text); font-size: 20px;">
                <i class="fa-solid fa-calendar-days text-primary me-2"></i>Lịch Thi Toàn Trường
            </h3>
            <p class="text-muted small mb-0">Quản lý lịch tổ chức ca thi, phân công giám thị và danh sách sinh viên thi</p>
        </div>
        @if(Auth::user()->vai_tro === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('lichthi.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tạo Ca Thi Mới</span>
            </a>
        </div>
        @endif
    </div>

    {{-- Bộ Lọc Nâng Cao (Filter Card) --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('lichthi.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Môn học</label>
                    <input type="text" name="ten_mon" value="{{ request('ten_mon') }}" class="form-control" placeholder="Tên môn thi...">
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">MSSV</label>
                    <input type="text" name="ma_sv" value="{{ request('ma_sv') }}" class="form-control" placeholder="Mã sinh viên...">
                </div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Phòng thi</label>
                    <input type="text" name="phong" value="{{ request('phong') }}" class="form-control" placeholder="Ví dụ: P101...">
                </div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Ngày thi</label>
                    <input type="date" name="ngay" value="{{ request('ngay') }}" class="form-control">
                </div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Trạng thái ca thi</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="chua_dien_ra" {{ request('trang_thai') == 'chua_dien_ra' ? 'selected' : '' }}>Chưa diễn ra</option>
                        <option value="dang_dien_ra" {{ request('trang_thai') == 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                        <option value="da_ket_thuc" {{ request('trang_thai') == 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <div class="d-flex gap-1">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fa-solid fa-filter me-1"></i> Lọc
                        </button>
                        @if(count(request()->all()) > 0)
                            <a href="{{ route('lichthi.index') }}" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Bảng Danh Sách Lịch Thi --}}
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th>Môn Thi / Học Phần</th>
                        <th>Thời Gian Bắt Đầu</th>
                        <th class="text-center">Thời Lượng</th>
                        <th class="text-center">Phòng Thi</th>
                        <th class="text-center">Trạng Thái</th>
                        @if(Auth::user()->vai_tro === 'admin')
                            <th class="text-center">Số Lượng</th>
                        @endif
                        <th class="text-center" style="min-width: 220px;">Thao Tác Quản Lý</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lichthis as $lt)
                    <tr>
                        {{-- Môn học & Thông tin kỳ thi --}}
                        <td>
                            <div class="fw-bold text-dark">{{ $lt->monHoc->ten_mon ?? 'Chưa rõ môn' }}</div>
                            <div class="small text-muted mt-1">
                                <span class="badge bg-light text-secondary border me-1">{{ $lt->ky_thi ?? 'Thi kết thúc' }}</span>
                                <span>{{ $lt->nam_hoc }}</span>
                            </div>
                        </td>

                        {{-- Thời gian thi --}}
                        <td>
                            <div class="fw-semibold text-primary">
                                <i class="fa-regular fa-calendar me-1"></i>{{ $lt->thoi_gian_thi->format('d/m/Y') }}
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="fa-regular fa-clock me-1"></i>{{ $lt->thoi_gian_thi->format('H:i') }}
                            </div>
                        </td>

                        {{-- Thời lượng --}}
                        <td class="text-center">
                            <span class="edu-badge edu-badge-secondary">
                                {{ $lt->thoi_luong_thi }} phút
                            </span>
                        </td>

                        {{-- Phòng thi --}}
                        <td class="text-center">
                            <span class="edu-badge edu-badge-primary fw-bold fs-7">
                                <i class="fa-solid fa-door-open me-1"></i>{{ $lt->phong }}
                            </span>
                        </td>

                        {{-- Trạng thái --}}
                        <td class="text-center">
                            @if($lt->trang_thai === 'dang_dien_ra')
                                <span class="edu-badge edu-badge-success">
                                    <span class="pulse-dot"></span> Đang diễn ra
                                </span>
                            @elseif($lt->trang_thai === 'chua_dien_ra')
                                <span class="edu-badge edu-badge-warning">
                                    <i class="fa-regular fa-clock"></i> Chưa diễn ra
                                </span>
                            @else
                                <span class="edu-badge edu-badge-secondary">
                                    <i class="fa-solid fa-check"></i> Đã kết thúc
                                </span>
                            @endif
                        </td>

                        {{-- Thống kê SL (cho Admin) --}}
                        @if(Auth::user()->vai_tro === 'admin')
                        <td class="text-center small">
                            <div><strong class="text-dark">{{ $lt->so_sinh_vien ?? $lt->diemDanhs()->count() }}</strong> SV</div>
                            <div class="text-muted"><strong class="text-primary">{{ $lt->so_giang_vien ?? $lt->phanCongGVs()->count() }}</strong> Giám thị</div>
                        </td>
                        @endif

                        {{-- Nhóm thao tác hành động --}}
                        <td class="text-center">
                            <div class="d-inline-flex flex-wrap gap-1 justify-content-center">
                                {{-- Nút Xem danh sách sinh viên ca thi --}}
                                <a href="{{ route('lichthi.show', $lt->id) }}" class="btn-action-soft btn-action-soft-primary" title="Xem danh sách sinh viên">
                                    <i class="fa-solid fa-users"></i> Xem SV
                                </a>

                                @if(Auth::user()->vai_tro === 'admin')
                                    {{-- Phân công cán bộ coi thi --}}
                                    <a href="{{ route('lichthi.phancong', $lt->id) }}" class="btn-action-soft btn-action-soft-info" title="Phân công giảng viên">
                                        <i class="fa-solid fa-user-check"></i> Phân công
                                    </a>

                                    {{-- Thêm sinh viên vào ca thi (khi chưa diễn ra) --}}
                                    @if($lt->trang_thai === 'chua_dien_ra')
                                        <button type="button" class="btn-action-soft btn-action-soft-primary addSVBtn" data-id="{{ $lt->id }}" title="Thêm sinh viên theo danh sách MSSV">
                                            <i class="fa-solid fa-user-plus"></i> Thêm SV
                                        </button>

                                        <a href="{{ route('lichthi.edit', $lt->id) }}" class="btn-action-soft btn-action-soft-warning" title="Chỉnh sửa ca thi">
                                            <i class="fa-solid fa-pen-to-square"></i> Sửa
                                        </a>

                                        <form action="{{ route('lichthi.destroy', $lt->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch thi môn {{ $lt->monHoc->ten_mon ?? '' }} tại phòng {{ $lt->phong }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-soft btn-action-soft-danger" title="Xóa lịch thi">
                                                <i class="fa-solid fa-trash-can"></i> Xóa
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Khi đang diễn ra: Xem tiến trình điểm danh --}}
                                    @if($lt->trang_thai === 'dang_dien_ra')
                                        <a href="{{ route('diemdanh.show', ['diemdanh' => $lt->id, 'type' => 'tientrinh']) }}" class="btn-action-soft btn-action-soft-info" title="Xem tiến trình điểm danh trực tiếp">
                                            <i class="fa-solid fa-chart-pie"></i> Tiến trình
                                        </a>
                                    @endif

                                    {{-- Khi đã kết thúc: Xem kết quả và xuất báo cáo --}}
                                    @if($lt->trang_thai === 'da_ket_thuc')
                                        <a href="{{ route('lichthi.ketqua', $lt->id) }}?{{ http_build_query(request()->query()) }}" class="btn-action-soft btn-action-soft-info" title="Xem kết quả điểm danh">
                                            <i class="fa-solid fa-chart-column"></i> Kết quả
                                        </a>
                                        <a href="{{ route('lichthi.export', $lt->id) }}" class="btn-action-soft btn-action-soft-primary" title="Xuất Excel kết quả">
                                            <i class="fa-solid fa-file-excel"></i> Xuất Excel
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-5 text-center">
                            <div class="empty-state-card border-0 py-2">
                                <i class="fa-solid fa-calendar-xmark mb-2"></i>
                                <h4>Không tìm thấy ca thi nào</h4>
                                <p class="text-muted mb-3">
                                    @if(count(request()->all()) > 0)
                                        Không có ca thi nào phù hợp với bộ lọc hiện tại. Thầy/Cô vui lòng thử xóa lọc hoặc tìm với từ khóa khác.
                                    @else
                                        Hiện tại trường học chưa có ca thi nào được tạo trên hệ thống.
                                    @endif
                                </p>
                                @if(count(request()->all()) > 0)
                                    <a href="{{ route('lichthi.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                                    </a>
                                @elseif(Auth::user()->vai_tro === 'admin')
                                    <a href="{{ route('lichthi.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-plus me-1"></i> Tạo ca thi mới
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
        @if(method_exists($lichthis, 'hasPages') && $lichthis->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $lichthis->firstItem() }}</strong> - <strong>{{ $lichthis->lastItem() }}</strong> trên tổng <strong>{{ $lichthis->total() }}</strong> ca thi
                </div>
                <div>
                    {{ $lichthis->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Thêm Sinh Viên Theo Danh Sách MSSV -->
<div class="modal fade" id="addStudentsModal" tabindex="-1" aria-labelledby="addStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header py-3 text-white" style="background: var(--edu-gradient);">
                <h5 class="modal-title fw-bold" id="addStudentsModalLabel">
                    <i class="fa-solid fa-user-plus me-2"></i>Thêm Sinh Viên Vào Ca Thi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="lichThiId">

                <div class="alert alert-info py-2 small mb-3">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Nhập mỗi Mã số sinh viên (MSSV) trên <strong>1 dòng</strong>. Hệ thống sẽ tự động tra cứu họ tên và kiểm tra trùng lịch.
                </div>

                <div class="row g-3">
                    <!-- Cột nhập MSSV -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold small text-muted">Danh sách MSSV (1 mã / dòng):</label>
                        <textarea id="mssvInput" class="form-control" rows="10" placeholder="DH52000001&#10;DH52000002&#10;DH52000003..."></textarea>
                    </div>

                    <!-- Cột xem trước danh sách tên -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold small text-muted">Kết quả tra cứu sinh viên:</label>
                        <div id="studentNames" class="bg-white border p-3 rounded" style="height: 250px; overflow-y: auto; font-size: 13px;">
                            <span class="text-muted fst-italic">Nhập danh sách MSSV vào ô bên cạnh để hệ thống tìm kiếm thông tin...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="addStudentsBtn">
                    <i class="fa-solid fa-check me-1"></i> Xác Nhận Thêm Toàn Bộ
                </button>
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
        document.getElementById('studentNames').innerHTML = '<span class="text-muted fst-italic">Nhập danh sách MSSV vào ô bên cạnh để hệ thống tìm kiếm thông tin...</span>';

        new bootstrap.Modal(document.getElementById('addStudentsModal')).show();
    });
});

// Tra cứu danh sách sinh viên real-time khi gõ MSSV
document.getElementById('mssvInput').addEventListener('input', function () {
    const mssvList = this.value.trim().split('\n').filter(x => x.trim() !== '');

    if (mssvList.length === 0) {
        document.getElementById('studentNames').innerHTML = '<span class="text-muted fst-italic">Nhập danh sách MSSV vào ô bên cạnh để hệ thống tìm kiếm thông tin...</span>';
        return;
    }

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
        if (data.length > 0) {
            data.forEach((sv, idx) => {
                html += `<div class="py-1 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border me-1">${sv.ma_sv}</span>
                        <strong>${sv.ho_ten}</strong>
                    </div>
                    <span class="text-muted small">${sv.lop ?? ''}</span>
                </div>`;
            });
        } else {
            html = '<div class="text-danger py-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Không tìm thấy sinh viên nào khớp với danh sách MSSV trên.</div>';
        }
        document.getElementById('studentNames').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('studentNames').innerHTML = '<span class="text-danger">Lỗi kết nối khi tra cứu sinh viên.</span>';
    });
});

// Thêm sinh viên
document.getElementById('addStudentsBtn').addEventListener('click', async function () {
    const mssvList = document.getElementById('mssvInput').value
                        .trim()
                        .split('\n')
                        .filter(x => x.trim() !== '');

    if (mssvList.length === 0) {
        Swal.fire("Thiếu dữ liệu", "Thầy/Cô vui lòng nhập ít nhất 1 MSSV.", "warning");
        return;
    }

    const confirmResult = await Swal.fire({
        title: "Xác nhận thêm sinh viên",
        html: `Thầy/Cô có chắc chắn muốn thêm <b>${mssvList.length}</b> sinh viên vào ca thi này không?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Thêm ngay",
        cancelButtonText: "Xem lại"
    });

    if (!confirmResult.isConfirmed) return;

    try {
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
                <div class="text-success mb-2"><i class="fa-solid fa-circle-check"></i> Đã thêm thành công: <b>${added.length}</b> sinh viên.</div>
                ${skipped.length > 0 ? `<div class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i> Bỏ qua: <b>${skipped.length}</b> sinh viên (đã có hoặc trùng ca).</div>` : ''}
            `,
            showCancelButton: skipped.length > 0,
            confirmButtonText: "Hoàn tất",
            cancelButtonText: "Xem chi tiết SV bỏ qua",
            icon: "success"
        });

        if (result.dismiss === Swal.DismissReason.cancel) {
            await Swal.fire({
                title: 'Danh sách sinh viên bị bỏ qua',
                html: `<div style="text-align: left; max-height: 300px; overflow-y: auto;" class="small">${skipped.join('<hr class="my-1">')}</div>`,
                icon: 'info',
                confirmButtonText: 'Đóng'
            });
        }

        location.reload();
    } catch (e) {
        Swal.fire("Lỗi hệ thống", "Không thể gửi dữ liệu lên máy chủ.", "error");
    }
});
</script>
@endsection
