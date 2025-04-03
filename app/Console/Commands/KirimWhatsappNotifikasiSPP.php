<?php

namespace App\Console\Commands;

use App\Jobs\KirimNotifikasiWA;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\TwilioService;

class KirimWhatsappNotifikasiSPP extends Command
{
    protected $signature = 'spp:kirim-wa {--bulan=}';
    protected $description = 'Kirim notifikasi WhatsApp untuk santri yang belum bayar SPP';

    public function handle()
    {
        $bulanInput = strtoupper($this->option('bulan') ?? now()->format('M'));

        $mapBulan = [
            'JAN' => '14',
            'FEB' => '15',
            'MAR' => '16',
            'APR' => '17',
            'MEI' => '18',
            'JUN' => '19',
            'JULI' => '8',
            'AUG' => '9',
            'SEPT' => '10',
            'OKT' => '11',
            'NOP' => '12',
            'DES' => '13',
        ];

        $kolom = $mapBulan[$bulanInput] ?? null;
        if (!$kolom) {
            $this->error("Bulan $bulanInput tidak valid.");
            return;
        }

        $response = Http::get('https://api.sheetbest.com/sheets/632e9480-5bcf-4851-b68b-a979baa6bea8');
        $rows = collect($response->json())->skip(1);

        $totalTerkirim = 0;

        foreach ($rows as $s) {
            $nama   = $s['1'] ?? '-';  // Nama santri
            $kelas  = $s['2'] ?? '-';  // Kelas
            $nomor  = $s['5'] ?? null; // WA (Pastikan sesuai)
            $ustadz = $s['4'] ?? '-';  // Ustadz
            $status = $s[$kolom] ?? '-';

            if (!$nomor || $status !== '-') continue;

            // Format nomor agar sesuai dengan standar internasional
            $nomor = preg_replace('/[^0-9]/', '', $nomor);
            if (str_starts_with($nomor, '0')) {
                $nomor = '+62' . substr($nomor, 1);
            } elseif (str_starts_with($nomor, '8')) {
                $nomor = '+62' . $nomor;
            }

            $pesan = ($status === '-' || $status === 'belum' || empty($status))
                ? "Assalamu'alaikum $nama, ini adalah pengingat untuk pembayaran SPP bulan $bulanInput. Mohon segera melakukan pembayaran sebelum tanggal 10. Terima kasih. Ustadz: $ustadz"
                : "Assalamu'alaikum $nama, terima kasih telah melakukan pembayaran SPP bulan $bulanInput. Semoga Allah memberikan keberkahan untukmu dan keluargamu. Ustadz: $ustadz";

            // Kirim ke Queue
            KirimNotifikasiWA::dispatch($nomor, $pesan);

            $this->info("Pesan dikirim ke: $nama ($kelas) - $nomor");
            $totalTerkirim++;
        }

        $this->info("Total pesan terkirim: $totalTerkirim");
    }
}
