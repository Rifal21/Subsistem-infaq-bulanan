<?php

namespace App\Jobs;

use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KirimNotifikasiWA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $nomor;
    protected $pesan;

    /**
     * Create a new job instance.
     */
    public function __construct($nomor, $pesan)
    {
        $this->nomor = $nomor;
        $this->pesan = $pesan;
        // dd($this->nomor, $this->pesan);
    }

    /**
     * Execute the job.
     */
    public function handle(TwilioService $twilioService)
    {
        try {
            $twilioService->sendWhatsAppMessage($this->nomor, $this->pesan);
        } catch (\Exception $e) {
            Log::error("Gagal mengirim WhatsApp ke {$this->nomor}: " . $e->getMessage());
        }
    }
}
