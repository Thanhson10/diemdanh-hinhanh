@extends('layouts.main-layout')

@section('content')
<div class="container mt-5">

    {{-- Hiển thị thông báo --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <a href="{{ route('home.index') }}" class="btn btn-secondary mt-2 mb-3">Trở về</a>
    
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Xóa dữ liệu sinh viên khỏi Rekognition & S3</h4>
        </div>

        <div class="card-body">
            {{-- Form nhập MSSV --}}
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="form-group mb-3">
                    <label for="studentId" class="fw-bold">Mã số sinh viên (MSSV)</label>
                    <input type="text" id="studentId" class="form-control" placeholder="Nhập MSSV" required autocomplete="off">
                </div>

                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Xóa dữ liệu khuôn mặt
                </button>
            </form>
        </div>
    </div>

</div>

{{-- Script xử lý submit form --}}
<script>
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        let rawId = document.getElementById('studentId').value;
        let id = rawId.trim().toUpperCase(); 

        if (!id) {
            e.preventDefault();
            alert("Vui lòng nhập MSSV hợp lệ!");
            return;
        }

        let isConfirmed = confirm('⚠️ Bạn có chắc chắn muốn xóa dữ liệu khuôn mặt của sinh viên ' + id + ' không?\nHành động này không thể hoàn tác!');
        
        if (!isConfirmed) {
            e.preventDefault();
            return;
        }

        let routeUrl = "{{ route('rekognition.delete', ['studentId' => 'PLACEHOLDER']) }}";
        
        // Thay thế chữ 'PLACEHOLDER' bằng MSSV thật do người dùng nhập
        this.action = routeUrl.replace('PLACEHOLDER', encodeURIComponent(id));
    });
</script>

@endsection