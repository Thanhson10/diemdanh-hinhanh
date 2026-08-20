<?php

namespace App\Http\Controllers;

use App\Models\LichThi;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LichThiImport;
use App\Models\GiangVien;
use App\Models\PhanCongGV;
use App\Models\SinhVien;
use App\Models\DiemDanh;
use Illuminate\Validation\Rule;
use App\Exports\DiemDanhExport;
use Illuminate\Support\Str;
class LichThiController extends Controller
{
    public function index(Request $request)
    {
        $query = LichThi::with('monHoc')
            ->withCount([
                'sinhViens as so_sinh_vien',
                'giangViens as so_giang_vien'
            ]);
        LichThi::whereIn('trang_thai', ['chua_dien_ra', 'dang_dien_ra'])
            ->get()
            ->each(function ($lichThi) {
                $lichThi->capNhatTrangThai();
            });
        // Bộ lọc nâng cao
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

        if ($request->filled('gio')) {
            $query->where('gio_thi', $request->gio);
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

        if ($request->filled('ma_sv')) {
            $ma_sv = trim($request->ma_sv);
            $query->whereHas('sinhViens', function ($q) use ($ma_sv) {
                $q->where('ma_sv', 'like', "%$ma_sv%");
            });
        }
        
        $lichthis = $query->orderByRaw("
            FIELD(trang_thai, 'dang_dien_ra', 'chua_dien_ra', 'da_ket_thuc')
        ")
        ->orderBy('ngay_thi', 'desc')
        ->orderBy('gio_thi', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('lichthi.index', compact('lichthis'),['hideSearch' => true]);
    }

    public function create()
    {
        $monhocs = MonHoc::all();
        return view('lichthi.create', compact('monhocs'),['hideSearch' => true]);
    }

    public function store(Request $request)
    {   
        $year = now()->year;

        $validNamHoc = [
            ($year - 1) . '-' . $year,
            $year . '-' . ($year + 1),
        ];

        $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hocs,id',
            'ngay_thi'   => 'required|date',
            'gio_thi'    => 'required',
            // 'gio_thi' => [
            //         'required',
            //         'date_format:H:i',
            //         'after_or_equal:07:00',
            //         'before_or_equal:17:00',
            //     ],
            'thoi_luong_thi' => 'required|integer|min:1|max:300',
            'phong'      => 'required|string',
            'ky_thi'     => 'required|string',
            'nam_hoc'    => ['required', Rule::in($validNamHoc)],
        ], [
        'nam_hoc.in' => 'Năm học chỉ được phép là: ' . implode(' hoặc ', $validNamHoc),
        'mon_hoc_id.required' => 'Không được để trống',
        'ngay_thi.required' => 'Không được để trống ngày thi',
        'gio_thi.required' => 'Không được để trống giờ thi',
        'phong.required' => 'Không được để trống phòng',
        'ky_thi.required' => 'Không được để trống kỳ thi',
        'nam_hoc.required' => 'Không được để trống năm học',
        'thoi_luong_thi.required' => 'Vui lòng nhập thời lượng thi',
        'thoi_luong_thi.integer' => 'Thời lượng phải là số nguyên',
        'thoi_luong_thi.min' => 'Thời lượng phải lớn hơn 0',
        'thoi_luong_thi.max' => 'Thời lượng không được vượt quá 300 phút',
        ]);

        $batDau = \Carbon\Carbon::parse($request->ngay_thi . ' ' . $request->gio_thi);
        $ketThuc = $batDau->copy()->addMinutes((int) $request->thoi_luong_thi);
        // Check thời gian phải ở tương lai
        if ($batDau->lessThanOrEqualTo(now())) {
            return back()->withErrors([
                'ngay_thi' => 'Thời gian thi phải lớn hơn thời điểm hiện tại',
                'gio_thi' => 'Thời gian thi phải lớn hơn thời điểm hiện tại'
            ])->withInput();
        }

        // Check trùng phòng
       $trungPhong = LichThi::where('phong', $request->phong)
        ->whereRaw(
            "TIMESTAMP(ngay_thi, gio_thi) < ?
             AND ADDTIME(TIMESTAMP(ngay_thi, gio_thi), SEC_TO_TIME(thoi_luong_thi * 60)) > ?",
            [$ketThuc, $batDau]
        )
        ->exists();

        if ($trungPhong) {
            return back()->withErrors([
                'phong' => 'Phòng này đã có lịch thi trong khung giờ này'
            ])->withInput();
        }

        LichThi::create($request->all());

        return redirect()->route('lichthi.index')
            ->with('success', 'Thêm lịch thi thành công!');
    }

    public function edit($id)
    {
        $lichthi = LichThi::findOrFail($id);
        $monhocs = MonHoc::all();
        return view('lichthi.edit', compact('lichthi','monhocs'),['hideSearch' => true]);
    }

    public function update(Request $request, $id)
    {
        $lichthi = LichThi::findOrFail($id);

        if ($lichthi->trang_thai !== 'chua_dien_ra') {
            return redirect()->route('lichthi.index')
                ->with('error', 'Chỉ được sửa lịch thi khi chưa diễn ra!');
        }

        $year = now()->year;

        $validNamHoc = [
            ($year - 1) . '-' . $year,
            $year . '-' . ($year + 1),
        ];
        
        $request->validate([
            'mon_hoc_id'     => 'required|exists:mon_hocs,id',
            'ngay_thi'       => 'required|date',
            'gio_thi'        => 'required',
            'thoi_luong_thi' => 'required|integer|min:1|max:300',
            'phong'          => 'required|string',
            'ky_thi'         => 'required|string',
            'nam_hoc'        => ['required', Rule::in($validNamHoc)],
        ]);

        $batDau = \Carbon\Carbon::parse($request->ngay_thi . ' ' . $request->gio_thi, 'Asia/Ho_Chi_Minh');
        $ketThuc = $batDau->copy()->addMinutes((int) $request->thoi_luong_thi); 

        // 1. Check thời gian tương lai
        if ($batDau->lessThanOrEqualTo(now())) {
            return back()->withErrors([
                'gio_thi'  => 'Thời gian thi phải lớn hơn thời điểm hiện tại',
                'ngay_thi' => 'Kiểm tra lại thời gian'
            ])->withInput();
        }

        // 2. Check trùng phòng (loại trừ chính nó)
        $trungPhong = LichThi::where('phong', $request->phong)
            ->where('id', '<>', $lichthi->id)
            ->where(function($q) use ($batDau, $ketThuc) {
                $q->whereRaw("TIMESTAMP(ngay_thi, gio_thi) < ?", [$ketThuc])
                ->whereRaw("TIMESTAMP(ngay_thi, gio_thi + INTERVAL thoi_luong_thi MINUTE) > ?", [$batDau]);
            })
            ->exists();
        
        if ($trungPhong) {
            return back()->withErrors([
                'phong' => 'Phòng này đã có lịch thi trong khung giờ này'
            ])->withInput();
        }

        $giangVienIds = PhanCongGV::where('lich_thi_id', $lichthi->id)->pluck('giang_vien_id');
        
        if ($giangVienIds->isNotEmpty()) {
            $lichTrungGV = PhanCongGV::whereIn('giang_vien_id', $giangVienIds)
                ->whereHas('lichThi', function($q) use ($batDau, $ketThuc, $lichthi) {
                    $q->where('id', '<>', $lichthi->id) // Bỏ qua lịch hiện tại đang sửa
                    ->where(function($q2) use ($batDau, $ketThuc) {
                        $q2->whereRaw("TIMESTAMP(ngay_thi, gio_thi) < ?", [$ketThuc])
                        ->whereRaw("TIMESTAMP(ngay_thi, gio_thi + INTERVAL thoi_luong_thi MINUTE) > ?", [$batDau]);
                    });
                })
                ->with(['lichThi.monHoc', 'giangVien'])
                ->first();

            if ($lichTrungGV) {
                $lt = $lichTrungGV->lichThi;
                $gv = $lichTrungGV->giangVien;
                return back()->withErrors([
                    'ngay_thi' => "Không thể đổi giờ! Giảng viên {$gv->ho_ten} bị trùng với ca thi môn {$lt->monHoc->ten_mon} (Phòng {$lt->phong}, lúc {$lt->thoi_gian_thi->format('d-m-Y H:i')})."
                ])->withInput();
            }
        }

        $sinhVienIds = DiemDanh::where('lich_thi_id', $lichthi->id)->pluck('sinh_vien_id');

        if ($sinhVienIds->isNotEmpty()) {
            $trungLichSV = DiemDanh::whereIn('sinh_vien_id', $sinhVienIds)
                ->whereHas('lichThi', function($q) use ($batDau, $ketThuc, $lichthi) {
                    $q->where('id', '<>', $lichthi->id)
                    ->where(function($q2) use ($batDau, $ketThuc) {
                        $q2->whereRaw("TIMESTAMP(ngay_thi, gio_thi) < ?", [$ketThuc])
                        ->whereRaw("TIMESTAMP(ngay_thi, gio_thi + INTERVAL thoi_luong_thi MINUTE) > ?", [$batDau]);
                    });
                })
                ->with(['lichThi.monHoc', 'sinhVien'])
                ->first(); 

            if ($trungLichSV) {
                $lt = $trungLichSV->lichThi;
                $sv = $trungLichSV->sinhVien;
                return back()->withErrors([
                    'ngay_thi' => "Không thể đổi giờ! Sinh viên {$sv->ma_sv} - {$sv->ho_ten} bị trùng với ca thi môn {$lt->monHoc->ten_mon} (Phòng {$lt->phong}, lúc {$lt->thoi_gian_thi->format('d-m-Y H:i')})."
                ])->withInput();
            }
        }

        // Nếu vượt qua toàn bộ các bước check -> Tiến hành cập nhật
        $lichthi->update($request->all());

        return redirect()->route('lichthi.index')
            ->with('success', 'Cập nhật lịch thi thành công!');
    }

    public function destroy($id)
    {
        $lichthi = LichThi::findOrFail($id);

        if ($lichthi->trang_thai !== 'chua_dien_ra') {
            return redirect()->route('lichthi.index')
                ->with('error', 'Chỉ được xóa lịch thi chưa diễn ra!');
        }

        $lichthi->delete();

        return redirect()->route('lichthi.index')
            ->with('success', 'Đã xóa lịch thi!');
    }
//     public function destroy($id)
    // {
    //     $lichthi = LichThi::findOrFail($id);

    //     if ($lichthi->trang_thai === 'dang_dien_ra') {
    //         return redirect()->route('lichthi.index')
    //             ->with('error', 'Không thể xóa lịch thi đang diễn ra!');
    //     }

    //     if ($lichthi->trang_thai === 'da_ket_thuc') {

    //         $coSinhVienCoMat = $lichthi->diemDanhs()
    //             ->where('trang_thai', '!=', 'vang_mat')
    //             ->exists();

    //         if ($coSinhVienCoMat) {
    //             return redirect()->route('lichthi.index')
    //                 ->with('error', 'Không thể xóa vì đã có sinh viên tham gia!');
    //         }
    //     }

    //     $lichthi->delete();

    //     return redirect()->route('lichthi.index')
    //         ->with('success', 'Đã xóa lịch thi!');
    // }

    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        Excel::import(new LichThiImport, $request->file('file'));
        return redirect()->route('lichthi.index')->with('success', 'Import lịch thi thành công!');
    }
    public function phanCongForm($id) { 
        $lichThi = LichThi::with('phanCongGVs')->findOrFail($id); 
        // Lấy ID các giảng viên đã được phân công cho lịch thi này
        $daPhanCong = $lichThi->phanCongGVs->pluck('giang_vien_id'); 
        // Lọc bỏ các giảng viên đó
        $giangViens = GiangVien::whereNotIn('id', $daPhanCong)->where('is_active', 1)->get();
        return view('lichthi.phancong', compact('lichThi', 'giangViens'),['hideSearch' => true]); 
    } 
    public function phanCongSave(Request $request, $id)
    {
        $request->validate([
            'lich_thi_id' => 'required|exists:lich_this,id',
            'giang_vien_id' => 'required|exists:giang_viens,id',
        ]);

        $lichThi = LichThi::findOrFail($request->lich_thi_id);
        $giangVien = GiangVien::findOrFail($request->giang_vien_id);

        // CHECK GIẢNG VIÊN BỊ KHÓA
        if (!$giangVien->is_active) {
            return redirect()->back()->with('error', 
                "Tài khoản giảng viên {$giangVien->ho_ten} đã bị khóa!");
        }
        $giangVienId = $giangVien->id;

        $batDau = $lichThi->thoi_gian_thi;
        $ketThuc = $batDau->copy()->addMinutes($lichThi->thoi_luong_thi);

        // Tìm lịch trùng của giảng viên
        $lichTrung = PhanCongGV::where('giang_vien_id', $giangVienId)
        ->whereHas('lichThi', function($q) use ($batDau, $ketThuc, $lichThi) {
            $q->where('id', '<>', $lichThi->id)
            ->where(function($q2) use ($batDau, $ketThuc) {
                $q2->whereRaw("TIMESTAMP(ngay_thi, gio_thi) < ?", [$ketThuc])
                    ->whereRaw("TIMESTAMP(ngay_thi, gio_thi + INTERVAL thoi_luong_thi MINUTE) > ?", [$batDau]);
            });
        })
        ->with(['lichThi.monHoc', 'giangVien']) // lấy thông tin giảng viên
        ->first();

        if ($lichTrung) {
            $lt = $lichTrung->lichThi;
            $gv = $lichTrung->giangVien;
            $msg = "Giảng viên {$gv->ho_ten} đã có lịch thi môn: {$lt->monHoc->ten_mon}, "
                . "Phòng: {$lt->phong} vào lúc: {$lt->thoi_gian_thi->format('d-m-Y H:i')}!";
            return redirect()->back()->with('error', $msg);
        }

        // Nếu hợp lệ, phân công
        PhanCongGV::updateOrCreate(
            [
                'lich_thi_id' => $request->lich_thi_id,
                'giang_vien_id' => $giangVienId,
            ]
        );

        return redirect()->back()->with('success', 'Phân công giảng viên thành công!');
    }


