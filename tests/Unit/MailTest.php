<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Mail\ResetPasswordMail;

class MailTest extends TestCase
{
    public function test_reset_password_mail_khoi_tao_dung_mat_khau(): void
    {
        $mail = new ResetPasswordMail('matKhauMoi123');

        $this->assertEquals('matKhauMoi123', $mail->password);
    }

    public function test_reset_password_mail_render_view_chinh_xac(): void
    {
        $mail = new ResetPasswordMail('SecretPass#2026');

        $mail->build();

        $this->assertEquals('emails.reset_password', $mail->view);
        $this->assertArrayHasKey('password', $mail->viewData);
        $this->assertEquals('SecretPass#2026', $mail->viewData['password']);
    }
}
