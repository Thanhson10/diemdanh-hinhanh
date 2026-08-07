@extends('layouts.main-layout')

@section('content')
<div class="container mt-5">
    <div class="col-md-4 offset-md-4 card p-4 shadow">
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
        <h4 class="text-center mb-3">Đăng nhập</h4>

        <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="email" value="{{old('email')}}" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            @if ($errors->has('login_error'))
                <div class="text-danger small mb-2 text-center">{{ $errors->first('login_error') }}</div>
            @endif

            <button id="loginBtn" type="submit" class="btn btn-primary w-100">
                <span id="btnText">Đăng nhập</span>
                <span id="btnLoading" class="d-none">
                    <span class="spinner-border spinner-border-sm"></span> Đang xử lý...
                </span>
            </button>
            <div class="text-center mt-2">
                <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
            </div> 
        </form>
    </div>
</div>

{{-- <script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    const btn = document.getElementById('loginBtn');
    const text = document.getElementById('btnText');
    const loading = document.getElementById('btnLoading');

    // 👉 bật loading
    btn.disabled = true;
    text.classList.add('d-none');
    loading.classList.remove('d-none');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        // lỗi validation → render lại
        const html = await response.text();
        document.body.innerHTML = html;

    } catch (error) {
        alert("❌ Không có kết nối mạng!");

        // 👉 tắt loading nếu lỗi mạng
        btn.disabled = false;
        text.classList.remove('d-none');
        loading.classList.add('d-none');
    }
});
</script>
<style>
.spinner-border {
    vertical-align: middle;
}
</style> --}}
@endsection