    public function xoaPhanCong($lichthiId, $phancongId) { 

        $lichthi = LichThi::findOrFail($lichthiId);

        if (in_array($lichthi->trang_thai, ['dang_dien_ra', 'da_ket_thuc'])) {
            return redirect()->back()->with('error', 'Không thể hủy phân công khi lịch thi đang diễn ra hoặc đã kết thúc!');
        }

        $phanCong = PhanCongGV::where('lich_thi_id', $lichthiId) 
        ->where('id', $phancongId) 
        ->firstOrFail(); 
        $phanCong->delete(); 
        return redirect()->back()->with('success', 'Đã hủy phân công giảng viên.'); 
    }

    public function addStudents(Request $request, LichThi $lichthi)
    {
        $mssvList = $request->input('mssv', []);

        if (empty($mssvList)) {
            return response()->json(['success' => false, 'msg' => 'Không có MSSV gửi lên', 'added'=>[], 'skipped'=>[]]);
        }

        $students = SinhVien::whereIn('ma_sv', $mssvList)->get();

        $batDau = $lichthi->thoi_gian_thi;
        $ketThuc = $batDau->copy()->addMinutes($lichthi->thoi_luong_thi);

        $added = [];
        $skipped = [];

        foreach ($students as $sv) {
            $daCoTrongLich = DiemDanh::where('lich_thi_id', $lichthi->id)
                ->where('sinh_vien_id', $sv->id)
                ->exists();

            if ($daCoTrongLich) {
                $skipped[] = "{$sv->ma_sv} – {$sv->ho_ten} (đã có trong ca thi)";
                continue;
            }

            $trungLich = DiemDanh::where('sinh_vien_id', $sv->id)
                ->whereHas('lichThi', function ($q) use ($batDau, $ketThuc, $lichthi) {
                    $q->where('id', '<>', $lichthi->id)
                    ->where(function($q2) use ($batDau, $ketThuc) {
                        $q2->whereRaw("TIMESTAMP(ngay_thi, gio_thi) < ?", [$ketThuc])
                            ->whereRaw("TIMESTAMP(ngay_thi, gio_thi + INTERVAL thoi_luong_thi MINUTE) > ?", [$batDau]);
                    });
                })
                ->with(['lichThi.monHoc'])
                ->first();

            if ($trungLich) {
                $lt = $trungLich->lichThi;
                $skipped[] = "{$sv->ma_sv} – {$sv->ho_ten} (trùng môn: {$lt->monHoc->ten_mon}, phòng: {$lt->phong}, {$lt->thoi_gian_thi->format('d-m-Y H:i')})";
                continue;
            }

            DiemDanh::updateOrCreate([
                'lich_thi_id' => $lichthi->id,
                'sinh_vien_id' => $sv->id,
            ]);
            $added[] = "{$sv->ma_sv} – {$sv->ho_ten}";
        }

        return response()->json([
            'success' => count($added) > 0,
            'msg' => '',
            'added' => $added,
            'skipped' => $skipped
        ]);
    }


