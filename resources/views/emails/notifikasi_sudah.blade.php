<!DOCTYPE html>
<html>

<head>
    <title>Terima Kasih atas Pembayaran SPP</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #10b981;
            padding: 10px;
            border-radius: 6px 6px 0 0;
            color: #ffffff;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #10b981;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Terima Kasih atas Pembayaran SPP</h1>
        </div>

        <div class="content">
            <p>Assalamu'alaikum Wr. Wb.</p>

            <p>Yth. Orang Tua/Wali dari santri <strong>{{ $nama }}</strong> kelas <strong>{{ $kelas }}</strong>,</p>

            <p>Terima kasih, kami telah menerima pembayaran SPP untuk bulan <strong>{{ $bulan }}</strong> pada tanggal
                <strong>{{ $status }}</strong>.</p>

            <p>Semoga Allah SWT membalas dengan rezeki yang berlimpah dan berkah.</p>

            <p>Wassalamu’alaikum Wr. Wb.</p>
            <p><em>Ustadz/Ustadzah {{ $ustadz }}</em></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Pondok Pesantren. Semua Hak Dilindungi.</p>
        </div>
    </div>
</body>

</html>
