@extends('layouts.main-layout')

@section('title', 'Phòng ' . $lichThi->phong . ' - ' . ($lichThi->monHoc->ten_mon ?? 'Điểm danh'))

@section('content')
<div class="container-fluid px-0">
    <div id="alert-container" class="mb-3"></div>

    {{-- Room Header Card --}}
    <div class="edu-card mb-4 shadow-sm">
        <div class="edu-card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="edu-badge edu-badge-primary fs-6 px-3 py-1">
                            <i class="fa-solid fa-door-open me-1"></i>Phòng: {{ $lichThi->phong }}
                        </span>
                        @if($lichThi->trang_thai === 'dang_dien_ra')
                            <span class="edu-badge edu-badge-success">
                                <span class="pulse-dot"></span> Đang diễn ra
                            </span>
                        @elseif($lichThi->trang_thai === 'chua_dien_ra')
                            <span class="edu-badge edu-badge-warning">
                                <i class="fa-regular fa-clock"></i> Chưa diễn ra
                            </span>
                        @else
                            <span class="edu-badge edu-badge-secondary">
                                <i class="fa-solid fa-check"></i> Đã kết thúc
                            </span>
                        @endif
                        <span class="text-muted small">
                            <i class="fa-regular fa-calendar-days me-1"></i>{{ $lichThi->thoi_gian_thi->format('d/m/Y') }} ({{ $lichThi->thoi_gian_thi->format('H:i') }})
                        </span>
                    </div>

                    <h3 class="fw-bold text-dark mb-1" style="font-size: 22px;">
                        {{ $lichThi->monHoc->ten_mon ?? 'Học phần thi' }}
                    </h3>
                    <div class="text-muted small">
                        <span>Học kỳ: <strong>{{ $lichThi->ky_thi ?? 'Chính thức' }}</strong></span> • 
                        <span>Năm học: <strong>{{ $lichThi->nam_hoc }}</strong></span> • 
                        <span>Thời lượng: <strong>{{ $lichThi->thoi_luong_thi }} phút</strong></span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('diemdanh.index', request()->query()) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>DS phòng thi</span>
                    </a>

                    @if($lichThi->trang_thai === 'dang_dien_ra')
                        <a href="{{ route('rekognition.index', [$lichThi->id]) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm px-3 py-2 fw-bold">
                            <i class="fa-solid fa-camera"></i>
                            <span>Điểm Danh Camera AI</span>
                        </a>
                    @else
                        <button class="btn btn-secondary d-inline-flex align-items-center gap-2" disabled title="Chỉ mở camera khi ca thi đang diễn ra">
                            <i class="fa-solid fa-video-slash"></i>
                            <span>Điểm danh (Chưa mở)</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Thống kê nhanh sĩ số sinh viên --}}
            @php
                $tongSo = $sinhViens->count();
                $daDiemDanh = $sinhViens->where('ket_qua', 'hợp lệ')->count();
                $chuaDiemDanh = $tongSo - $daDiemDanh;
            @endphp
            <div class="row g-3 mt-3 pt-3 border-top">
                <div class="col-4 col-sm-3">
                    <div class="small text-muted">Sĩ số phòng thi:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $tongSo }} <span class="fs-7 fw-normal text-muted">SV</span></div>
                </div>
                <div class="col-4 col-sm-3">
                    <div class="small text-muted">Đã điểm danh:</div>
                    <div class="fs-5 fw-bold text-success">{{ $daDiemDanh }} <span class="fs-7 fw-normal text-muted">SV</span></div>
                </div>
                <div class="col-4 col-sm-3">
                    <div class="small text-muted">Chưa có mặt:</div>
                    <div class="fs-5 fw-bold text-warning">{{ $chuaDiemDanh }} <span class="fs-7 fw-normal text-muted">SV</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Search Bar --}}
    <div class="filter-card mb-3">
        <form method="GET" action="{{ route('diemdanh.show', $lichThi->id) }}" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="input-group" style="max-width: 340px;">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control border-start-0 ps-0" placeholder="Tìm MSSV hoặc tên sinh viên...">
            </div>

            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" name="chua_diem_danh" value="1" id="chuaDiemDanh"
                       {{ request('chua_diem_danh') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label fw-semibold small text-muted ms-1" for="chuaDiemDanh">
                    Chỉ hiện sinh viên chưa điểm danh
                </label>
            </div>

            <div class="ms-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fa-solid fa-filter me-1"></i> Lọc
                </button>
                @if(request()->has('search') || request()->has('chua_diem_danh'))
                    <a href="{{ route('diemdanh.show', $lichThi->id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bảng danh sách sinh viên --}}
    @if ($sinhViens->isEmpty())
        <div class="empty-state-card shadow-sm">
            <i class="fa-solid fa-user-xmark mb-2"></i>
            <h4>Không có sinh viên nào trong danh sách</h4>
            <p class="text-muted mb-0">Ca thi này hiện chưa có sinh viên hoặc không có sinh viên phù hợp với bộ lọc tìm kiếm.</p>
        </div>
    @else
        <div class="table-container shadow-sm">
            <div class="table-responsive">
                <table class="table-edu text-center">
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th style="width: 80px;">Ảnh Mẫu</th>
                            <th style="width: 130px;">MSSV</th>
                            <th class="text-start">Họ Và Tên</th>
                            <th style="width: 110px;">Lớp</th>
                            <th style="width: 100px;">Điểm Danh</th>
                            <th style="width: 110px;">Kết Quả</th>
                            <th style="width: 100px;">Độ Chính Xác</th>
                            <th style="width: 150px;">Thời Gian</th>
                            <th style="width: 110px;">Hình Thức</th>
                            <th style="width: 180px;">Ghi Chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sinhViens as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <button class="btn-action-soft btn-action-soft-primary btn-xem-anh py-1 px-2" 
                                            data-id="{{ $item->sinhVien->id }}" title="Xem ảnh khuôn mặt đã train">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="edu-badge edu-badge-primary font-monospace">{{ $item->sinhVien->ma_sv }}</span>
                                </td>
                                <td class="text-start fw-semibold text-dark">{{ $item->sinhVien->ho_ten }}</td>
                                <td><span class="text-muted small">{{ $item->sinhVien->lop ?? '-' }}</span></td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center m-0">
                                        <input type="checkbox" class="form-check-input toggle-diemdanh"
                                               data-id="{{ $item->id }}"
                                               style="cursor: pointer;"
                                               {{ $item->ket_qua === 'hợp lệ' ? 'checked' : '' }}
                                               @if($lichThi->trang_thai !== 'dang_dien_ra') disabled @endif>
                                    </div>
                                </td>
                                <td class="col-ketqua">
                                    @if($item->ket_qua === 'hợp lệ')
                                        <span class="edu-badge edu-badge-success">Hợp lệ</span>
                                    @elseif($item->ket_qua === 'Vắng mặt')
                                        <span class="edu-badge edu-badge-danger">Vắng mặt</span>
                                    @else
                                        <span class="edu-badge edu-badge-secondary">Chưa có</span>
                                    @endif
                                </td>
                                <td class="col-dochinhxac font-monospace small">
                                    {{ $item->do_chinh_xac ? $item->do_chinh_xac . '%' : '-' }}
                                </td>
                                <td class="col-thoigian small text-muted">
                                    {{ $item->thoi_gian_dd ? \Carbon\Carbon::parse($item->thoi_gian_dd)->format('H:i:s d/m') : '-' }}
                                </td>
                                <td class="col-hinhthuc small">
                                    @if($item->hinh_thuc_dd === 'Camera')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Camera AI</span>
                                    @elseif($item->hinh_thuc_dd === 'Thủ công')
                                        <span class="badge bg-secondary-subtle text-secondary border">Thủ công</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <select class="form-select form-select-sm ghi-chu-select" 
                                            data-id="{{ $item->id }}" disabled style="font-size: 12px;">
                                        <option value="">-- Không có --</option>
                                        <option value="chua_co_anh" {{ $item->ghi_chu == 'chua_co_anh' ? 'selected' : '' }}>Chưa có ảnh</option>
                                        <option value="mat_khong_khop" {{ $item->ghi_chu == 'mat_khong_khop' ? 'selected' : '' }}>Không nhận diện được</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Modal Xem ảnh đã train --}}