    public function show($id)
    {
        $lichThi = LichThi::findOrFail($id);
        $sinhViens = DiemDanh::with('sinhVien')->where('lich_thi_id', $id)->get();
        return view('lichthi.show', compact('lichThi', 'sinhViens'),['hideSearch' => true]);
    }
    public function removeStudent($id)
    {
        $record = DiemDanh::findOrFail($id); 
        $record->delete();

        return back()->with('success', 'Đã xóa sinh viên khỏi ca thi.');
    }
    public function showKetQua($id)
    {
        $lichThi = LichThi::with(['monHoc'])->findOrFail($id);
        
        // Lấy danh sách điểm danh với thông tin sinh viên
        $sinhViens = DiemDanh::where('lich_thi_id', $id)
            ->with('sinhVien')
            ->orderBy('sinh_vien_id')
            ->get();

        return view('lichthi.ketqua', compact('lichThi', 'sinhViens'),['hideSearch' => true]);
    }
    
    public function export($id)
    {
        \Log::info("Export called with ID: " . $id);
        
        $lichThi = LichThi::with('monHoc')->findOrFail($id);
        
        // Sử dụng Str::slug() thay vì str_slug()
        $tenMon = Str::slug($lichThi->monHoc->ten_mon ?? 'monhoc');
        $phong = Str::slug($lichThi->phong ?? 'phong');
        $ngayGio = $lichThi->thoi_gian_thi 
        ? $lichThi->thoi_gian_thi->format('d-m-Y') . '_' . $lichThi->thoi_gian_thi->format('G') . 'h' . $lichThi->thoi_gian_thi->format('i')
        : 'ngaythi';
        $kyThi = Str::slug($lichThi->ky_thi ?? 'kythi');
        $namHoc = Str::slug($lichThi->nam_hoc ?? 'namhoc');
        
        $filename = "{$tenMon}_{$phong}_{$ngayGio}_{$kyThi}_{$namHoc}.xlsx";
        
        return Excel::download(new DiemDanhExport($id), $filename);
    }
}
