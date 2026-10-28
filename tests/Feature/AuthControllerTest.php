<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GiangVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createGiangVien(array $overrides = []): GiangVien
    {
        return GiangVien::create(array_merge([
            'ma_gv' => 'GV001',
            'ho_ten' => 'Nguyen Van A',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'vai_tro' => 'admin',
            'is_active' => true,
        ], $overrides));
    }

    // ===== LOGIN =====

    public function test_hien_thi_form_dang_nhap(): void
    {
        $response = $this->get('/dangnhap');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_dang_nhap_thanh_cong(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home.index'));
        $this->assertAuthenticatedAs($gv, 'giangvien');
    }

    public function test_dang_nhap_sai_mat_khau(): void
    {
        $this->createGiangVien();

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login_error');
        $this->assertGuest('giangvien');
    }

    public function test_dang_nhap_email_khong_ton_tai(): void
    {
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login_error');
        $this->assertGuest('giangvien');
    }

    public function test_dang_nhap_tai_khoan_bi_khoa(): void
    {
        $this->createGiangVien(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('login_error');
        $this->assertGuest('giangvien');
    }

    public function test_dang_nhap_validation_email_required(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_dang_nhap_validation_password_required(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ===== LOGOUT =====

    public function test_dang_xuat(): void
    {
        $gv = $this->createGiangVien();
        $this->actingAs($gv, 'giangvien');

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest('giangvien');
    }

    // ===== PROFILE =====

    public function test_xem_profile_khi_da_dang_nhap(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->get('/tai-khoan');

        $response->assertStatus(200);
        $response->assertViewIs('auth.profile');
    }

    public function test_xem_profile_khi_chua_dang_nhap(): void
    {
        $response = $this->get('/tai-khoan');

        $response->assertRedirect(route('login'));
    }

    // ===== CHANGE PASSWORD =====

    public function test_hien_thi_form_doi_mat_khau(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->get('/tai-khoan/doi-mat-khau');

        $response->assertStatus(200);
        $response->assertViewIs('auth.changePassword');
    }

    public function test_doi_mat_khau_thanh_cong(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->post('/tai-khoan/doi-mat-khau', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertGuest('giangvien');

        // Verify new password works
        $gv->refresh();
        $this->assertTrue(Hash::check('newpassword123', $gv->password));
    }

    public function test_doi_mat_khau_sai_mat_khau_cu(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->post('/tai-khoan/doi-mat-khau', [
            'current_password' => 'wrong-old-password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_doi_mat_khau_xac_nhan_khong_khop(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->post('/tai-khoan/doi-mat-khau', [
            'current_password' => 'password',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('new_password');
    }

    public function test_doi_mat_khau_qua_ngan(): void
    {
        $gv = $this->createGiangVien();

        $response = $this->actingAs($gv, 'giangvien')->post('/tai-khoan/doi-mat-khau', [
            'current_password' => 'password',
            'new_password' => '12345',
            'new_password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors('new_password');
    }

    // ===== FORGOT PASSWORD =====

    public function test_hien_thi_form_quen_mat_khau(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_reset_mat_khau_thanh_cong(): void
    {
        Mail::fake();

        $gv = $this->createGiangVien();
        $oldPassword = $gv->password;

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Password should be changed
        $gv->refresh();
        $this->assertNotEquals($oldPassword, $gv->password);

        // Mail should be sent
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($gv) {
            return $mail->hasTo($gv->email);
        });
    }

    public function test_reset_mat_khau_email_khong_ton_tai(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'notexist@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ===== CHECKACTIVE MIDDLEWARE =====

    public function test_tai_khoan_bi_khoa_giua_session(): void
    {
        $gv = $this->createGiangVien();
        $this->actingAs($gv, 'giangvien');

        // Simulate account being deactivated
        $gv->update(['is_active' => false]);

        $response = $this->get('/tai-khoan');

        $response->assertRedirect(route('login'));
    }
}
