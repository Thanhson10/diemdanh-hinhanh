@extends('layouts.main-layout')
@section('search')
   
@endsection
@section('content')
<div class="container mt-4">
    <h3>➕ Thêm sinh viên</h3>
    <a href="{{ route('sinhvien.index') }}" class="btn btn-secondary mt-2">Trở về</a>
    <form action="{{ route('sinhvien.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-2">
            <label>Mã sinh viên</label>
            <input type="text" name="ma_sv" value="{{ old('ma_sv') }}" class="form-control" required>
            @error('ma_sv')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <label>Họ tên</label>
            <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" class="form-control" required>
            @error('ho_ten')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-2">
            <label>Lớp</label>
            <input type="text" name="lop" value="{{ old('lop') }}" class="form-control" required>
            @error('lop')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-success mt-2">Lưu</button>
        
    </form>
</div>
@endsection
