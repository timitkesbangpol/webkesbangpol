<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $userName;
    public $ipAddress;
    public $requestTime;

    public function __construct(string $otpCode, string $userName = 'Pengguna', ?string $ipAddress = null, ?string $requestTime = null)
    {
        $this->otpCode = $otpCode;
        $this->userName = $userName;
        $this->ipAddress = $ipAddress ?: request()->ip();
        $this->requestTime = $requestTime ?: now()->format('d/m/Y H:i') . ' WIB';
    }

    public function build()
    {
        return $this->subject('[OTP] Kode Verifikasi Masuk: ' . $this->otpCode . ' - Bakesbangpol')
                    ->view('emails.otp');
    }
}
