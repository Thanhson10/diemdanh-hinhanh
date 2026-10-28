@extends('layouts.main-layout')

@section('title', 'Thông tin tài khoản - Giảng viên')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="edu-card shadow-sm">
                {{-- Header Card --}}
                <div class="p-4 text-center text-white" style="background: var(--edu-gradient);">
                    @php
                        $shortName = mb_substr($user->ho_ten ?? 'GV', 0, 1, 'UTF-8');
                    @endphp
                    <div class="mx-auto mb-3 shadow" style="width: 72px; height: 72px; border-radius: 50%; background: #ffffff; color: var(--edu-primary); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800;">
                        {{ $shortName }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->ho_ten }}</h4>
                    <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-semibold">
                        {{ $user->vai_tro === 'admin' ? 'Quản trị viên hệ thống' : 'Giảng viên' }}
                    </span>
                </div>

                <div class="card-body p-4 bg-white">
                    <h5 class="fw-bold mb-3 pb-2 border-bottom text-muted small text-uppercase">
                        <i class="fa-solid fa-address-card me-2 text-primary"></i>Thông tin hồ sơ công tác
                    </h5>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted fw-semibold py-2" style="width: 35%;">Mã Giảng viên:</th>
                            <td class="py-2"><strong class="text-primary">{{ $user->ma_gv ?? 'Chưa cập nhật' }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Họ và tên:</th>
                            <td class="py-2 fw-semibold">{{ $user->ho_ten }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Email liên hệ:</th>
                            <td class="py-2 text-break">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Vai trò:</th>
                            <td class="py-2">
                                <span class="edu-badge {{ $user->vai_tro === 'admin' ? 'edu-badge-danger' : 'edu-badge-primary' }}">
                                    <i class="fa-solid {{ $user->vai_tro === 'admin' ? 'fa-shield-halved' : 'fa-chalkboard-user' }}"></i>
                                    {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Giảng viên coi thi' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-semibold py-2">Trạng thái:</th>
                            <td class="py-2">
                                <span class="edu-badge edu-badge-success">
                                    <i class="fa-solid fa-circle-check"></i> Đang hoạt động
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div class="d-flex justify-content-center gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('auth.changePassword') }}" class="btn btn-primary px-4">
                            <i class="fa-solid fa-key me-1"></i> Đổi mật khẩu
                        </a>
                        <a href="{{ route('home.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="fa-solid fa-house me-1"></i> Trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
