<?php

namespace App\Http\Controllers;

use App\Models\GiangVien;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Imports\GiangVienImport;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class GiangVienController extends Controller
{
    public function index(Request $request)
    {
        $query = GiangVien::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ho_ten', 'like', "%{$search}%");
        }

        $giangviens = $query->orderBy('is_active', 'desc')
        ->orderBy('ma_gv', 'desc')
        ->paginate(10)->withQueryString();

        return view('giangvien.index', compact('giangviens'),['hideSearch' => true]);
    }

    public function create()
    {
        return view('giangvien.create',['hideSearch' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_gv'   => 'required|unique:giang_viens',
            'ho_ten'  => 'required',
            'email'   => 'required|email|unique:giang_viens',
            'password' => 'required|min:6',
            'vai_tro'  => 'required',
        ], [
            'ma_gv.unique' => '⚠️ Mã giảng viên đã tồn tại trong hệ thống!',
            'password.min' => '🔒 Mật khẩu cần ít nhất 6 ký tự.',
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'vai_tro.required' => 'Vui lòng chọn vai trò.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => '⚠️ Email đã tồn tại trong hệ thống!',
        ]);

        GiangVien::create([
            'ma_gv'    => $request->ma_gv,
            'ho_ten'   => $request->ho_ten,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // mã hóa mật khẩu
            'vai_tro'  => $request->vai_tro,
        ]);

        return redirect()->route('giangvien.index')->with('success', 'Thêm giảng viên thành công!');
    }

    public function edit($id)
    {
        $giangvien = GiangVien::findOrFail($id);
        return view('giangvien.edit', compact('giangvien'),['hideSearch' => true]);
    }

    public function update(Request $request, $id)
    {
        $giangvien = GiangVien::findOrFail($id);

        $request->validate([
            'ma_gv' => [
                'required',
                Rule::unique('giang_viens')->ignore($giangvien->id), // Bỏ qua bản ghi hiện tại
            ],
            'ho_ten' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('giang_viens')->ignore($giangvien->id), // Bỏ qua bản ghi hiện tại
            ],
        ],[
            'ma_gv.unique' => '⚠️ Mã giảng viên đã tồn tại trong hệ thống!',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => '⚠️ Email đã tồn tại trong hệ thống!',
        ]);

        // Không cho cập nhật password hoặc vai_tro
        $giangvien->update([
            'ma_gv' => $request->ma_gv,
            'ho_ten' => $request->ho_ten,
            'email'  => $request->email,
        ]);

        return redirect()->route('giangvien.index')->with('success', 'Cập nhật giảng viên thành công!');
    }

    public function destroy($id)
   {
        try {
            $giangVien = GiangVien::findOrFail($id);

            // KIỂM TRA: Nếu giảng viên đã có phân công -> CHẶN LẠI VÀ BÁO LỖI
            if ($giangVien->phanCongGVs()->exists()) {
                return redirect()->back()->with('error', 'Không thể xóa giảng viên này vì họ có lịch sử phân công coi thi!');
            }

            // Nếu chưa có phân công nào thì mới cho phép xóa
            $giangVien->delete();

            return redirect()->back()->with('success', 'Xóa giảng viên thành công!');
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new GiangVienImport, $request->file('file'));

        return redirect()->route('giangvien.index')->with('success', 'Import giảng viên thành công!');
    }

    public function phancong(GiangVien $giangvien, Request $request)
    {
        // Lấy ID các lịch thi mà giảng viên đã được phân công
        $lichDaPhanCongIds = $giangvien->lichthis()->pluck('lich_thi_id');

        $query = LichThi::where('trang_thai', 'chua_dien_ra')
            ->whereNotIn('id', $lichDaPhanCongIds) // loại bỏ lịch đã phân công
            ->with(['giangviens', 'monHoc']);

        // Bộ lọc
        if($request->ten_mon){
            $query->whereHas('monHoc', fn($q) => 
                $q->where('ten_mon', 'like', '%'.$request->ten_mon.'%')
            );
        }
        if($request->phong){
            $query->where('phong', 'like', '%'.$request->phong.'%');
        }
        if($request->ngay){
            $query->where('ngay_thi', $request->ngay);
        }
        if($request->gio){
            $query->where('gio_thi', $request->gio);
        }
        $lichthis = $query->get();
        // Load danh sách lịch đã phân công (chỉ lấy lịch chưa diễn ra)
        $giangvien->load(['lichthis' => function($q){
            $q->where('trang_thai', 'chua_dien_ra')->with('monHoc');
        }]);

        return view('giangvien.phancong', compact('giangvien','lichthis'),['hideSearch' => true]);
    }

    // Phân công giảng viên
    public function assign(GiangVien $giangvien, LichThi $lichthi)
    {
        if (!$giangvien->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => "Tài khoản giảng viên {$giangvien->ho_ten} đã bị khóa!"
            ]);
        }
        $batDauMoi = $lichthi->thoi_gian_thi;
        $ketThucMoi = $batDauMoi->copy()->addMinutes($lichthi->thoi_luong_thi);

        $lichTrung = $giangvien->lichthis()->get()->first(function ($lt) use ($batDauMoi, $ketThucMoi) {
            $batDauCu = $lt->thoi_gian_thi;
            $ketThucCu = $batDauCu->copy()->addMinutes($lt->thoi_luong_thi);

            return ($batDauCu < $ketThucMoi) && ($ketThucCu > $batDauMoi);
        });

        if ($lichTrung) {
            return response()->json([
                'status' => 'conflict',
                'message' => 'Bị trùng lịch thi: '.$lichTrung->monHoc->ten_mon.' ('.$lichTrung->thoi_gian_thi.')'
            ]);
        }

        $giangvien->lichthis()->syncWithoutDetaching($lichthi->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Phân công thành công!'
        ]);
    }
    public function unassign(GiangVien $giangvien, LichThi $lichthi)
    {
        if (in_array($lichthi->trang_thai, ['dang_dien_ra', 'da_ket_thuc'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể hủy phân công khi lịch thi đang diễn ra hoặc đã kết thúc!'
            ], 400); 
        }
        // Xóa bản ghi trong bảng trung gian
        $giangvien->lichthis()->detach($lichthi->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã hủy phân công thành công!'
        ]);
    }

    public function toggle($id)
    {
        $gv = GiangVien::findOrFail($id);

        if (auth()->id() == $gv->id) {
        return back()->with('error', 'Không thể khóa chính tài khoản của bạn!');
        }

        $hasConflictExam = $gv->lichThis
            ->contains(function ($lich) {

                $batDau = $lich->thoi_gian_thi;
                $ketThuc = $batDau->copy()->addMinutes($lich->thoi_luong_thi);

                return now()->between($batDau, $ketThuc) || $batDau > now();
            });

        // ❌ Nếu đang active và có lịch → chặn khóa
        if ($hasConflictExam && $gv->is_active) {
            return back()->with('error', 'Giảng viên đang có hoặc sắp có lịch thi!');
        }

        // đảo trạng thái
        $gv->is_active = !$gv->is_active;
        $gv->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
