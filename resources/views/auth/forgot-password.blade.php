@extends('layouts.main-layout')

@section('content')
<div class="container mt-5">
    <div class="col-md-6 offset-md-3 card p-4 shadow">
        <h4 class="text-center mb-4">Reset mật khẩu</h4>

         @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" value="{{old('email')}}" name="email" class="form-control" required>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Xác nhận</button>
        </form>
    </div>
</div>
@endsection
