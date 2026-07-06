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
    // Danh sách sinh viên
   public function index(Request $request)
    {
        $query = SinhVien::query();

        // Nếu có tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ma_sv', 'like', "%{$search}%")
                ->orWhere('ho_ten', 'like', "%{$search}%")
                ->orWhere('lop', 'like', "%{$search}%");
        }

        $sinhviens = $query->orderBy('ma_sv', 'asc')->paginate(20);
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
            'ma_sv'   => 'required|unique:sinh_viens',
            'ho_ten'  => 'required',
            'lop_y'   => 'required|digits:2',
            'lop_z'   => 'required|digits:2',
            'email'   => 'required|email|unique:sinh_viens',
        ], [
            'ma_sv.unique' => '⚠️ Mã số sinh viên đã tồn tại!',
            'email.unique' => '⚠️ Email đã tồn tại!',
        ]);

        /** Lớp */
        $folder = strtolower("d{$request->lop_y}_th{$request->lop_z}");
        $lop    = "D{$request->lop_y}_TH{$request->lop_z}";

        /** Chuẩn bị data */
        $data = $request->except(['lop_y', 'lop_z']);
        $data['lop'] = $lop;

        SinhVien::create($data);

        return redirect()
            ->route('sinhvien.index')
            ->with('success', 'Thêm sinh viên thành công!');
    }


    // Form sửa sinh viên
    public function edit($id)
    {
        $sinhvien = SinhVien::findOrFail($id);
        // Ví dụ: D23_TH09
        $lop = $sinhvien->lop;
        // D23_TH09 → 23
        $lop_y = substr($lop, 1, 2);
        // D23_TH09 → 09
        $lop_z = substr($lop, -2);

        return view('sinhvien.edit', compact('sinhvien', 'lop_y', 'lop_z'),['hideSearch' => true]);
    }


    // Cập nhật sinh viên
    public function update(Request $request, $id)
    {
        $sinhvien = SinhVien::findOrFail($id);

        $oldMaSv = $sinhvien->ma_sv;

        // Lớp cũ
        preg_match('/D(\d+)_TH(\d+)/', $sinhvien->lop, $matches);
        $oldFolder = strtolower("d{$matches[1]}_th{$matches[2]}");

        $request->validate([
            'ma_sv' => [
                'required',
                Rule::unique('sinh_viens')->ignore($sinhvien->id),
            ],
            'ho_ten' => 'required',
            'lop_y'  => 'required|digits:2',
            'lop_z'  => 'required|digits:2',
            'email'  => [
                'required',
                'email',
                Rule::unique('sinh_viens')->ignore($sinhvien->id),
            ],
        ], [
            'ma_sv.unique' => '⚠️ Mã số sinh viên đã tồn tại!',
            'email.unique' => '⚠️ Email đã tồn tại!',
        ]);

        /** Lớp mới */
        $newFolder = strtolower("d{$request->lop_y}_th{$request->lop_z}");
        $lop       = "D{$request->lop_y}_TH{$request->lop_z}";

        /** Chuẩn bị data */
        $data = $request->except(['lop_y', 'lop_z']);
        $data['lop'] = $lop;

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

            if ($sv->da_train_khuon_mat) {
                $lambdaUrl = env('LAMBDA_DELETE_URL');

                $response = Http::post($lambdaUrl, [
                    'bucket'          => $this->bucket,
                    'collectionId'    => $this->collection,
                    'externalImageId' => $sv->ma_sv,
                ]);

                if (!$response->ok()) {
                    return redirect()->back()->with('error', 'Không thể kết nối đến AWS Lambda để xóa ảnh khuôn mặt.');
                }

                $data = $response->json();
                if (isset($data['body'])) {
                    $data = json_decode($data['body'], true);
                }

                if (empty($data['success'])) {
                    return redirect()->back()->with('error', 'Lỗi từ AWS khi xóa mặt: ' . ($data['message'] ?? 'Không xác định'));
                }
            }
            // XÓA SINH VIÊN TRONG DATABASE
            $sv->delete();

            return redirect()->back()->with('success', 'Đã xóa sinh viên và toàn bộ dữ liệu khuôn mặt thành công!');

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage());
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