<div class="modal fade" id="modalAnhTrain" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header py-3 text-white" style="background: var(--edu-gradient);">
                <h5 class="modal-title fw-bold" style="font-size: 16px;">
                    <i class="fa-solid fa-id-card me-2"></i>Ảnh Khuôn Mặt Huấn Luyện (Train)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center bg-light" id="modal-body-anh">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="small text-muted mt-2">Đang tải hình ảnh từ hệ thống lưu trữ...</div>
            </div>
        </div>
    </div>
</div>

{{-- Nút cuộn lên đầu trang --}}
<button id="scrollTopBtn" title="Lên đầu trang" style="position: fixed; bottom: 24px; right: 24px; z-index: 999; background: var(--edu-primary); color: white; border: none; width: 44px; height: 44px; border-radius: 50%; font-size: 16px; display: none; box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3); transition: all 0.2s;">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý toggle điểm danh thủ công
    document.querySelectorAll('.toggle-diemdanh').forEach(cb => {
        cb.addEventListener('change', async function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const checked = this.checked;
            const token = '{{ csrf_token() }}';
            row.style.opacity = "0.6";

            const showAlert = (message, type='success') => {
                const alertBox = document.createElement('div');
                alertBox.className = `alert alert-${type} alert-dismissible fade show`;
                alertBox.innerHTML = `<strong>${type==='success'?'<i class="fa-solid fa-circle-check me-1"></i>':'<i class="fa-solid fa-triangle-exclamation me-1"></i>'}</strong> ${message}`;
                const container = document.getElementById('alert-container');
                container.innerHTML = '';
                container.appendChild(alertBox);
                setTimeout(() => alertBox.classList.remove('show'), 3000);
            };

            if(checked){
                if(!confirm("Bạn có chắc muốn ĐIỂM DANH sinh viên này?")){
                    this.checked = false;
                    row.style.opacity="1";
                    return;
                }
            }else{
                if(!confirm("Bạn có chắc muốn HỦY điểm danh sinh viên này?")){
                    this.checked = true;
                    row.style.opacity="1";
                    return;
                }
            }

            try {
                const res = await fetch("{{ route('diemdanh.toggle') }}", {
                    method: "POST",
                    headers: { "Content-Type":"application/json", "X-CSRF-TOKEN": token },
                    body: JSON.stringify({ id, checked })
                });
                const data = await res.json();
                if(data.success){
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const timeStr = `${hours}:${minutes}:${seconds} ${day}/${month}`;

                    row.querySelector('.col-ketqua').innerHTML = checked ? '<span class="edu-badge edu-badge-success">Hợp lệ</span>':'<span class="edu-badge edu-badge-secondary">Chưa có</span>';
                    row.querySelector('.col-dochinhxac').textContent = checked ? '100%':'-';
                    row.querySelector('.col-thoigian').textContent = checked ? timeStr:'-';
                    row.querySelector('.col-hinhthuc').innerHTML = checked ? '<span class="badge bg-secondary-subtle text-secondary border">Thủ công</span>' : '-';
                    updateGhiChuState(row);

                    row.style.transition = 'background-color 0.4s';
                    row.style.backgroundColor = checked ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)';
                    setTimeout(()=>row.style.backgroundColor='', 1000);

                    showAlert(data.message, checked?'success':'danger');
                } else {
                    showAlert(data.message || 'Có lỗi xảy ra!', 'danger');
                    this.checked = !checked;
                }
            } catch(e){
                showAlert('Lỗi mạng: '+e.message,'danger');
                this.checked = !checked;
            } finally { row.style.opacity="1"; }
        });
    });

    // Xem ảnh huấn luyện của sinh viên
    document.querySelectorAll('.btn-xem-anh').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            let html = '';
            const modalBody = document.getElementById('modal-body-anh');
            modalBody.innerHTML = '<div class="spinner-border text-primary" role="status"></div><div class="small text-muted mt-2">Đang tải hình ảnh...</div>';
            
            const modal = new bootstrap.Modal(document.getElementById('modalAnhTrain'));
            modal.show();

            try {
                const res = await fetch(`/sinhvien/${id}/anh-train`);
                const data = await res.json();
                if (data.length === 0) {
                    html = `<div class="text-muted py-4"><i class="fa-regular fa-image mb-2 fs-2 d-block opacity-50"></i>Sinh viên này chưa có ảnh huấn luyện khuôn mặt trên hệ thống.</div>`;
                } else { 
                    html = `<div class="d-flex flex-wrap gap-3 justify-content-center">`;
                    data.forEach(img => {
                        html += `
                            <div class="p-1 bg-white border rounded shadow-sm text-center">
                                <img src="${img.url}" style="width:100%; max-width:240px; height:240px; border-radius:10px; object-fit:cover;">
                                <div class="small text-muted mt-1 font-monospace">${img.file_name || 'Ảnh train'}</div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }
                modalBody.innerHTML = html;
            } catch(e) {
                modalBody.innerHTML = '<div class="text-danger">Lỗi khi tải ảnh khuôn mặt sinh viên.</div>';
            }
        });
    });

    function updateGhiChuState(row) {
        const checkbox = row.querySelector('.toggle-diemdanh');
        const select = row.querySelector('.ghi-chu-select');
        if (checkbox && select) {
            select.disabled = checkbox.checked;
        }
    }

    document.querySelectorAll('tr[data-id]').forEach(row => {
        updateGhiChuState(row);
    });

    // Xử lý nút cuộn lên đầu trang
    const scrollBtn = document.getElementById("scrollTopBtn");
    const scrollContainer = document.querySelector(".content"); 

    if (scrollContainer && scrollBtn) {
        scrollContainer.addEventListener("scroll", function () {
            if (scrollContainer.scrollTop > 200) {
                scrollBtn.style.display = "block";
            } else {
                scrollBtn.style.display = "none";
            }
        });

        scrollBtn.addEventListener("click", function () {
            scrollContainer.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }
});
</script>
@endsection