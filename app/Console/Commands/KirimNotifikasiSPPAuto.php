<?php

namespace App\Console\Commands;

use App\Jobs\KirimEmailNotifikasiSPP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifikasiPembayaranMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class KirimNotifikasiSPPAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:kirim-notifikasi-s-p-p-auto';
    protected $signature = 'app:kirim-notifikasi-s-p-p-auto {--bulan=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim email notifikasi otomatis untuk santri yang belum bayar SPP hingga tanggal 10 setiap bulannya.';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    //     $today = Carbon::today();

    //     if ($today->day !== 10) {
    //         $this->info('Hari ini bukan tanggal 10. Cron tidak dijalankan.');
    //         return;
    //     }

    //     $bulanSekarang = strtoupper($today->format('M'));

    //     $mapBulan = [
    //         'JAN' => 14,
    //         'FEB' => 15,
    //         'MAR' => 16,
    //         'APR' => 17,
    //         'MEI' => 18,
    //         'JUN' => 19,
    //         'JULI' => 6,
    //         'AUG' => 7,
    //         'SEPT' => 8,
    //         'OKT' => 9,
    //         'NOP' => 10,
    //         'DES' => 11,
    //     ];

    //     $kolomBulan = $mapBulan[$bulanSekarang] ?? null;

    //     if (!$kolomBulan) {
    //         $this->error("Kolom untuk bulan $bulanSekarang tidak ditemukan.");
    //         return;
    //     }

    //     // Ganti dengan nama view/tabel yang sesuai
    //     $santri = DB::table('view_pembayaran')
    //         ->whereNull("column_$kolomBulan")
    //         ->orWhere("column_$kolomBulan", '-')
    //         ->get();

    //     if ($santri->isEmpty()) {
    //         $this->info("Tidak ada santri yang perlu dikirimi notifikasi.");
    //         return;
    //     }

    //     foreach ($santri as $s) {
    //         $data = [
    //             'id'     => $s->id,
    //             'nama'   => $s->nama_santri ?? $s->nama,
    //             'kelas'  => $s->kelas,
    //             'gender' => $s->gender,
    //             'ustadz' => $s->ustadz,
    //             'bulan'  => $bulanSekarang,
    //             'status' => $s->{"column_$kolomBulan"} ?? '-',
    //         ];

    //         Mail::to('eskohengdelapansembilan@gmail.com')
    //             ->send(new NotifikasiPembayaranMail($data));
    //     }

    //     $this->info('Notifikasi berhasil dikirim ke ' . $santri->count() . ' santri.');
    // }
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
            'JULI' => '6',
            'AUG' => '7',
            'SEPT' => '8',
            'OKT' => '9',
            'NOP' => '10',
            'DES' => '11',
        ];

        $kolomBulan = $mapBulan[$bulanInput] ?? null;

        if (!$kolomBulan) {
            $this->error("Bulan $bulanInput tidak valid.");
            return;
        }

        // Ambil data dari API
        $response = Http::get('https://api.sheetbest.com/sheets/632e9480-5bcf-4851-b68b-a979baa6bea8');
        $rawData = $response->json();
        $rows = collect($rawData)->skip(1); // Buang header

        // Filter data yang belum bayar (status kosong atau '-')
        $santri = $rows->filter(function ($item) use ($kolomBulan) {
            return empty($item[$kolomBulan]) || $item[$kolomBulan] === '-';
        });

        if ($santri->isEmpty()) {
            $this->info("Tidak ada data santri dengan status kosong untuk bulan $bulanInput.");
            return;
        }

        foreach ($santri as $s) {
            $data = [
                'id'     => $s['0'], // kolom ID
                'nama'   => $s['1'],
                'kelas'  => $s['2'],
                'gender' => $s['3'],
                'ustadz' => $s['4'],
                'bulan'  => $bulanInput,
                'status' => $s[$kolomBulan] ?? '-',
            ];

            KirimEmailNotifikasiSPP::dispatch($data);
            $this->info("Queued email untuk: {$data['nama']} ($bulanInput)");
        }

        $this->info("Selesai queue email notifikasi ke {$santri->count()} santri.");
    }
}
