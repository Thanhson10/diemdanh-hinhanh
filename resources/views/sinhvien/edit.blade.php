@extends('layouts.main-layout')

@section('content')
<div class="container mt-4">
    <h3>✏️ Sửa thông tin sinh viên</h3>
    <form action="{{ route('sinhvien.update', $sinhvien->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label>Mã sinh viên</label>
            <input type="text" name="ma_sv" value="{{ old('ma_sv',$sinhvien->ma_sv) }}" class="form-control" required>
            @error('ma_sv')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <label>Họ tên</label>
            <input type="text" name="ho_ten" value="{{ $sinhvien->ho_ten }}" class="form-control" required>
            @error('ho_ten')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <label for="">Lớp</label>
            <input type="text" name="lop" value="{{ $sinhvien->lop }}" class="form-control" required>
            @error('lop')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email',$sinhvien->email) }}" class="form-control" required>
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-primary mt-2">Cập nhật</button>
        <a href="{{ route('sinhvien.index', request()->query())  }}" class="btn btn-secondary mt-2">Quay lại</a>
    </form>
</div>
@endsection
