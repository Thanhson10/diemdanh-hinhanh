@extends('layouts.main-layout')
@section('content')
<div class="container-fluid px-3 py-2">
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-3">
            <i class="fas fa-exclamation-circle me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-3">
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center mb-3">
            <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    <!-- Header Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-dark mb-1">
                <i class="fas fa-user-graduate text-primary me-2"></i>Danh Sách Sinh Viên
            </h2>
            <p class="text-muted small mb-0">Quản lý hồ sơ, lớp sinh hoạt và dữ liệu nhận diện khuôn mặt sinh viên</p>
        </div>
        
        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('sinhvien.create') }}" class="btn btn-edu btn-edu-primary">
                <i class="fas fa-plus"></i> Thêm sinh viên
            </a>
        </div>
        @endif
    </div>

    @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
    <!-- Import & Template Card -->
    <div class="edu-card mb-4" style="background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%); border-left: 4px solid var(--primary);">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- LEFT: FORM IMPORT -->
                <form action="{{ route('sinhvien.import') }}" method="POST" enctype="multipart/form-data" 
                    class="d-flex gap-2 align-items-center flex-wrap">
                    @csrf
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-excel text-success fs-4"></i>
                        <span class="fw-semibold text-dark small">Nhập từ Excel:</span>
                    </div>

                    <input type="file" name="file" accept=".xlsx,.xls,.csv" 
                        class="form-control form-control-sm" style="max-width: 250px;" required>

                    <button type="submit" class="btn btn-success btn-sm px-3 fw-semibold">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Tải lên
                    </button>
                </form>

                <!-- RIGHT: DOWNLOAD TEMPLATE -->
                <a href="{{ route('download.mau.sinhvien') }}" 
                    class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    <i class="fas fa-file-download me-1"></i> Tải file Excel mẫu
                </a>
            </div>

            @if(session('import_failures'))
            <div class="alert alert-warning mt-3 mb-0" style="max-height: 250px; overflow-y: auto;">
                <div class="fw-bold mb-2 text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i> Danh sách dòng bị lỗi (dòng 1 & 2 là tiêu đề):
                </div>
                <ul class="mb-0 small ps-3">
                    @foreach(session('import_failures') as $failure)
                        @php
                            $row = $failure->values();
                            $maSv = $row[0] ?? '';
                            $hoTen = trim(($row[1] ?? '') . ' ' . ($row[2] ?? ''));
                            $lop = $row[3] ?? '';
                        @endphp
                        <li class="mb-1">
                            <span class="badge bg-danger">Dòng {{ $failure->row() }}</span>:
                            @foreach($failure->errors() as $error)
                                {{ $error }}
                            @endforeach
                            <span class="text-muted ms-1">(MSSV: {{ $maSv }} | Họ tên: {{ $hoTen }} | Lớp: {{ $lop }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="filter-card mb-4">
        <div class="filter-card-header">
            <i class="fas fa-search me-2 text-primary"></i>Bộ lọc tìm kiếm sinh viên
        </div>
        <form method="GET" action="{{ route('sinhvien.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Mã sinh viên (MSSV)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="ma_sv" class="form-control"
                            placeholder="Nhập MSSV..."
                            value="{{ request('ma_sv') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Họ và tên</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="fas fa-user"></i></span>
                        <input type="text" name="ho_ten" class="form-control"
                            placeholder="Nhập tên..."
                            value="{{ request('ho_ten') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Lớp sinh hoạt</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="fas fa-users-class"></i></span>
                        <input type="text" name="lop" class="form-control"
                            placeholder="Ví dụ: D20_TH01"
                            value="{{ request('lop') }}">
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-center pb-2">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="chua_co_anh" value="1" id="chua_co_anh"
                            {{ request('chua_co_anh') ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold text-muted" for="chua_co_anh">Chưa có ảnh train</label>
                    </div>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-edu btn-edu-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-edu btn-edu-secondary btn-sm" title="Đặt lại">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Student Table Card -->
    <div class="edu-card">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th style="width: 130px;">MSSV</th>
                        <th>Họ và Tên</th>
                        <th style="width: 140px;">Lớp</th>
                        <th>Email Liên Hệ</th>
                        <th style="width: 120px;" class="text-center">Ảnh Train</th>
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <th style="width: 120px;" class="text-center">Thao tác</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sinhviens as $sv)
                    <tr>
                        <td>
                            <span class="fw-bold font-monospace text-primary">{{ $sv->ma_sv }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sv->ho_ten }}</div>
                        </td>
                        <td>
                            <span class="badge-edu badge-edu-info">
                                <i class="fas fa-graduation-cap me-1"></i>{{ $sv->lop }}
                            </span>
                        </td>
                        <td>
                            <a href="mailto:{{ $sv->email }}" class="text-decoration-none text-muted small">
                                <i class="far fa-envelope me-1"></i>{{ $sv->email }}
                            </a>
                        </td>
                        <td class="text-center">
                            @php
                                $anhTrain = $sv->anhDaTrain;
                                $count = $anhTrain->count();
                                $latest = $anhTrain->last();
                            @endphp

                            @if($count > 0)
                                <div class="position-relative d-inline-block" 
                                    style="cursor: pointer;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#imageModal{{ $sv->id }}"
                                    title="Xem các ảnh khuôn mặt đã huấn luyện">
                                    
                                    <img src="{{ asset($latest->hinh_anh_url) }}" 
                                        width="48" 
                                        height="48" 
                                        class="rounded-circle shadow-sm" 
                                        style="object-fit: cover; border: 2px solid #e2e8f0; transition: transform 0.2s;"
                                        onmouseover="this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.transform='scale(1)'">

                                    @if($count > 1)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" 
                                            style="font-size: 10px; border: 2px solid white;">
                                            +{{ $count - 1 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <button type="button" 
                                    class="badge-edu badge-edu-danger border-0"
                                    style="cursor: pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{ $sv->id }}">
                                    <i class="fas fa-camera me-1"></i>Chưa có ảnh
                                </button>
                            @endif
                        </td>
                        
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('sinhvien.edit', $sv->id) }}?{{ http_build_query(request()->query()) }}" 
                                    class="btn-action btn-action-warning" 
                                    title="Chỉnh sửa thông tin">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action btn-action-danger" 
                                        title="Xóa sinh viên"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên {{ $sv->ho_ten }} ({{ $sv->ma_sv }})?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin' ? 6 : 5 }}">
                            <div class="empty-state">
                                <i class="fas fa-user-graduate"></i>
                                <h5>Không tìm thấy sinh viên nào</h5>
                                <p>Thử điều chỉnh điều kiện lọc hoặc nhập thêm sinh viên mới vào hệ thống.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if($sinhviens->hasPages())
        <div class="p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted small">
                Hiển thị <strong>{{ ($sinhviens->currentPage() - 1) * $sinhviens->perPage() + 1 }}</strong> 
                - <strong>{{ min($sinhviens->currentPage() * $sinhviens->perPage(), $sinhviens->total()) }}</strong> 
                trên tổng số <strong>{{ $sinhviens->total() }}</strong> sinh viên
            </div>
            <div>
                {{ $sinhviens->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal kho ảnh của từng SV -->
@foreach($sinhviens as $sv)
<div class="modal fade" id="imageModal{{ $sv->id }}" data-masv="{{ $sv->ma_sv }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 640px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom p-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">Kho Ảnh Nhận Diện: {{ $sv->ho_ten }}</h6>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="badge bg-white text-primary border font-monospace small px-2">MSSV: {{ $sv->ma_sv }}</span>
                            <span class="badge bg-white text-secondary border small px-2">Lớp: {{ $sv->lop }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn btn-sm btn-edu btn-edu-primary"
                            onclick="openTrainModal('{{ $sv->ma_sv }}', '{{ $sv->id }}')">
                        <i class="fas fa-plus"></i> Huấn luyện thêm
                    </button>
                    <button type="button" class="btn-close ms-1" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border py-2 px-3 small d-flex align-items-center mb-3 rounded-2 text-muted">
                    <i class="fas fa-info-circle text-primary me-2 fs-6"></i>
                    <span>Ảnh dùng cho AI đối chiếu nhận diện khi vào phòng thi. Tối đa <strong>2 ảnh</strong>/sinh viên. Bấm vào ảnh để phóng to.</span>
                </div>

                <div class="row g-3" id="imageContainer{{ $sv->id }}">
                    <div class="text-center text-muted py-4">Đang tải dữ liệu ảnh...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal phóng to ảnh -->
<div class="modal fade" id="previewImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 text-center">
            <img id="previewImage" src="" class="rounded shadow-lg" style="max-height: 80vh; max-width: 100%; object-fit: contain; margin: 0 auto;">
        </div>
    </div>
</div>

<!-- Modal upload ảnh huấn luyện -->
<div class="modal fade" id="trainModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="fas fa-camera text-primary me-2"></i>Huấn luyện ảnh khuôn mặt mới
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form id="trainForm">
                    @csrf
                    <input type="hidden" name="ma_sv" id="ma_sv">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Chọn ảnh chân dung rõ mặt</label>
                        <input type="file" name="hinh_anh" accept="image/*" class="form-control" required>
                        <div class="form-text small">Ảnh chụp chính diện, đủ ánh sáng, không đeo khẩu trang hoặc kính râm.</div>
                    </div>

                    <button type="submit" class="btn btn-edu btn-edu-primary w-100 py-2">
                        <i class="fas fa-brain me-1"></i> Tải Lên & Huấn Luyện AI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('[id^="imageModal"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const svId = this.id.replace('imageModal', '');
            loadImages(svId);
        });

        modal.addEventListener('hidden.bs.modal', function () {
            const svId = this.id.replace('imageModal', '');
            const container = document.getElementById('imageContainer' + svId);
            if (container) container.dataset.loaded = "";
        });
    });

    function previewImage(url) {
        document.getElementById('previewImage').src = url;
    }

    window.csrfToken = "{{ csrf_token() }}";

    async function loadImages(svId) {
        const container = document.getElementById('imageContainer' + svId);
        if (!container) return;

        const modalEl = document.getElementById('imageModal' + svId);
        const maSv = modalEl ? (modalEl.dataset.masv || '') : '';

        container.innerHTML = `
        <div class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-muted small mt-2">Đang nạp ảnh khuôn mặt...</div>
        </div>`;

        const url = "{{ route('sinhvien.anhDaTrain', ':id') }}".replace(':id', svId);
        try {
            const res = await fetch(url);
            if (!res.ok) {
                container.innerHTML = "<div class='text-danger text-center py-3'>Lỗi tải ảnh!</div>";
                return;
            }

            const data = await res.json();
            if (data.length === 0) {
                container.innerHTML = `
                <div class="col-12 text-center py-4">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 text-secondary" style="width: 60px; height: 60px;">
                        <i class="fas fa-camera fa-2x"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Chưa có ảnh khuôn mặt nào</h6>
                    <p class="text-muted small mb-3">Sinh viên này chưa có dữ liệu nhận diện AI trong hệ thống.</p>
                    <button type="button" class="btn btn-sm btn-edu btn-edu-primary" onclick="openTrainModal('${maSv}', '${svId}')">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Tải lên ảnh đầu tiên
                    </button>
                </div>`;
                return;
            }

            let html = '';
            data.forEach((img, idx) => {
                html += `
                <div class="col-sm-6">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-sm" style="background: #ffffff;">
                        <div class="position-relative" style="background: #0f172a;">
                            <img src="${img.url}" 
                                class="w-100"
                                style="height: 220px; object-fit: cover; cursor: pointer; transition: opacity 0.2s;"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'"
                                onclick="previewImage('${img.url}')"
                                data-bs-toggle="modal"
                                data-bs-target="#previewImageModal"
                                title="Nhấn để xem phóng to">

                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-pill px-2 py-1 small">
                                Ảnh #${idx + 1}
                            </span>

                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 rounded-circle p-2 text-white" 
                                  style="cursor: pointer;"
                                  onclick="previewImage('${img.url}')"
                                  data-bs-toggle="modal"
                                  data-bs-target="#previewImageModal"
                                  title="Phóng to">
                                <i class="fas fa-search-plus"></i>
                            </span>
                        </div>

                        <div class="p-3 text-center bg-white border-top">
                            <div class="mb-2">
                                <span class="badge-edu badge-edu-success">
                                    <i class="fas fa-check-circle me-1"></i>Đã huấn luyện AI
                                </span>
                            </div>

                            <button type="button"
                                class="btn btn-sm btn-outline-danger w-100 py-1"
                                onclick="deleteImage(event, ${img.id}, ${svId})">
                                <i class="fas fa-trash-alt me-1"></i> Xóa ảnh này
                            </button>
                        </div>
                    </div>
                </div>
                `;
            });

            // Nếu chỉ mới có 1 ảnh, hiển thị thêm slot thứ 2 cho cân xứng
            if (data.length === 1) {
                html += `
                <div class="col-sm-6">
                    <div class="h-100 p-4 border border-2 border-dashed rounded-3 text-center d-flex flex-column align-items-center justify-content-center bg-light"
                         style="min-height: 280px; cursor: pointer; transition: all 0.2s;"
                         onclick="openTrainModal('${maSv}', '${svId}')"
                         onmouseover="this.style.borderColor='var(--edu-primary)'; this.style.backgroundColor='#eff6ff';"
                         onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc';">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-plus text-primary fs-4"></i>
                        </div>
                        <div class="fw-bold text-dark small mb-1">Thêm ảnh nhận diện thứ 2</div>
                        <div class="text-muted" style="font-size: 11.5px;">Tải thêm ảnh chân dung góc chụp khác để tăng độ chính xác</div>
                    </div>
                </div>
                `;
            }

            container.innerHTML = html;
            container.dataset.loaded = true;
        } catch (e) {
            container.innerHTML = "<div class='text-danger text-center py-3'>Lỗi kết nối máy chủ!</div>";
        }
    }

    function openTrainModal(ma_sv, svId){
        document.getElementById('ma_sv').value = ma_sv;
        document.getElementById('trainForm').dataset.svId = svId;
        new bootstrap.Modal(document.getElementById('trainModal')).show();
    }

    document.getElementById('trainForm').addEventListener('submit', async function(e){
        e.preventDefault();
        let submitBtn = this.querySelector('button[type="submit"]');
        let originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang xử lý...';

        let formData = new FormData(this);

        try {
            const res = await fetch("{{ route('rekognition.train.ajax') }}", {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            const data = await res.json();
            alert(data.message);

            if(data.success){
                const svId = document.getElementById('trainForm').dataset.svId;
                await new Promise(r => setTimeout(r, 400));
                await loadImages(svId);
                bootstrap.Modal.getInstance(document.getElementById('trainModal')).hide();
                this.reset();
            }
        } catch (err) {
            alert("Đã xảy ra lỗi trong quá trình xử lý!");
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    function deleteImage(event, id, svId) {
        event.preventDefault();
        event.stopPropagation();

        if (!confirm("Bạn chắc chắn muốn xóa ảnh khuôn mặt này?")) return;

        const url = "{{ route('anh_train.destroy', ':id') }}".replace(':id', id);

        fetch(url, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": window.csrfToken,
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            alert("Xóa thành công");
            const container = document.getElementById('imageContainer' + svId);
            container.dataset.loaded = "";
            loadImages(svId);
        })
        .catch(err => {
            alert("Lỗi khi xóa ảnh!");
        });
    }
</script>
@endsection