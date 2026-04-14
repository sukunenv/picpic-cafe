<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $name;

    public function __construct($token, $email, $name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    public function build()
    {
        $resetUrl = env('FRONTEND_URL', 'http://localhost:5173') . "/reset-password?token=" . $this->token . "&email=" . urlencode($this->email);

        return $this->subject('Reset Password PicPic Cafe')
                    ->html("
                        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #F8F7FF; border-radius: 20px;'>
                            <div style='text-align: center; margin-bottom: 30px;'>
                                <img src='https://res.cloudinary.com/dkcl8wzdc/image/upload/q_auto/f_auto/v1776032977/logo_apuccy.png' width='80' style='border-radius: 20px;' />
                                <h1 style='color: #2D2B55; margin-top: 15px;'>Reset Password Kamu</h1>
                            </div>
                            <div style='background: white; padding: 30px; border-radius: 24px; box-shadow: 0 10px 30px rgba(99, 103, 255, 0.05);'>
                                <p style='color: #2D2B55; font-size: 16px; line-height: 1.6;'>Halo <strong>{$this->name}</strong>,</p>
                                <p style='color: #2D2B55; opacity: 0.8; font-size: 14px; line-height: 1.6;'>
                                    Kami menerima permintaan untuk mereset password akun PicPic Cafe kamu. Klik tombol di bawah ini untuk membuat password baru:
                                </p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='{$resetUrl}' style='background: #6367FF; color: white; padding: 15px 30px; text-decoration: none; border-radius: 14px; font-weight: bold; display: inline-block;'>Reset Password Sekarang</a>
                                </div>
                                <p style='color: #2D2B55; opacity: 0.8; font-size: 12px; font-style: italic;'>
                                    *Link ini akan kedaluwarsa dlm 60 menit. Jika kamu tidak meminta reset password, abaikan email ini.
                                </p>
                            </div>
                            <div style='text-align: center; margin-top: 30px; color: #2D2B55; opacity: 0.5; font-size: 11px;'>
                                &copy; 2026 PicPic Cafe. Powered by Kalify.dev
                            </div>
                        </div>
                    ");
    }
}
