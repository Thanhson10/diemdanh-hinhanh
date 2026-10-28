@extends('layouts.main-layout')

@section('content')
<div class="container-fluid px-3 py-2">
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-3">
            <i class="fas fa-exclamation-circle me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Teacher Info Banner & Action -->
    <div class="edu-card mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-left: 4px solid var(--primary);">
        <div class="p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Phân Công Coi Thi: {{ $giangvien->ho_ten }}</h5>
                    <div class="text-muted small">
                        Mã GV: <span class="fw-semibold text-primary font-monospace">{{ $giangvien->ma_gv }}</span> | 
                        Đã được phân công: <span class="badge-edu badge-edu-info">{{ $giangvien->lichthis->count() }} ca thi</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-edu btn-edu-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignedModal">
                    <i class="fas fa-calendar-check me-1"></i> Xem lịch đã nhận ({{ $giangvien->lichthis->count() }})
                </button>
                <a href="{{ route('giangvien.index') }}" class="btn btn-edu btn-edu-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Trở về
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card mb-4">
        <div class="filter-card-header">
            <i class="fas fa-search me-2 text-primary"></i>Tìm kiếm ca thi phù hợp để phân công
        </div>
        <form method="GET" action="{{ route('giangvien.phancong', $giangvien->id) }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Tên môn học</label>
                    <input type="text" name="ten_mon" value="{{ request('ten_mon') }}" class="form-control form-control-sm" placeholder="Tìm theo tên môn...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Phòng thi</label>
                    <input type="text" name="phong" value="{{ request('phong') }}" class="form-control form-control-sm" placeholder="Phòng (VD: A101)...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Ngày thi</label>
                    <input type="date" name="ngay" value="{{ request('ngay') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Giờ thi</label>
                    <input type="time" name="gio" value="{{ request('gio') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-edu btn-edu-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                    @if(count(request()->all()) > 0)
                        <a href="{{ route('giangvien.phancong', $giangvien->id) }}" class="btn btn-edu btn-edu-secondary btn-sm" title="Xóa lọc">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Exam Sessions Table -->
    <div class="edu-card">
        <div class="table-responsive">
            <table class="table-edu">
                <thead>
                    <tr>
                        <th>Học Phần / Môn Thi</th>
                        <th>Ngày Thi</th>
                        <th>Khung Giờ</th>
                        <th>Phòng Thi</th>
                        <th class="text-center">Cán Bộ Đã Phân Công</th>
                        <th class="text-center" style="width: 140px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lichthis as $lt)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $lt->monHoc->ten_mon ?? 'N/A' }}</div>
                            <small class="text-muted font-monospace">{{ $lt->monHoc->ma_mon ?? '' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                <i class="far fa-calendar-alt text-primary me-1"></i>
                                {{ \Carbon\Carbon::parse($lt->ngay_thi)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge-edu badge-edu-info">
                                <i class="far fa-clock me-1"></i>{{ substr($lt->gio_thi, 0, 5) }} ({{ $lt->thoi_luong_thi }}p)
                            </span>
                        </td>
                        <td>
                            <span class="badge-edu badge-edu-secondary font-monospace fw-bold">
                                {{ $lt->phong }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php $gvCount = $lt->giangviens->count(); @endphp
                            <span class="badge-edu {{ $gvCount >= 2 ? 'badge-edu-success' : 'badge-edu-warning' }}">
                                <i class="fas fa-user-check me-1"></i>{{ $gvCount }} Cán bộ
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $alreadyAssigned = $lt->giangviens->contains('id', $giangvien->id);
                            @endphp
                            @if($alreadyAssigned)
                                <span class="badge-edu badge-edu-success">
                                    <i class="fas fa-check me-1"></i>Đã nhận
                                </span>
                            @else
                                <form class="assignForm d-inline" data-lichid="{{ $lt->id }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-edu btn-edu-primary py-1 px-3">
                                        <i class="fas fa-user-plus me-1"></i> Phân công
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5>Không có ca thi nào phù hợp</h5>
                                <p>Thử nới lỏng điều kiện lọc ngày hoặc môn thi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Xem phân công --}}
<div class="modal fade" id="assignedModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Ca Thi Đã Phân Công Cho: {{ $giangvien->ho_ten }}</h6>
                        <small class="text-muted">Tổng cộng {{ $giangvien->lichthis->count() }} ca thi cần coi</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table-edu mb-0">
                        <thead>
                            <tr>
                                <th>Môn Thi</th>
                                <th>Ngày Thi</th>
                                <th>Giờ Thi</th>
                                <th>Phòng</th>
                                <th class="text-center" style="width: 100px;">Hủy Phân Công</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($giangvien->lichthis as $lt)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $lt->monHoc->ten_mon ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($lt->ngay_thi)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge-edu badge-edu-info">
                                            {{ substr($lt->gio_thi, 0, 5) }}
                                        </span>
                                    </td>
                                    <td><span class="font-monospace fw-bold">{{ $lt->phong }}</span></td>
                                    <td class="text-center">
                                        <button class="btn-action btn-action-danger btn-unassign"
                                            data-lichid="{{ $lt->id }}"
                                            title="Hủy phân công ca này">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="far fa-calendar-minus fa-2x mb-2 text-secondary"></i>
                                        <div>Giảng viên này chưa được phân công ca thi nào.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS xử lý phân công AJAX --}}
<script>
document.querySelectorAll('.assignForm').forEach(form => {
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const lichId = this.dataset.lichid;
        const csrfToken = this.querySelector('[name=_token]').value;
        const submitBtn = this.querySelector('button[type=submit]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;

        try {
            const res = await fetch(`/admin/giangvien/{{ $giangvien->id }}/assign/${lichId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if(data.status === 'success'){
                Swal.fire({
                    icon: 'success',
                    title: 'Phân công thành công',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(()=> location.reload());
            } else if(data.status === 'conflict'){
                Swal.fire('Trùng lịch coi thi', data.message, 'warning');
                submitBtn.disabled = false;
            } else {
                Swal.fire('Lỗi', data.message, 'error');
                submitBtn.disabled = false;
            }
        } catch (err) {
            Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ', 'error');
            submitBtn.disabled = false;
        }
    });
});

document.querySelectorAll('.btn-unassign').forEach(btn => {
    btn.addEventListener('click', async function () {
        const lichId = this.dataset.lichid;

        const confirmResult = await Swal.fire({
            title: 'Hủy phân công?',
            text: 'Bạn có chắc chắn muốn rút phân công ca thi này khỏi giảng viên?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Đồng ý hủy',
            cancelButtonText: 'Không'
        });

        if(!confirmResult.isConfirmed) return;

        try {
            const res = await fetch(
                `/admin/giangvien/{{ $giangvien->id }}/unassign/${lichId}`, 
                {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }
            );

            const data = await res.json();

            if(data.status === 'success'){
                Swal.fire({
                    icon: 'success',
                    title: 'Đã hủy phân công',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(()=> location.reload());
            } else {
                Swal.fire('Lỗi', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('Lỗi', 'Không thể kết nối đến máy chủ', 'error');
        }
    });
});
</script>
@endsection

