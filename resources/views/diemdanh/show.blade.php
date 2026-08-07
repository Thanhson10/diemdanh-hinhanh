@extends('layouts.main-layout')

@section('content')

<div class="container mt-3">
    <div id="alert-container" class="mt-2"></div>
    <h2 class="mb-2" style="font-size:1.2rem">
        🧾 Danh sách sinh viên phòng {{ $lichThi->phong }} – {{ $lichThi->monHoc->ten_mon }}
    </h2>

    <div class="action-buttons mb-2">
        <a href="{{ route('diemdanh.index') }}" class="btn btn-custom btn-secondary">
            <i class="fa-solid fa-list"></i> Danh sách phòng thi
        </a>

        <a href="{{ route('rekognition.index', [$lichThi->id]) }}" class="btn btn-custom btn-primary">
            <i class="fa-solid fa-video"></i> Điểm danh
        </a>
    </div>
    {{-- Thanh tìm kiếm --}}
    <form method="GET" action="{{ route('diemdanh.show', $lichThi->id) }}" class="mb-2 d-flex align-items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" 
            class="form-control" placeholder="🔍 Tìm MSSV hoặc tên sinh viên"
            style="max-width: 300px;">
        
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="chua_diem_danh" value="1" id="chuaDiemDanh"
                {{ request('chua_diem_danh') == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="chuaDiemDanh">Chưa điểm danh</label>
        </div>

        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        
        @if(request()->has('search') || request()->has('chua_diem_danh'))
            <a href="{{ route('diemdanh.show', $lichThi->id) }}" class="btn btn-outline-secondary">Xóa lọc</a>
        @endif
    </form>

    @if ($sinhViens->isEmpty())
        <div class="alert alert-warning">Không có sinh viên nào.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Ảnh</th>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Điểm danh</th>
                        <th>Kết quả</th>
                        <th>Độ chính xác</th>
                        <th>Thời gian</th>
                        <th>Hình thức</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sinhViens as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-xem-anh" 
                                    data-id="{{ $item->sinhVien->id }}">
                                    👁 Xem
                                </button>
                            </td>
                            <td>{{ $item->sinhVien->ma_sv }}</td>
                            <td class="text-start">{{ $item->sinhVien->ho_ten }}</td>
                            <td >{{ $item->sinhVien->lop }}</td>
                            <td>
                                <input type="checkbox" class="form-check-input toggle-diemdanh"
                                    data-id="{{ $item->id }}" {{ $item->ket_qua === 'hợp lệ' ? 'checked' : '' }}
                                    @if($lichThi->trang_thai === 'da_ket_thuc') disabled @endif
                            </td>
                            <td class="col-ketqua">{{ $item->ket_qua ?? 'Chưa có' }}</td>
                            <td class="col-dochinhxac">{{ $item->do_chinh_xac ?? '-' }}</td>
                            <td class="col-thoigian">{{ $item->thoi_gian_dd ?? '-' }}</td>
                            <td class="col-hinhthuc">{{ $item->hinh_thuc_dd ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                alertBox.innerHTML = `<strong>${type==='success'?'✅':'⚠️'}</strong> ${message}`;
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

                    const timeStr = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

                    row.querySelector('.col-ketqua').textContent = checked ? 'hợp lệ':'Chưa có';
                    row.querySelector('.col-dochinhxac').textContent = checked ? '100':'-';
                    row.querySelector('.col-thoigian').textContent = checked ? timeStr:'-';
                    row.querySelector('.col-hinhthuc').textContent = checked ? 'Thủ công' : '-';

                    row.style.transition = 'background-color 0.4s';
                    row.style.backgroundColor = checked ? '#d1e7dd' : '#f8d7da';
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
});

    document.querySelectorAll('.btn-xem-anh').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            let html = '';
            const res = await fetch(`/sinhvien/${id}/anh-train`);
            const data = await res.json();
            if (data.length === 0) {
                html = `<div class="text-muted">Không có ảnh train</div>`;
            }
            else { html = `
                <div style="
                    display:flex;
                    flex-direction:column;
                    gap:10px;
                    align-items:center;
                ">
            `;

            data.forEach(img => {
                html += `
                    <img src="${img.url}" 
                        style="
                            width:100%;
                            max-width:250px;
                            border-radius:10px;
                            object-fit:cover;
                            box-shadow:0 2px 8px rgba(0,0,0,0.15);
                        ">
                `;
            });

            html += `</div>`;
        }

            document.getElementById('modal-body-anh').innerHTML = html;

            new bootstrap.Modal(document.getElementById('modalAnh')).show();
        });
    });
</script>
<div class="modal fade" id="modalAnh" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ảnh đã train</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modal-body-anh">
        Loading...
      </div>
    </div>
  </div>
</div>
@endsection