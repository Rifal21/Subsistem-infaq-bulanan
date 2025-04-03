<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Master Notification') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="mb-4">
                {{-- Filter Dropdown --}}
                <form method="GET">
                    @csrf
                    <label for="kode" class="font-semibold text-gray-700 dark:text-gray-300">Filter Kelas:</label>
                    <select name="kode" id="kode"
                        class="border p-2 rounded text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->kode }}" {{ request('kode') == $kelas->kode ? 'selected' : '' }}>
                                {{ $kelas->nama }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Filter Bulan --}}
                    <label for="bulan" class="ml-4 font-semibold text-gray-700 dark:text-gray-300">Bulan:</label>
                    <select name="bulan" id="bulan"
                        class="border p-2 rounded text-sm dark:bg-gray-700 dark:text-white">
                        @php
                            $bulanOptions = [
                                'JULI',
                                'AUG',
                                'SEPT',
                                'OKT',
                                'NOP',
                                'DES',
                                'JAN',
                                'FEB',
                                'MAR',
                                'APR',
                                'MEI',
                                'JUN',
                            ];
                        @endphp
                        @foreach ($bulanOptions as $bulan)
                            <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="ml-2 px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">Terapkan</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 overflow-x-auto">
                @php
                    $namaBulan = strtoupper(request('bulan') ?? now()->format('M'));

                    $mapBulan = [
                        'JAN' => 14,
                        'FEB' => 15,
                        'MAR' => 16,
                        'APR' => 17,
                        'MEI' => 18,
                        'JUN' => 19,
                        'JULI' => 8,
                        'AUG' => 9,
                        'SEPT' => 10,
                        'OKT' => 11,
                        'NOP' => 12,
                        'DES' => 13,
                    ];

                    $kolomBulan = $mapBulan[$namaBulan] ?? null;
                @endphp
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2">NO</th>
                            <th class="px-3 py-2">NAMA SANTRI</th>
                            <th class="px-3 py-2">KLS</th>
                            <th class="px-3 py-2">L/P</th>
                            <th class="px-3 py-2">USTADZ</th>
                            <th class="px-3 py-2">Whatsapp</th>
                            @if ($kolomBulan)
                                <th class="px-3 py-2">{{ $namaBulan }}</th>
                            @endif
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600 text-gray-900 dark:text-gray-100">
                        @forelse ($data as $index => $item)
                            @php
                                $nilaiBulan = $kolomBulan ? $item[$kolomBulan] ?? null : null;
                            @endphp
                            <tr>
                                <td class="px-3 py-2">{{ $item[0] ?? $index + 1 }}</td>
                                <td class="px-3 py-2">{{ $item[1] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $item[2] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $item[3] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $item[4] ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $item[5] ?? '-' }}</td>
                                @if ($kolomBulan)
                                    <td class="px-3 py-2">{{ $item[$kolomBulan] ?? '-' }}</td>
                                @endif
                                <td class="px-3 py-2">
                                    <span
                                        class="badge-status cursor-pointer inline-block w-4 h-4 rounded-full {{ $nilaiBulan ? 'bg-green-800' : 'bg-red-800' }}"
                                        data-id="{{ $item[0] }}"
                                        data-status="{{ $nilaiBulan ? 'sudah' : 'belum' }}">
                                    </span>
                                </td>
                                <td class="px-3 py-2 flex space-x-2">
                                    <a href="#"
                                        class="text-sm bg-blue-600 hover:bg-blue-800 send-mail text-white rounded-lg p-2"
                                        data-id="{{ $item[0] }}" data-nama="{{ $item[1] }}"
                                        data-kelas="{{ $item[2] }}" data-gender="{{ $item[3] }}"
                                        data-ustadz="{{ $item[4] }}" data-bulan="{{ $namaBulan }}"
                                        data-status="{{ $item[$kolomBulan] ?? '-' }}">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                    <a href="#"
                                        class="text-sm bg-green-600 hover:bg-green-800 send-whatsapp text-white rounded-lg p-2"
                                        data-id="{{ $item[0] }}" data-nama="{{ $item[1] }}"
                                        data-kelas="{{ $item[2] }}" data-gender="{{ $item[3] }}"
                                        data-ustadz="{{ $item[4] }}" data-bulan="{{ $namaBulan }}"
                                        data-wa="{{ $item[5] }}" data-status="{{ $item[$kolomBulan] ?? '-' }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center px-3 py-4">Tidak ada data untuk kelas yang
                                    dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.send-whatsapp').forEach(badge => {
                    badge.addEventListener('click', () => {
                        const id = badge.dataset.id;
                        const status = badge.dataset.status;

                        if (status === 'belum' || status === '-') {
                            Swal.fire({
                                title: 'Kirim Notifikasi?',
                                text: 'Santri ini belum bayar. Kirim pesan pengingat via WhatsApp?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Kirim',
                                cancelButtonText: 'Batal'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    fetch(`/kirim-notifikasi/${id}`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                id: badge.dataset.id,
                                                nama: badge.dataset.nama,
                                                kelas: badge.dataset.kelas,
                                                gender: badge.dataset.gender,
                                                ustadz: badge.dataset.ustadz,
                                                bulan: badge.dataset.bulan,
                                                wa: badge.dataset.wa,
                                                status: badge.dataset.status
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(res => {
                                            Swal.fire('Terkirim!', res.message ||
                                                'Notifikasi berhasil dikirim.',
                                                'success');
                                        });
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Kirim Notifikasi?',
                                text: 'Santri ini sudah bayar. Kirim pesan via WhatsApp?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Kirim',
                                cancelButtonText: 'Batal'
                            }).then(result => {
                                if (result.isConfirmed) {
                                    fetch(`/kirim-notifikasi/${id}`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            id: badge.dataset.id,
                                            nama: badge.dataset.nama,
                                            kelas: badge.dataset.kelas,
                                            gender: badge.dataset.gender,
                                            ustadz: badge.dataset.ustadz,
                                            bulan: badge.dataset.bulan,
                                            wa: badge.dataset.wa,
                                            status: badge.dataset.status
                                        })
                                    });
                                }
                            });
                        }
                    });
                });


                // Klik tombol email
                document.querySelectorAll('.send-mail').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();

                        const id = button.dataset.id;
                        const nama = button.dataset.nama;
                        const kelas = button.dataset.kelas;
                        const gender = button.dataset.gender;
                        const ustadz = button.dataset.ustadz;
                        const bulan = button.dataset.bulan;
                        const status = button.dataset.status;

                        Swal.fire({
                            title: 'Kirim Email?',
                            text: `Kirim email ke ${nama} (${kelas}) untuk bulan ${bulan}?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal'
                        }).then(result => {
                            if (result.isConfirmed) {
                                fetch(`/kirim-email/${id}`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            id,
                                            nama,
                                            kelas,
                                            gender,
                                            ustadz,
                                            bulan,
                                            status
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(res => {
                                        Swal.fire('Terkirim!', res.message ||
                                            'Email berhasil dikirim.', 'success');
                                    });
                            }
                        });
                    });
                });

            });
        </script>
    @endpush

    {{-- Session Alerts --}}
    @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
            <script>
                Swal.fire({
                    icon: '{{ $type }}',
                    title: '{{ ucfirst($type) }}!',
                    text: '{{ session($type) }}'
                });
            </script>
        @endif
    @endforeach
</x-app-layout>
