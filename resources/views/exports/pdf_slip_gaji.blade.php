<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji Pengajar - Fikra Academy</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 12px;
            line-height: 1.5;
        }
        .container {
            border: 2px double #1e3a8a;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: bold;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #555555;
            font-size: 11px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            letter-spacing: 1px;
            color: #1e3a8a;
            text-decoration: underline;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            width: 150px;
        }
        .meta-value {
            color: #111111;
        }
        .meta-dots {
            border-bottom: 1px dotted #888888;
            width: 250px;
            display: inline-block;
            height: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f1f5f9;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 11px;
        }
        .details-table td {
            padding: 10px;
            border: 1px solid #cbd5e1;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8fafc;
        }
        .terbilang-box {
            border: 1px dashed #cbd5e1;
            background-color: #fafafa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-style: italic;
        }
        .signature-section {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }
        .signature-box.right {
            float: right;
        }
        .signature-space {
            height: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Fikra Academy</h1>
            <p>Jl. Kebon Nanas Selatan III No.05, RT.2/RW.5, Cipinang Cempedak, Kecamatan Jatinegara, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13340</p>
        </div>

        <div class="title">Slip Gaji Pengajar</div>

        <table class="meta-table">
            <tr>
                <td class="meta-label">No. Slip / Ref</td>
                <td>: <span class="meta-value">{{ $ref_no }}</span></td>
                <td class="meta-label">Tanggal Bayar</td>
                <td>: <span class="meta-value">{{ $date }}</span></td>
            </tr>
            <tr>
                <td class="meta-label">Nama Pengajar</td>
                <td colspan="3">: <span class="meta-dots"></span> <span style="font-size: 10px; color: #777; font-style: italic;">(Diisi manual / Tulis tangan)</span></td>
            </tr>
            <tr>
                <td class="meta-label">Metode Pembayaran</td>
                <td colspan="3">: <span class="meta-value">{{ $payment_method }}</span></td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Deskripsi / Rincian Pekerjaan</th>
                    <th style="width: 30%;" class="text-right">Jumlah Uang (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 100px; vertical-align: top;">
                        <strong>{{ $description }}</strong>
                        <p style="margin: 8px 0 0 0; color: #666; font-size: 11px;">
                            Pembayaran Honorarium Mengajar / Gaji Pengajar Fikra Academy sesuai rincian jam/kelas berjalan.
                        </p>
                    </td>
                    <td class="text-right" style="font-weight: bold; font-size: 13px; vertical-align: top;">
                        Rp {{ number_format($amount, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td class="text-right">TOTAL DITERIMA</td>
                    <td class="text-right" style="font-size: 13px; color: #1e3a8a;">
                        Rp {{ number_format($amount, 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="terbilang-box">
            <strong>Terbilang:</strong> {{ $terbilang }} Rupiah
        </div>

        <table class="signature-section">
            <tr>
                <td>
                    <div class="signature-box">
                        <p>Penerima / Pengajar,</p>
                        <div class="signature-space"></div>
                        <p class="signature-name">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                    </div>
                    <div class="signature-box right">
                        <p>Jakarta, {{ $date }}<br>Bendahara Keuangan,</p>
                        <div class="signature-space"></div>
                        <p class="signature-name">( Bendahara Fikra Academy )</p>
                    </div>
                    <div class="clear"></div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
