<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LichThi;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        LichThi::whereIn('trang_thai', ['chua_dien_ra', 'dang_dien_ra'])
            ->get()
            ->each(function ($lichThi) {
                $lichThi->capNhatTrangThai();
            });

        if (!$user) {
            $lichThis = collect();

            return view('home.index', compact('lichThis'));
        }
        $query = LichThi::with('monHoc');

        // Chỉ giảng viên/admin mới xem lịch được phân công
        if (in_array($user->vai_tro, ['giang_vien', 'admin'])) {
            $query->whereHas('phanCongGVs', function ($q) use ($user) {
                $q->where('giang_vien_id', $user->id);
            });
        }

        $query->where('trang_thai', 'dang_dien_ra');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('phong', 'like', "%$search%")
                ->orWhereHas('monHoc', function ($sub) use ($search) {
                    $sub->where('ten_mon', 'like', "%$search%");
                });
            });
        }

        $lichThis = $query
            ->orderBy('ngay_thi', 'asc')
            ->orderBy('gio_thi', 'asc')
            ->paginate(10);

        return view('home.index', compact('lichThis'));
    }
}
