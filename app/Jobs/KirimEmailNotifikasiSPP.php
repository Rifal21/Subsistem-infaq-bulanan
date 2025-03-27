<?php

namespace App\Jobs;

use App\Mail\NotifikasiPembayaranMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class KirimEmailNotifikasiSPP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $emailTujuan = 'eskohengdelapansembilan@gmail.com';

        Mail::to($emailTujuan)->send(new NotifikasiPembayaranMail($this->data));
    }
}
