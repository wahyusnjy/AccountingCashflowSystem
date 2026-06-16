<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Umum - Fikra Academy</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
            font-size: 10px;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .tx-header-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .tx-desc-row td {
            font-style: italic;
            color: #555555;
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .credit-align {
            padding-left: 20px;
            color: #555555;
        }
        .footer-total {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #1e3a8a;
        }
        .footer-total td {
            border: 1px solid #cbd5e1;
            font-size: 11px;
            padding: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Fikra Academy</h1>
        <h2>Laporan Jurnal Umum (General Ledger)</h2>
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

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">No. Bukti / Ref</th>
                <th style="width: 12%;">Post Ref</th>
                <th style="width: 37%;">Keterangan Akun</th>
                <th style="width: 12%;" class="text-right">Debit (Rp)</th>
                <th style="width: 12%;" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($generalLedger as $tx)
                <tr class="tx-header-row">
                    <td class="text-center">{{ $tx->date->format('d/m/Y') }}</td>
                    <td class="text-center">TX-{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td colspan="4">{{ $tx->description }}</td>
                </tr>
                @foreach($tx->journalEntries as $entry)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="text-center">{{ $entry->account_id }}</td>
                        <td>
                            @if($entry->credit > 0)
                                <div class="credit-align">&mdash; {{ $entry->account->account_name }}</div>
                            @else
                                <div style="font-weight: bold;">{{ $entry->account->account_name }}</div>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ $entry->debit > 0 ? number_format($entry->debit, 2, ',', '.') : '-' }}
                        </td>
                        <td class="text-right">
                            {{ $entry->credit > 0 ? number_format($entry->credit, 2, ',', '.') : '-' }}
                        </td>
                    </tr>
                @endforeach
                <tr class="tx-desc-row">
                    <td></td>
                    <td colspan="5">Keterangan: {{ $tx->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #888888; font-style: italic;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="4" class="text-right">TOTAL / JUMLAH</td>
                <td class="text-right">Rp {{ number_format($totalDebitSum, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalCreditSum, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
