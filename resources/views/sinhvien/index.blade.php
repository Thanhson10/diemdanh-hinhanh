@extends('layouts.main-layout')
@section('content')
<div class="container p-4">
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

    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Danh sách sinh viên</h2>
        
        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
        <div class="d-flex gap-2">
            <a href="{{ route('sinhvien.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm sinh viên
            </a>
        </div>
        @endif
    </div>

    @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
    <div class="card mb-4">
        <div class="card-body">
           
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- LEFT: FORM IMPORT -->
                <form action="{{ route('sinhvien.import') }}" method="POST" enctype="multipart/form-data" 
                    class="d-flex gap-2 align-items-center">
                    @csrf

                    <input type="file" name="file" accept=".xlsx,.xls,.csv" 
                        class="form-control form-control-sm" required>

                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-file-import"></i> Import Excel
                    </button>
                </form>

                <!-- RIGHT: DOWNLOAD TEMPLATE -->
                <a href="{{ route('download.mau.sinhvien') }}" 
                class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-download"></i> Tải file mẫu
                </a>

            </div>
           @if(session('import_failures'))
            <div class="alert alert-warning" style="max-height: 300px; overflow-y: auto;">
                <strong>Các dòng bị lỗi: (dòng 1 và 2 là tiêu đề)</strong>
                <ul>
                    @foreach(session('import_failures') as $failure)
                        @php
                            $row = $failure->values();

                            $maSv = $row[0] ?? '';
                            $hoTen = trim(($row[1] ?? '') . ' ' . ($row[2] ?? ''));
                            $lop = $row[3] ?? '';
                        @endphp

                        <li>
                            <b>Dòng {{ $failure->row() }}:</b>

                            @foreach($failure->errors() as $error)
                                {{ $error }}
                            @endforeach

                            <br>
                            <small>
                                (MSSV: {{ $maSv }} | Họ tên: {{ $hoTen }} | Lớp: {{ $lop }})
                            </small>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        </div>
    </div>
    @endif

    <form method="GET" action="{{ route('sinhvien.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="ma_sv" class="form-control"
                    placeholder="Mã sinh viên"
                    value="{{ request('ma_sv') }}">
            </div>

            <div class="col-md-3">
                <input type="text" name="ho_ten" class="form-control"
                    placeholder="Họ tên"
                    value="{{ request('ho_ten') }}">
            </div>

            <div class="col-md-3">
                <input type="text" name="lop" class="form-control"
                    placeholder="Lớp"
                    value="{{ request('lop') }}">
            </div>

            <div class="col-md-2 d-flex align-items-center">
                <input type="checkbox" name="chua_co_anh" value="1"
                    {{ request('chua_co_anh') ? 'checked' : '' }}>
                <label class="ms-2">Chưa có ảnh</label>
            </div>

            <!-- <div class="col-md-3">
                <label class="form-label mb-1">Trạng thái ảnh</label>

                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="trang_thai_anh" value=""
                            {{ request('trang_thai_anh') == null ? 'checked' : '' }}>
                        <label class="form-check-label">Tất cả</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="trang_thai_anh" value="co_anh"
                            {{ request('trang_thai_anh') == 'co_anh' ? 'checked' : '' }}>
                        <label class="form-check-label text-success">Đã có ảnh</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="trang_thai_anh" value="chua_co_anh"
                            {{ request('trang_thai_anh') == 'chua_co_anh' ? 'checked' : '' }}>
                        <label class="form-check-label text-danger">Chưa có ảnh</label>
                    </div>
                </div>
            </div> -->

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    Lọc
                </button>
                <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
            
        </div>
    </form>

    <!-- Desktop Table -->
    <div class="d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Email</th>
                        <th>Hình ảnh</th>
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <th>Hành động</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sinhviens as $sv)
                    <tr>
                        <td><strong>{{ $sv->ma_sv }}</strong></td>
                        <td>{{ $sv->ho_ten }}</td>
                        <td><span class="badge bg-info">{{ $sv->lop }}</span></td>
                        <td>{{ $sv->email }}</td>
                        <td>
                            @php
                                // Lấy danh sách ảnh đã train của sv
                                $anhTrain = $sv->anhDaTrain;
                                $count = $anhTrain->count();
                                $latest = $anhTrain->last(); // Lấy ảnh mới nhất
                            @endphp

                            @if($count > 0)
                                <div class="position-relative d-inline-block" 
                                    style="cursor: pointer;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#imageModal{{ $sv->id }}">
                                    
                                    <!-- Ảnh đại diện -->
                                    <img src="{{ asset($latest->hinh_anh_url) }}" 
                                        width="60" 
                                        height="60" 
                                        class="rounded-circle object-fit-cover border" 
                                        style="object-fit: cover;">

                                    <!-- Badge hiển thị số lượng ảnh phụ -->
                                    @if($count > 1)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                            style="font-size: 10px; border: 2px solid white;">
                                            +{{ $count - 1 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small"
                                    style="cursor:pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{ $sv->id }}">
                                    Chưa có ảnh
                                </span>
                            @endif
                        </td>
                        
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sinhvien.edit', $sv->id) }}?{{ http_build_query(request()->query()) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa sinh viên {{ $sv->ho_ten }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin' ? 6 : 5 }}" class="text-center text-muted py-4">
                            <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                            Không có sinh viên nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Table -->
    <div class="d-block d-md-none">
        <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table table-bordered table-striped table-hover" style="min-width: 800px;">
                <thead class="table-dark">
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Email</th>
                        <th>Hình ảnh</th>
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <th>Hành động</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sinhviens as $sv)
                    <tr>
                        <td><strong>{{ $sv->ma_sv }}</strong></td>
                        <td>{{ $sv->ho_ten }}</td>
                        <td><span class="badge bg-info">{{ $sv->lop }}</span></td>
                        <td>{{ $sv->email }}</td>
                        <td>
                            @php
                                // Lấy danh sách ảnh đã train của sv
                                $anhTrain = $sv->anhDaTrain;
                                $count = $anhTrain->count();
                                $latest = $anhTrain->last(); // Lấy ảnh mới nhất
                            @endphp

                            @if($count > 0)
                                <div class="position-relative d-inline-block" 
                                    style="cursor: pointer;" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#imageModal{{ $sv->id }}">
                                    
                                    <!-- Ảnh đại diện -->
                                    <img src="{{ asset($latest->hinh_anh_url) }}" 
                                        width="60" 
                                        height="60" 
                                        class="rounded-circle object-fit-cover border" 
                                        style="object-fit: cover;">

                                    <!-- Badge hiển thị số lượng ảnh phụ -->
                                    @if($count > 1)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                            style="font-size: 10px; border: 2px solid white;">
                                            +{{ $count - 1 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small"
                                    style="cursor:pointer;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{ $sv->id }}">
                                    Chưa có ảnh
                                </span>
                            @endif
                        </td>
                        
                        @if(Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin')
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sinhvien.edit', $sv->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa sinh viên {{ $sv->ho_ten }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::guard('giangvien')->check() && Auth::guard('giangvien')->user()->vai_tro === 'admin' ? 6 : 5 }}" class="text-center text-muted py-4">
                            <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                            Không có sinh viên nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Hiển thị thông báo về cuộn ngang trên mobile -->
        <div class="alert alert-info mt-2 d-flex align-items-center" style="font-size: 12px;">
            <i class="fas fa-info-circle me-2"></i>
            <span>Vuốt sang trái/phải để xem thêm thông tin</span>
        </div>
    </div>


    @foreach($sinhviens as $sv)
            <div class="modal fade" id="imageModal{{ $sv->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
    
                            <h5 class="modal-title mb-0">
                                Kho ảnh của: {{ $sv->ho_ten }}
                            </h5>

                            <div class="d-flex align-items-center gap-2">
                                
                                <button class="btn btn-sm btn-primary"
                                        onclick="openTrainModal('{{ $sv->ma_sv }}', '{{ $sv->id }}')">
                                    <i class="fas fa-plus"></i> Train thêm
                                </button>

                                <!-- Nút đóng -->
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                        </div>
                        <div class="modal-body">
                            <div class="row g-3" id="imageContainer{{ $sv->id }}">
                                <div class="text-center text-muted">Đang tải...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endforeach
    <div class="modal fade" id="previewImageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <img id="previewImage" src="" class="w-100 rounded">
            </div>
        </div>
    </div>
    {{-- Hiển thị phân trang --}}
    @if($sinhviens->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if($sinhviens->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $sinhviens->previousPageUrl() }}" aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $current = $sinhviens->currentPage();
                    $last = $sinhviens->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp

                {{-- First Page Link --}}
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $sinhviens->url(1) }}">1</a>
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
                            <a class="page-link" href="{{ $sinhviens->url($i) }}">{{ $i }}</a>
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
                        <a class="page-link" href="{{ $sinhviens->url($last) }}">{{ $last }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if($sinhviens->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $sinhviens->nextPageUrl() }}" aria-label="Next">
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
        Hiển thị {{ ($sinhviens->currentPage() - 1) * $sinhviens->perPage() + 1 }} 
        đến {{ min($sinhviens->currentPage() * $sinhviens->perPage(), $sinhviens->total()) }} 
        của {{ $sinhviens->total() }} kết quả
    </div>
    @endif
</div>

<!-- modal upload ảnh -->
<div class="modal fade" id="trainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Train ảnh sinh viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="trainForm">
                    @csrf
                    <input type="hidden" name="ma_sv" id="ma_sv">

                    <input type="file" name="hinh_anh" class="form-control mb-3" required>

                    <button type="submit" class="btn btn-success w-100">
                        Upload & Train
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
            resetImages(svId);
        });
    });

    function previewImage(url) {
        document.getElementById('previewImage').src = url;
    }

    window.csrfToken = "{{ csrf_token() }}";

    async function loadImages(svId) {
        const container = document.getElementById('imageContainer' + svId);

        if (!container) return;

        container.innerHTML = `
        <div class="text-center w-100 py-3">
            <div class="spinner-border text-primary"></div>
        </div>`;

        const url = "{{ route('sinhvien.anhDaTrain', ':id') }}".replace(':id', svId);
        const res = await fetch(url);
        // const res = await fetch(`/admin/sinhvien/${svId}/images`);

        if (!res.ok) {
            container.innerHTML = "Lỗi tải ảnh!";
            return;
        }

        const data = await res.json();

        let html = '';

        if (data.length === 0) {
                container.innerHTML = "<p class='text-center'>Chưa có ảnh</p>";
                return;
            }

        data.forEach(img => {
            html += `
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <img src="${img.url}" 
                        class="card-img-top"
                        style="height: 200px; object-fit: cover; cursor: pointer;"
                        onclick="previewImage('${img.url}')"
                        data-bs-toggle="modal"
                        data-bs-target="#previewImageModal">

                    <div class="card-body p-2 text-center">
                        <small class="text-muted d-block mb-2">
                            Trạng thái:
                            <span class="badge ${img.trang_thai === 'trained' ? 'bg-success' : 'bg-warning'}">
                                ${img.trang_thai}
                            </span>
                        </small>

                        <button type="button"
                            class="btn btn-sm btn-outline-danger w-100"
                            onclick="deleteImage(event, ${img.id}, ${svId})">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>
            `;
        });

        container.innerHTML = html;
        container.dataset.loaded = true;
    }

    function openTrainModal(ma_sv, svId){
        document.getElementById('ma_sv').value = ma_sv;
        document.getElementById('trainForm').dataset.svId = svId;
        new bootstrap.Modal(document.getElementById('trainModal')).show();
    }

    document.getElementById('trainForm').addEventListener('submit', async function(e){
        e.preventDefault();

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

                await new Promise(r => setTimeout(r, 500));

                await loadImages(svId);

                bootstrap.Modal.getInstance(document.getElementById('trainModal')).hide();
            }

        } catch (err) {
            alert("Lỗi!");
            console.error(err);
        }
    });

    function deleteImage(event, id, svId) {
        event.preventDefault();
        event.stopPropagation();

        if (!confirm("Bạn chắc chắn muốn xóa?")) return;

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
        });
    }
