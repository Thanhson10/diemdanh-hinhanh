
@if($sinhViens->isEmpty())
    <tr>
        <td colspan="9" class="text-center py-4">
            <div class="alert alert-warning mb-0">Không có sinh viên nào trong phòng thi này.</div>
        </td>
    </tr>
@else
    @foreach ($sinhViens as $item)
        <tr data-id="{{ $item->id }}">
            <td>{{ $loop->iteration }}</td>
            <td>
                <button class="btn btn-sm btn-info btn-xem-anh" 
                    data-id="{{ $item->sinhVien->id }}">
                    👁 Xem
                </button>
            </td>
            <td>{{ $item->sinhVien->ma_sv }}</td>
            <td>{{ $item->sinhVien->ho_ten }}</td>
            <td>{{ $item->sinhVien->lop }}</td>
            <td>
                <input type="checkbox" class="form-check-input toggle-diemdanh"
                    data-id="{{ $item->id }}" {{ $item->ket_qua === 'hợp lệ' ? 'checked' : '' }}
                    @if($lichThi->trang_thai === 'da_ket_thuc') disabled @endif>
            </td>
            <td class="col-ketqua">{{ $item->ket_qua ?? 'Chưa có' }}</td>
            <td class="col-dochinhxac">{{ $item->do_chinh_xac ?? '-' }}</td>
            <td class="col-thoigian">{{ $item->thoi_gian_dd ?? '-' }}</td>
            <td class="col-hinhthuc">{{ $item->hinh_thuc_dd ?? '-' }}</td>
            <td>
                <select class="form-select form-select-sm ghi-chu-select"   
                    data-id="{{ $item->id }}" disabled>

                    <option value="">-- Chọn --</option>

                    <option value="chua_co_anh"
                        {{ $item->ghi_chu == 'chua_co_anh' ? 'selected' : '' }}>
                            Chưa có ảnh
                    </option>

                    <option value="mat_khong_khop"
                        {{ $item->ghi_chu == 'mat_khong_khop' ? 'selected' : '' }}>
                            Không nhận diện được
                    </option>

                    <!-- <option value="anh_mo"
                        {{ $item->ghi_chu == 'anh_mo' ? 'selected' : '' }}>
                            Ảnh mờ
                    </option> -->

                </select>
            </td>
        </tr>
    @endforeach
@endif

