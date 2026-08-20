@extends('layouts.main-layout')

@section('content')

<div class="container mt-3">
    <div id="alert-container" class="mt-2"></div>
    <h2 class="mb-2" style="font-size:1.2rem">
        🧾 Danh sách sinh viên phòng {{ $lichThi->phong }} – {{ $lichThi->monHoc->ten_mon }}
    </h2>

    <div class="action-buttons mb-2">
        <a href="{{ route('lichthi.index', request()->query()) }}" class="btn btn-custom btn-secondary">
            <i class="fa-solid fa-list"></i> Danh sách lịch thi
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
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Lớp</th>
                        <th>Điểm danh</th>
                        <th>Kết quả</th>
                        <th>Độ chính xác</th>
                        <th>Thời gian</th>
                        <th>Hình thức</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sinhViens as $item)
                        <tr data-id="{{ $item->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->sinhVien->ma_sv }}</td>
                            <td class="text-start">{{ $item->sinhVien->ho_ten }}</td>
                            <td >{{ $item->sinhVien->lop }}</td>
                            <td>
                                <input type="checkbox" class="form-check-input toggle-diemdanh" disabled
                                    data-id="{{ $item->id }}" {{ $item->ket_qua === 'hợp lệ' ? 'checked' : '' }}
                            </td>
                            <td class="col-ketqua">{{ $item->ket_qua ?? 'Chưa có' }}</td>
                            <td class="col-dochinhxac">{{ $item->do_chinh_xac ?? '-' }}</td>
                            <td class="col-thoigian">{{ $item->thoi_gian_dd ?? '-' }}</td>
                            <td class="col-hinhthuc">{{ $item->hinh_thuc_dd ?? '-' }}</td>
                            <td>{{ $item->ghi_chu_text}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<style>
#scrollTopBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    background-color: #0d6efd;
    color: white;
    border: none;
    padding: 10px 14px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#scrollTopBtn:hover {
    background-color: #0b5ed7;
}
.action-buttons {
    position: sticky;
    top: 0;
    z-index: 100;

    display: inline-block;  
    background: transparent; 
    padding: 0;
}
</style>
<button id="scrollTopBtn" title="Lên đầu trang">
    <i class="fa-solid fa-arrow-up"></i>
</button>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const scrollBtn = document.getElementById("scrollTopBtn");
    
    // class="content" từ layout
    const scrollContainer = document.querySelector(".content"); 

    if (scrollContainer) {
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