</script>

@endsection
@push('styles')
<style>
    .table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
    }
    
    .badge {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    /* Style cho pagination */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .page-link {
        color: var(--primary-color);
    }
    
    .page-link:hover {
        color: #0d62c9;
    }
    
    /* Mobile table scroll hint */
    .scroll-hint {
        position: sticky;
        left: 0;
        bottom: 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        animation: fadeOut 3s forwards;
        animation-delay: 2s;
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>
@endpush
@push('styles')
<style>
    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-item {
        margin: 0 2px;
    }
    
    .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        color: var(--primary-color);
        border: 1px solid #dee2e6;
        border-radius: 4px;
        min-width: 32px;
        text-align: center;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: var(--primary-color);
        transform: translateY(-1px);
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 2px 4px rgba(var(--primary-color-rgb, 13, 110, 253), 0.2);
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .pagination-sm .page-link {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    
    /* Hiệu ứng hover */
    .page-link i {
        font-size: 0.7rem;
    }
    
    /* Mobile responsive */
    @media (max-width: 576px) {
        .page-link {
            padding: 0.15rem 0.3rem;
            min-width: 28px;
            font-size: 0.75rem;
        }
        
        .pagination-sm .page-link {
            padding: 0.15rem 0.25rem;
            font-size: 0.7rem;
        }
    }
    
    /* Info text */
    .pagination-info {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
</style>
@endpush