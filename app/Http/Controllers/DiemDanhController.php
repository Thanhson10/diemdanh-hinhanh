<?php

namespace App\Http\Controllers;

use App\Models\DiemDanh;
use App\Models\LichThi;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DiemDanhImport;
use Illuminate\Support\Facades\Auth;


class DiemDanhController extends Controller
{
    public function index(Request $request)
    {   
        $user = Auth::user();
        $query = LichThi::with('monHoc');
        LichThi::whereIn('trang_thai', ['chua_dien_ra', 'dang_dien_ra'])
            ->get()
            ->each(function ($lichThi) {
                $lichThi->capNhatTrangThai();
            });

        // Nếu đã đăng nhập và là admin hoặc giang viên -> chỉ xem lịch thi được phân công
        if ($user && in_array($user->vai_tro, ['giang_vien', 'admin'])) {
            $query->whereHas('phanCongGVs', function ($q) use ($user) {
                $q->where('giang_vien_id', $user->id);
            });
        }

        if ($request->filled('ten_mon')) {
            $query->whereHas('monHoc', function ($q) use ($request) {
                $q->where('ten_mon', 'like', "%{$request->ten_mon}%");
            });
        }

        if ($request->filled('phong')) {
            $query->where('phong', 'like', "%{$request->phong}%");
        }

        if ($request->filled('ngay')) {
            $query->where('ngay_thi', $request->ngay);
        }

        if ($request->filled('ky_thi')) {
            $query->where('ky_thi', 'like', "%{$request->ky_thi}%");
        }
        if ($request->filled('nam_hoc')) {
            $query->where('nam_hoc', 'like', "%{$request->nam_hoc}%");
        }
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        $lichThis = $query->orderByRaw("
            FIELD(trang_thai, 'dang_dien_ra', 'chua_dien_ra', 'da_ket_thuc')")
            ->orderBy('ngay_thi', 'desc')->orderBy('gio_thi', 'desc')->paginate(10)->withQueryString();;
    
        return view('diemdanh.index', compact('lichThis'),['hideSearch' => true])
            ->with('search', $request->search);
    }


    public function show(Request $request, $id)
    {
        $lichThi = LichThi::findOrFail($id);
        $lichThi->capNhatTrangThai();

        // Tạo query, chưa get()
        $sinhViensQuery = DiemDanh::with('sinhVien')->where('lich_thi_id', $id);

        // Tìm kiếm MSSV hoặc họ tên
        if ($request->filled('search')) {
            $search = $request->search;
            $sinhViensQuery->whereHas('sinhVien', function($q) use ($search) {
                $q->where('ma_sv', 'like', "%{$search}%")
                ->orWhere('ho_ten', 'like', "%{$search}%")
                ->orWhere('lop', 'like', "%{$search}%");
            });
        }

        // Lọc chỉ những người chưa điểm danh
        if ($request->has('chua_diem_danh') && $request->chua_diem_danh == '1') {
            $sinhViensQuery->whereNull('ket_qua');
        }

        // Sắp xếp theo id giảm dần (mới nhất lên trước)
        $sinhViens = $sinhViensQuery->orderBy('sinh_vien_id', 'asc')->get();

        if (request('type') === 'tientrinh') {
            return view('diemdanh.showtientrinh', compact('lichThi', 'sinhViens'),['hideSearch' => true]);
        }

        return view('diemdanh.show', compact('lichThi', 'sinhViens'),['hideSearch' => true]);
    }

    // Import danh sách sinh viên tham gia thi
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new DiemDanhImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import danh sách điểm danh thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi import: ' . $e->getMessage());
        }
    }

    // Xóa một bản ghi điểm danh
    public function destroy($id)
    {
        $record = DiemDanh::findOrFail($id);
        $record->delete();
        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:diem_danhs,id',
                'checked' => 'required|boolean',
            ]);

            $diemDanh = DiemDanh::findOrFail($request->id);

            if ($request->checked) {
                $diemDanh->update([
                    'ket_qua' => 'hợp lệ',
                    'do_chinh_xac' => 100,
                    'thoi_gian_dd' => now(),
                    'hinh_thuc_dd' => 'Thủ công',
                ]);
                return response()->json(['success' => true, 'message' => 'Điểm danh thành công, lưu ý để lại ghi chú!']);
            } else {
                $diemDanh->update([
                    'ket_qua' => null,
                    'do_chinh_xac' => null,
                    'thoi_gian_dd' => null,
                    'hinh_thuc_dd' => null,
                ]);
                return response()->json(['success' => true, 'message' => 'Đã hủy điểm danh']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function anhTrain($id)
    {
        $sinhVien = SinhVien::with('danhSachAnhTrain')->findOrFail($id);

        return response()->json(
            $sinhVien->danhSachAnhTrain->map(function($img){
                return [
                    'url' => $img->hinh_anh_url 
                ];
            })
        );
    }
    public function updateGhiChu(Request $request, $id)
    {
        $diemDanh = DiemDanh::findOrFail($id);

        if ($request->ghi_chu === null || $request->ghi_chu === '') {
            $diemDanh->ghi_chu = null;
            $diemDanh->save();

            return response()->json([
                'success' => true
            ]);
        }

        // chỉ cho lưu nếu hợp lệ
        if ($diemDanh->hinh_thuc_dd !== 'Thủ công' || $diemDanh->ket_qua !== 'hợp lệ') {
            return response()->json([
                'success' => false,
                'message' => 'Không hợp lệ'
            ], 400);
        }

        $diemDanh->ghi_chu = $request->ghi_chu;
        $diemDanh->save();

        return response()->json([
            'success' => true
        ]);
    }
}
