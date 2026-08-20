@extends('layouts.main-layout')

@section('content')
<div class="container mt-4">
    <h3>📤 Upload & Train khuôn mặt sinh viên</h3>
    <hr>

    <div class="mb-3">
        <label>Chọn ảnh khuôn mặt (Đảm bảo MSSV nằm ở đầu tên file, ≤2MB, JPG/PNG, VD: DH52201371_1.jpg, dh52201371-2.png)</label>
        <input type="file" id="images" class="form-control" accept="image/jpeg,image/png" multiple required>
        <small id="fileError" class="text-danger"></small>
    </div>

    <button class="btn btn-primary" id="uploadBtn">🚀 Upload & Train</button>

    <hr>

    <div id="logBox" class="mt-3" style="max-height:300px; overflow-y:auto; background:#f8f9fa; padding:10px; border-radius:5px;">
        <b>📄 Log:</b>
        <div id="logs"></div>
    </div>
</div>

<script>
async function uploadTrain(url) {
    const files = Array.from(document.getElementById('images').files);
    const logBox = document.getElementById('logs');

    if (files.length === 0) {
        alert("⚠️ Vui lòng chọn ảnh!");
        return;
    }

    logBox.innerHTML = "";
    let total = files.length;

    // Hàm xử lý 1 file
    async function uploadSingle(file, index) {
        let stt = index + 1;
        let fileNameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.'));

        let match = fileNameWithoutExt.match(/[A-Z]{2}\d{8}/i);
        if (!match) {
            logBox.innerHTML += `
                <div class="text-danger">
                    ❌ [${stt}/${total}] ${file.name}: Không nhận diện được MSSV
                </div>
            `;
            return;
        }

        let ma_sv = match[0].toUpperCase();

        let formData = new FormData();
        formData.append('ma_sv', ma_sv);
        formData.append('hinh_anh', file);

        logBox.innerHTML += `
            <div id="log-${index}">
                ▶️ [${stt}/${total}] Đang xử lý ${file.name} ...
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
                logEl.innerHTML = `✅ [${stt}/${total}] ${file.name}: ${result.message}`;
                logEl.classList.add('text-success');
            } else {
                let errorMsg = result.message;

                if (errorMsg.includes('Không tồn tại MSSV')) {
                    errorMsg = `Không tồn tại MSSV: ${ma_sv}`;
                }

                logEl.innerHTML = `❌ [${stt}/${total}] ${file.name}: ${errorMsg}`;
                logEl.classList.add('text-danger');
            }
        } catch (e) {
           const logEl = document.getElementById(`log-${index}`);
            if (!navigator.onLine) {
                logEl.innerHTML = `❌ [${stt}/${total}] ${file.name}: Mất kết nối internet`;
            } else {
                logEl.innerHTML = `🔥 [${stt}/${total}] ${file.name}: Không thể gửi request`;
            }
            logEl.classList.add('text-danger');
        }
    }

    const limit = 3;

    for (let i = 0; i < files.length; i += limit) {
        const chunk = files.slice(i, i + limit);

        await Promise.all(
            chunk.map((file, idx) => uploadSingle(file, i + idx))
        );
    }

    logBox.innerHTML += `<div><b>Hoàn tất ${total} ảnh!</b></div>`;
}

document.getElementById('uploadBtn').onclick = () =>
    uploadTrain("{{ route('rekognition.train.ajax') }}");
</script>

<script>
document.getElementById('images').addEventListener('change', function () {
    const maxSize = 2 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png'];

    const dt = new DataTransfer();
    const errors = [];

    for (const file of this.files) {
        if (!allowedTypes.includes(file.type)) {
            errors.push(`❌ ${file.name}: sai định dạng`);
            continue;
        }

        if (file.size > maxSize) {
            errors.push(`❌ ${file.name}: vượt quá 2MB`);
            continue;
        }

        dt.items.add(file);
    }

    this.files = dt.files;

    document.getElementById('fileError').innerHTML =
        errors.length ? errors.join('<br>') : '';
});
</script>
@endsection