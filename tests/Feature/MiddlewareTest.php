<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth:giangvien', 'checkactive'])->get('/_test/check-active', function () {
            return response('active-ok');
        });

        Route::middleware(['web', 'auth:giangvien', 'role:admin'])->get('/_test/admin-role', function () {
            return response('admin-ok');
        });
    }

    public function test_check_active_cho_phep_giang_vien_dang_hoat_dong(): void
    {
        $gv = GiangVien::factory()->create(['is_active' => true]);

        $response = $this->actingAs($gv, 'giangvien')->get('/_test/check-active');

        $response->assertStatus(200);
        $response->assertSee('active-ok');
    }

    public function test_check_active_dang_xuat_va_chuyen_huong_khi_tai_khoan_bi_khoa(): void
    {
        $gv = GiangVien::factory()->create(['is_active' => false]);

        $response = $this->actingAs($gv, 'giangvien')->get('/_test/check-active');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Tài khoản đã bị khóa!');
        $this->assertGuest('giangvien');
    }

    public function test_role_middleware_cho_phep_admin(): void
    {
        $admin = GiangVien::factory()->admin()->create();

        $response = $this->actingAs($admin, 'giangvien')->get('/_test/admin-role');

        $response->assertStatus(200);
        $response->assertSee('admin-ok');
    }

    public function test_role_middleware_tu_choi_giang_vien_thuong(): void
    {
        $gv = GiangVien::factory()->create(['vai_tro' => 'giang_vien']);

        $response = $this->actingAs($gv, 'giangvien')->get('/_test/admin-role');

        $response->assertStatus(403);
    }

    public function test_chua_dang_nhap_bi_chuyen_huong_ve_login(): void
    {
        $response = $this->get('/_test/check-active');

        $response->assertRedirect(route('login'));
    }
}
