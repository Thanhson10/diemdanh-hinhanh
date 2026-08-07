<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use Illuminate\Http\Request;
use App\Imports\SinhVienImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
class SinhVienController extends Controller
{
        private $bucket = 'diemdanh-sinhvien';
        private $collection = 'sinhvien_faces';
    
    public function index(Request $request)
    {
        $query = SinhVien::query()
            ->with(['anhDaTrain']); 

        if ($request->filled('ma_sv')) {
            $query->where('ma_sv', 'like', '%' . $request->ma_sv . '%');
        }

        if ($request->filled('ho_ten')) {
            $query->where('ho_ten', 'like', '%' . $request->ho_ten . '%');
        }

        if ($request->filled('lop')) {
            $query->where('lop', 'like', '%' . $request->lop . '%');
        }

         if ($request->has('chua_co_anh')) {
            $query->whereDoesntHave('anhDaTrain');
        }

        if ($request->has('co_anh')) {
            $query->whereHas('anhDaTrain');
        }

        $sinhviens = $query->orderBy('ma_sv', 'desc')->paginate(10)->withQueryString();;

        return view('sinhvien.index', compact('sinhviens'));
    }

    // Form thêm sinh viên
    public function create()
    {
        return view('sinhvien.create',['hideSearch' => true]);
    }

    // Lưu sinh viên mới
   public function store(Request $request)
    {
        $request->validate([
            'ma_sv'   => 'required|size:10|unique:sinh_viens',
            'ho_ten'  => 'required',
            'lop'   => 'required',
            'email'   => 'required|email|unique:sinh_viens',
        ], [
            'ma_sv.required' => '⚠️ Không được để trống MSSV!',
            'email.required' => '⚠️ Email không được để trống!',
            'ma_sv.unique' => '⚠️ Mã số sinh viên đã tồn tại!',
            'email.unique' => '⚠️ Email đã tồn tại!',
            'lop.required' => '⚠️ Không được để trống lớp!',
            'ho_ten.required' => '⚠️ Vui lòng nhập họ tên!',
            'ma_sv.size'     => '⚠️ MSSV phải đủ 10 ký tự!',
        ]);

        $data = $request->only(['ma_sv', 'ho_ten', 'lop', 'email']);
        SinhVien::create($data);

        return redirect()
            ->route('sinhvien.index')
            ->with('success', 'Thêm sinh viên thành công!');
    }


    // Form sửa sinh viên
    public function edit($id)
    {
        $sinhvien = SinhVien::findOrFail($id);
        return view('sinhvien.edit', compact('sinhvien'),['hideSearch' => true]);
    }


    // Cập nhật sinh viên
    public function update(Request $request, $id)
    {
        $sinhvien = SinhVien::findOrFail($id);

        $oldMaSv = $sinhvien->ma_sv;

        $request->validate([
            'ma_sv' => [
                'required',
                'size:10',
                Rule::unique('sinh_viens')->ignore($sinhvien->id),
            ],
            'ho_ten' => 'required',
            'lop'  => 'required',
            'email'  => [
                'required',
                'email',
                Rule::unique('sinh_viens')->ignore($sinhvien->id),
            ],
        ], [
            'ma_sv.required' => '⚠️ Không được để trống MSSV!',
            'email.required' => '⚠️ Email không được để trống!',
            'ma_sv.unique' => '⚠️ Mã số sinh viên đã tồn tại!',
            'email.unique' => '⚠️ Email đã tồn tại!',
            'lop.required' => '⚠️ Không được để trống lớp!',
            'ho_ten.required' => '⚠️ Vui lòng nhập họ tên!',
            'ma_sv.size'     => '⚠️ MSSV phải đủ 10 ký tự!',
        ]);

        $data = $request->only(['ma_sv', 'ho_ten', 'lop', 'email']);
        $sinhvien->update($data);

        return redirect()
            ->route('sinhvien.index')
            ->with('success', 'Cập nhật sinh viên thành công!');
    }
    // Xóa sinh viên
    public function destroy($id)
    {
        try {
            $sv = SinhVien::findOrFail($id);

            // Nếu sinh viên đã có tên trong bất kỳ danh sách điểm danh nào -> CHẶN XÓA
            if ($sv->diemDanhs()->exists()) {
                return redirect()->back()->with('error', 'Không thể xóa! Sinh viên này đã có dữ liệu điểm danh trong lịch thi.');
            }

            $sv->delete();
            
            return redirect()->back()->with('success', 'Đã xóa sinh viên thành công.');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new SinhVienImport();
        Excel::import($import, $request->file('file'));

        // Lấy lỗi từng dòng
        $failures = $import->failures();

        if ($failures->isNotEmpty()) {
            return back()->with([
                'import_failures' => $failures,
                'warning' => 'Import hoàn tất nhưng có một số dòng bị lỗi!',
            ]);
        }

        return back()->with('success', 'Import danh sách sinh viên thành công!');
    }
    public function searchByList(Request $request)
    {
        $mssv = $request->input('mssv'); // <-- lấy mảng MSSV từ JSON

        $sinhviens = SinhVien::whereIn('ma_sv', $mssv)->get();

        return response()->json($sinhviens);
    }
}

