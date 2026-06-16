<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas - Fikra Academy</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 12px;
            margin: 5px 0 0 0;
            color: #555555;
            font-weight: normal;
        }
        .header .period {
            font-size: 10px;
            margin-top: 5px;
            font-style: italic;
            color: #777777;
        }
        .section-header-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .section-balance {
            float: right;
            font-weight: bold;
            font-size: 12px;
            color: #334155;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mutation-plus {
            color: #047857;
            font-weight: bold;
        }
        .mutation-minus {
            color: #475569;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Fikra Academy</h1>
        <h2>Laporan Arus Kas (Rekonsiliasi Kas & Bank)</h2>
        <div class="period">
            @if($startDate && $endDate)
                Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }}
            @elseif($periodMonth)
                Periode Bulan: {{ $periodMonth }}
            @else
                Periode: Semua Data
            @endif
        </div>
    </div>

    <!-- 1. KAS KECIL SECTION -->
    <div class="section-header-box">
        <span class="section-balance">Saldo Akhir: Rp {{ number_format($globalCashBalance, 2, ',', '.') }}</span>
        <h3 class="section-title">1. Kas Kecil H.O (Dompet Fisik) &mdash; 1.10.01.01</h3>
        <div class="clear"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">Tanggal</th>
                <th style="width: 60%;">Keterangan Transaksi</th>
                <th style="width: 25%;" class="text-right">Mutasi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashLedgerEntries as $entry)
                <tr>
                    <td class="text-center">{{ $entry->transaction->date->format('d/m/Y') }}</td>
                    <td>{{ $entry->transaction->description }}</td>
                    <td class="text-right font-semibold {{ $entry->debit > 0 ? 'mutation-plus' : 'mutation-minus' }}">
                        {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 15px; color: #888; font-style: italic;">
                        Belum ada mutasi Kas Kecil pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. REKENING DANA BANK SECTION -->
    <div class="section-header-box">
        <span class="section-balance">Saldo Akhir: Rp {{ number_format($globalBankBalance, 2, ',', '.') }}</span>
        <h3 class="section-title">2. Rekening Dana Bank &mdash; 1.10.02.01</h3>
        <div class="clear"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">Tanggal</th>
                <th style="width: 60%;">Keterangan Transaksi</th>
                <th style="width: 25%;" class="text-right">Mutasi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bankLedgerEntries as $entry)
                <tr>
                    <td class="text-center">{{ $entry->transaction->date->format('d/m/Y') }}</td>
                    <td>{{ $entry->transaction->description }}</td>
                    <td class="text-right font-semibold {{ $entry->debit > 0 ? 'mutation-plus' : 'mutation-minus' }}">
                        {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 15px; color: #888; font-style: italic;">
                        Belum ada mutasi Rekening Bank pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
