@extends('layouts.main-layout')

@section('content')
<div class="container-fluid px-3 py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="{{ route('lichthi.index') }}" class="btn btn-sm btn-edu btn-edu-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại lịch thi
                </a>
            </div>

            <!-- Form Card -->
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Tạo Lịch Thi Mới</h4>
                            <p class="text-muted small mb-0">Thiết lập ca thi, phòng thi và chỉ định môn học</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <form action="{{ route('lichthi.store') }}" method="POST">
                        @csrf

                        <!-- Section 1: Học phần -->
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-book me-2"></i>1. Thông Tin Học Phần & Đợt Thi
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">
                                    Môn thi học phần <span class="text-danger">*</span>
                                </label>
                                <select name="mon_hoc_id" class="select2 form-select" required style="width: 100%">
                                    <option value="">-- Chọn môn học --</option>
                                    @foreach($monhocs as $mon)
                                        <option value="{{ $mon->id }}" 
                                            {{ old('mon_hoc_id') == $mon->id ? 'selected' : '' }}>
                                            {{ $mon->ma_mon }} - {{ $mon->ten_mon }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mon_hoc_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Đợt / Kỳ thi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-award"></i></span>
                                    <input list="kythi_list" name="ky_thi" class="form-control" 
                                        placeholder="Nhập hoặc chọn kỳ thi..." 
                                        value="{{ old('ky_thi') }}" required>
                                </div>
                                <datalist id="kythi_list">
                                    <option value="Thi giữa kỳ HK1"></option>
                                    <option value="Thi cuối kỳ HK1"></option>
                                    <option value="Thi giữa kỳ HK2"></option>
                                    <option value="Thi cuối kỳ HK2"></option>
                                    <option value="Thi giữa kỳ HK3"></option>
                                    <option value="Thi cuối kỳ HK3"></option>
                                </datalist>
                                @error('ky_thi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">
                                    Năm học <span class="text-danger">*</span>
                                </label>
                                @php
                                    $year = now()->year;
                                    $options = [
                                        ($year - 1).'-'.$year,
                                        $year.'-'.($year + 1)
                                    ];
                                @endphp
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-calendar-alt"></i></span>
                                    <select name="nam_hoc" class="form-select" required>
                                        @foreach($options as $opt)
                                            <option value="{{ $opt }}" {{ old('nam_hoc') == $opt ? 'selected' : '' }}>
                                                {{ $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('nam_hoc')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 2: Thời gian thi -->
                        <h6 class="fw-bold text-primary mb-3 pt-3 border-top">
                            <i class="far fa-clock me-2"></i>2. Thời Gian Tổ Chức Thi
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">
                                    Ngày thi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="far fa-calendar-check"></i></span>
                                    <input type="date" name="ngay_thi" class="form-control" 
                                        value="{{ old('ngay_thi') }}" required>
                                </div>
                                @error('ngay_thi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">
                                    Giờ bắt đầu thi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="far fa-clock"></i></span>
                                    <input type="time" name="gio_thi" min="07:00" max="18:00" class="form-control" 
                                        value="{{ old('gio_thi') }}" required>
                                </div>
                                @error('gio_thi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">
                                    Thời lượng làm bài (phút) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-hourglass-half"></i></span>
                                    <input type="number" name="thoi_luong_thi" min="1" max="300" class="form-control" 
                                        value="{{ old('thoi_luong_thi', 60) }}" required>
                                </div>
                                @error('thoi_luong_thi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 3: Phòng thi -->
                        <h6 class="fw-bold text-primary mb-3 pt-3 border-top">
                            <i class="fas fa-door-open me-2"></i>3. Phòng Thi & Khu Nhà
                        </h6>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">
                                Chọn mã phòng thi <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2 align-items-center">
                                <div class="col-3 col-sm-2">
                                    <select id="type" class="form-select" required>
                                        <option value="">Khu</option>
                                        <option value="A" {{ old('phong_type') == 'A' || substr(old('phong', ''), 0, 1) == 'A' ? 'selected' : '' }}>Khu A</option>
                                        <option value="B" {{ old('phong_type') == 'B' || substr(old('phong', ''), 0, 1) == 'B' ? 'selected' : '' }}>Khu B</option>
                                        <option value="C" {{ old('phong_type') == 'C' || substr(old('phong', ''), 0, 1) == 'C' ? 'selected' : '' }}>Khu C</option>
                                    </select>
                                </div>

                                <div class="col-3 col-sm-2">
                                    <select id="num1" class="form-select" required></select>
                                </div>

                                <div class="col-3 col-sm-2">
                                    <select id="num2" class="form-select" required></select>
                                </div>

                                <div class="col-3 col-sm-2">
                                    <select id="num3" class="form-select" required>
                                        <option value=""></option>
                                        @for ($i = 1; $i <= 9; $i++)
                                            <option value="{{ $i }}" 
                                                {{ old('phong_last') == $i || substr(old('phong', ''), -1) == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-12 col-sm-4 text-sm-start mt-2 mt-sm-0">
                                    <span class="text-muted small">Mã phòng:</span>
                                    <span id="previewPhongText" class="badge-edu badge-edu-primary font-monospace fs-6 ms-1">
                                        {{ old('phong') ?: 'Chưa chọn' }}
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="phong" id="phong" value="{{ old('phong') }}">

                            @error('phong')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('lichthi.index') }}" class="btn btn-edu btn-edu-secondary">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-edu btn-edu-primary px-4">
                                <i class="fas fa-calendar-check me-1"></i> Tạo lịch thi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const type = document.getElementById('type');
    const num1 = document.getElementById('num1');
    const num2 = document.getElementById('num2');
    const num3 = document.getElementById('num3');
    const phong = document.getElementById('phong');
    const previewPhongText = document.getElementById('previewPhongText');

    // Lấy giá trị phòng cũ (nếu có)
    const oldPhong = "{{ old('phong') }}";

    function initializeDropdowns() {
        if (oldPhong && oldPhong.length >= 4) {
            const typeVal = oldPhong.charAt(0);
            const num1Val = oldPhong.charAt(1);
            const num2Val = oldPhong.charAt(2);
            const num3Val = oldPhong.charAt(3);
            
            type.value = typeVal;
            updateDropdowns();
            
            setTimeout(() => {
                num1.value = num1Val;
                num2.value = num2Val;
                num3.value = num3Val;
                updateHiddenValue();
            }, 100);
        } else {
            updateDropdowns();
        }
    }

    function updateDropdowns() {
        num1.innerHTML = "";
        num2.innerHTML = "";

        if (type.value === "A" || type.value === "B") {
            for (let i = 1; i <= 4; i++) {
                const selected = (oldPhong && oldPhong.charAt(1) == i && (type.value === "A" || type.value === "B")) ? 'selected' : '';
                num1.innerHTML += `<option value="${i}" ${selected}>${i}</option>`;
            }
            for (let i = 0; i <= 1; i++) {
                const selected = (oldPhong && oldPhong.charAt(2) == i && (type.value === "A" || type.value === "B")) ? 'selected' : '';
                num2.innerHTML += `<option value="${i}" ${selected}>${i}</option>`;
            }
        }

        if (type.value === "C") {
            for (let i = 1; i <= 9; i++) {
                const selected = (oldPhong && oldPhong.charAt(1) == i && type.value === "C") ? 'selected' : '';
                num1.innerHTML += `<option value="${i}" ${selected}>${i}</option>`;
            }
            num2.innerHTML = `<option value="0" ${oldPhong && oldPhong.charAt(2) == '0' && type.value === "C" ? 'selected' : ''}>0</option>`;
        }

        updateHiddenValue();
    }

    function updateHiddenValue() {
        if (type.value && num1.value && num2.value && num3.value) {
            const code = type.value + num1.value + num2.value + num3.value;
            phong.value = code;
            if (previewPhongText) previewPhongText.textContent = code;
        } else {
            if (previewPhongText) previewPhongText.textContent = 'Chưa chọn';
        }
    }

    type.onchange = updateDropdowns;
    num1.onchange = updateHiddenValue;
    num2.onchange = updateHiddenValue;
    num3.onchange = updateHiddenValue;

    document.addEventListener('DOMContentLoaded', initializeDropdowns);
</script>
@endsection