<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiPembayaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $view = ($this->data['status'] === '-' || empty($this->data['status']))
            ? 'emails.notifikasi_belum'
            : 'emails.notifikasi_sudah';

        return $this->subject('Notifikasi Pembayaran SPP')
            ->view($view)
            ->with($this->data);
    }
}
