<?php

namespace App\Http\Controllers;

use App\Jobs\KirimNotifikasiWA;
use App\Mail\NotifikasiPembayaranMail;
use App\Models\Kelas;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MasterNotificationController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function index(Request $request)
    {
        // Ambil list kode kelas dari tabel Kelas
        $kelasList = Kelas::all();

        // Ambil parameter kode (jika ada)
        $kode = $request->get('kode');

        // Ambil halaman saat ini (default 1)
        $currentPage = $request->get('page', 1);
        $perPage = 20;

        // Ambil data dari API (default sheet A)
        $response = Http::get('https://api.sheetbest.com/sheets/632e9480-5bcf-4851-b68b-a979baa6bea8');
        $rawData = $response->json();

        // Buang baris pertama (header)
        $rows = collect($rawData)->skip(1);

        // Filter jika ada kode, tampilkan semua kalau tidak ada
        $filtered = $rows->when($kode, function ($query) use ($kode) {
            return $query->filter(function ($item) use ($kode) {
                return isset($item['2']) && $item['2'] == $kode;
            });
        })->values();

        // Potong data sesuai halaman
        $paginated = $filtered->forPage($currentPage, $perPage);

        // Buat pagination manual (paginator)
        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('master', compact('kelasList', 'data', 'kode'));
    }

    // NotifController.php
    public function kirim(Request $request, $id)
    {
        $data = $request->all();
        // dd($data);

        // Tambahkan nomor WA, misalnya dari field `wa`
        if (empty($data['wa'])) {
            return response()->json(['message' => 'Nomor WhatsApp tidak tersedia'], 400);
        }

        // KirimNotifikasiWA::dispatch($data);
        $nama = $data['nama'];
        $kelas = $data['kelas'];
        $bulan = $data['bulan'];
        $ustadz = $data['ustadz'];
        $nomor = preg_replace('/[^0-9]/', '', $data['wa']);

        // Check if number starts with 0
        if (str_starts_with($nomor, '0')) {
            // Replace leading 0 with 62
            $nomor = '+62' . substr($nomor, 1);
        }
        // Check if number starts with 8
        elseif (str_starts_with($nomor, '8')) {
            // Add 62 before the number
            $nomor = '+62' . $nomor;
        }
        // If number already starts with 62, leave it as is
        $status = strtolower($data['status'] ?? '-');
        // dd($status);

        if ($status === '-' || $status === 'belum' || empty($status)) {
            $pesan = "Assalamu'alaikum $nama, ini adalah pengingat untuk pembayaran SPP bulan $bulan. Mohon segera melakukan pembayaran sebelum tanggal 10. Terima kasih. Ustadz: $ustadz";
        } else {
            $pesan = "Assalamu'alaikum $nama, terima kasih telah melakukan pembayaran SPP bulan $bulan. Semoga Allah memberikan keberkahan untukmu dan keluargamu. Ustadz: $ustadz";
        }


        // Dispatch ke Queue
        KirimNotifikasiWA::dispatch($nomor, $pesan);

        return response()->json(['message' => 'Notifikasi WhatsApp sedang diproses']);
    }

    public function email(Request $request, $id)
    {

        $data = $request->all();
        // Default tujuan email
        $tujuan = 'giantzord@gmail.com';

        // Kirim email
        Mail::to($tujuan)->send(new NotifikasiPembayaranMail($data));

        return response()->json(['message' => 'Email berhasil dikirim ke wali santri ' . $data['nama']]);
    }
}
