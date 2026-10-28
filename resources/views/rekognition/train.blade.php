@extends('layouts.main-layout')

@section('title', 'Huấn luyện khuôn mặt sinh viên - AI Rekognition')

@section('content')
<div class="container-fluid px-0">
    {{-- Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--edu-text); font-size: 20px;">
                <i class="fa-solid fa-camera-rotate text-primary me-2"></i>Huấn Luyện Khuôn Mặt Sinh Viên
            </h3>
            <p class="text-muted small mb-0">Tải lên và đồng bộ dữ liệu nhận diện gương mặt AI phục vụ điểm danh tự động</p>
        </div>
        <div>
            <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-users"></i>
                <span>Danh sách sinh viên</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Cột Form Tải Lên --}}
        <div class="col-12 col-lg-7">
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-3">
                    <h5 class="edu-card-title m-0">
                        <i class="fa-solid fa-cloud-arrow-up text-primary"></i> Tải ảnh lên và huấn luyện
                    </h5>
                </div>
                <div class="edu-card-body p-4">
                    {{-- Hướng dẫn định dạng --}}
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="fa-solid fa-circle-info me-1 fs-6"></i>
                        <strong>Quy tắc đặt tên file:</strong> Tên file ảnh cần bắt đầu bằng <strong>Mã số sinh viên (MSSV)</strong>.<br>
                        <em>Ví dụ:</em> <code>DH52201371_1.jpg</code>, <code>DH52201371-front.png</code>. Tối đa 2MB/ảnh.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Chọn tệp hình ảnh khuôn mặt:</label>
                        <input type="file" id="images" class="form-control form-control-lg" accept="image/jpeg,image/png" multiple required>
                        <div id="fileError" class="text-danger small mt-2"></div>
                    </div>

                    <button class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" id="uploadBtn">
                        <i class="fa-solid fa-bolt me-2"></i>Tiến Hành Tải Lên & Huấn Luyện AI
                    </button>
                </div>
            </div>

            {{-- Terminal / Log Box --}}
            <div class="edu-card shadow-sm mt-4">
                <div class="edu-card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h5 class="edu-card-title m-0">
                        <i class="fa-solid fa-terminal text-primary"></i> Tiến trình xử lý (Logs)
                    </h5>
                    <button class="btn btn-sm btn-link text-muted text-decoration-none p-0" onclick="document.getElementById('logs').innerHTML=''">
                        <i class="fa-solid fa-trash-can me-1"></i> Xóa log
                    </button>
                </div>
                <div class="edu-card-body p-3">
                    <div id="logBox" style="min-height: 180px; max-height: 320px; overflow-y: auto; background: #0f172a; color: #f8fafc; padding: 14px; border-radius: 10px; font-family: monospace; font-size: 13px;">
                        <div class="text-secondary fst-italic">// Trạng thái tiến trình sẽ hiển thị trực tiếp tại đây khi bạn bấm bắt đầu...</div>
                        <div id="logs" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cột Tiêu chuẩn ảnh chất lượng --}}
        <div class="col-12 col-lg-5">
            <div class="edu-card shadow-sm">
                <div class="edu-card-header bg-white border-bottom p-3">
                    <h5 class="edu-card-title m-0">
                        <i class="fa-solid fa-circle-question text-primary"></i> Tiêu chuẩn ảnh khuôn mặt
                    </h5>
                </div>
                <div class="edu-card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="bg-success-subtle text-success p-2 rounded-circle fs-5 flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Chính diện, rõ nét</strong>
                            <span class="text-muted small">Khuôn mặt chụp thẳng, mắt nhìn vào ống kính, không bị mờ nhòe hay ngược sáng.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="bg-success-subtle text-success p-2 rounded-circle fs-5 flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Ánh sáng đầy đủ</strong>
                            <span class="text-muted small">Môi trường đủ sáng, không bị bóng tối che khuất một nửa khuôn mặt.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="bg-danger-subtle text-danger p-2 rounded-circle fs-5 flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Không che chắn khuôn mặt</strong>
                            <span class="text-muted small">Không đeo khẩu trang, kính râm đen hoặc đội mũ che khuất trán và chân mày.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle text-primary p-2 rounded-circle fs-5 flex-shrink-0" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark">Tối đa 2 ảnh mẫu / sinh viên</strong>
                            <span class="text-muted small">Mỗi sinh viên chỉ cần 1 đến 2 ảnh mẫu để hệ thống huấn luyện đặc trưng khuôn mặt tối ưu.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function uploadTrain(url) {
    const files = Array.from(document.getElementById('images').files);
    const logBox = document.getElementById('logs');
    const uploadBtn = document.getElementById('uploadBtn');

    if (files.length === 0) {
        Swal.fire("Chưa chọn tệp", "Thầy/Cô vui lòng chọn ít nhất 1 ảnh khuôn mặt để huấn luyện.", "warning");
        return;
    }

    logBox.innerHTML = "";
    let total = files.length;
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý tải lên...';

    async function uploadSingle(file, index) {
        let stt = index + 1;
        let fileNameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.'));

        let match = fileNameWithoutExt.match(/[A-Z]{2}\d{8}/i);
        if (!match) {
            logBox.innerHTML += `
                <div class="text-warning my-1">
                    ⚠️ [${stt}/${total}] <strong>${file.name}</strong>: Không nhận diện được định dạng MSSV ở tên tệp.
                </div>
            `;
            return;
        }

        let ma_sv = match[0].toUpperCase();

        let formData = new FormData();
        formData.append('ma_sv', ma_sv);
        formData.append('hinh_anh', file);

        logBox.innerHTML += `
            <div id="log-${index}" class="text-info my-1">
                ⏳ [${stt}/${total}] Đang tải lên và phân tích ${file.name} (MSSV: ${ma_sv})...
            </div>
        `;

        try {
            const res = await fetch(url, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: formData
            });

            const result = await res.json();
            const logEl = document.getElementById(`log-${index}`);

            if (result.success) {
                logEl.innerHTML = `<span style="color: #4ade80;">✅ [${stt}/${total}] ${file.name} (${ma_sv}): ${result.message}</span>`;
            } else {
                let errorMsg = result.message;
                if (errorMsg.includes('Không tồn tại MSSV')) {
                    errorMsg = `Không tìm thấy sinh viên có MSSV: ${ma_sv}`;
                }
                logEl.innerHTML = `<span style="color: #f87171;">❌ [${stt}/${total}] ${file.name}: ${errorMsg}</span>`;
            }
        } catch (e) {
            const logEl = document.getElementById(`log-${index}`);
            logEl.innerHTML = `<span style="color: #f87171;">❌ [${stt}/${total}] ${file.name}: Lỗi kết nối máy chủ.</span>`;
        }
    }

    const limit = 3;
    for (let i = 0; i < files.length; i += limit) {
        const chunk = files.slice(i, i + limit);
        await Promise.all(
            chunk.map((file, idx) => uploadSingle(file, i + idx))
        );
    }

    logBox.innerHTML += `<div class="mt-2 pt-2 border-top border-secondary text-white fw-bold">🎉 Đã hoàn tất xử lý ${total} tệp ảnh!</div>`;
    uploadBtn.disabled = false;
    uploadBtn.innerHTML = '<i class="fa-solid fa-bolt me-2"></i>Tiến Hành Tải Lên & Huấn Luyện AI';
}

document.getElementById('uploadBtn').onclick = () =>
    uploadTrain("{{ route('rekognition.train.ajax') }}");

document.getElementById('images').addEventListener('change', function () {
    const maxSize = 2 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png'];
    const dt = new DataTransfer();
    const errors = [];

    for (const file of this.files) {
        if (!allowedTypes.includes(file.type)) {
            errors.push(`❌ ${file.name}: Định dạng không hợp lệ (chỉ hỗ trợ JPG/PNG)`);
            continue;
        }

        if (file.size > maxSize) {
            errors.push(`❌ ${file.name}: Dung lượng quá 2MB`);
            continue;
        }

        dt.items.add(file);
    }

    this.files = dt.files;
    document.getElementById('fileError').innerHTML = errors.length ? errors.join('<br>') : '';
});
</script>
@endsection