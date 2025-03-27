<?php

namespace App\Http\Controllers;

use App\Mail\NotifikasiPembayaranMail;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MasterNotificationController extends Controller
{

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
        dd($request->all());
        // TODO: logic kirim WA/email
        return response()->json(['message' => 'Notifikasi WhatsApp berhasil dikirim ke santri ID ' . $id]);
    }

    public function email(Request $request, $id)
    {

        $data = $request->all();
        // Default tujuan email
        $tujuan = 'eskohengdelapansembilan@gmail.com';

        // Kirim email
        Mail::to($tujuan)->send(new NotifikasiPembayaranMail($data));

        return response()->json(['message' => 'Email berhasil dikirim ke santri ID ' . $id]);
    }
}
