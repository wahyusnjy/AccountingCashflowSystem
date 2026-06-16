<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca Saldo - Fikra Academy</title>
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
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: Courier, monospace;
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
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-info {
            background-color: #f1f5f9;
            color: #334155;
        }
        .status-balanced {
            color: #047857;
            font-weight: bold;
        }
        .status-unbalanced {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Fikra Academy</h1>
        <h2>Laporan Neraca Saldo (Trial Balance)</h2>
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
                <th style="width: 15%;">No. Akun</th>
                <th style="width: 35%;">Keterangan / Nama Akun</th>
                <th style="width: 15%;">Tipe Akun</th>
                <th style="width: 11%;" class="text-right">Debit (Rp)</th>
                <th style="width: 11%;" class="text-right">Kredit (Rp)</th>
                <th style="width: 13%;" class="text-right">Saldo Akhir (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trialBalance as $item)
                <tr>
                    <td class="text-center font-mono">{{ $item['id'] }}</td>
                    <td style="font-weight: bold;">{{ $item['account_name'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ strtoupper($item['type']) }}</span>
                    </td>
                    <td class="text-right">
                        {{ $item['total_debit'] > 0 ? number_format($item['total_debit'], 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $item['total_credit'] > 0 ? number_format($item['total_credit'], 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($item['ending_balance'], 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="3" class="text-right">TOTAL NERACA SALDO</td>
                <td class="text-right">Rp {{ number_format($tbTotalDebit, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tbTotalCredit, 2, ',', '.') }}</td>
                <td class="text-center">
                    @if(abs($tbTotalDebit - $tbTotalCredit) < 0.01)
                        <span class="status-balanced">BALANCED ✓</span>
                    @else
                        <span class="status-unbalanced">UNBALANCED!<br>Diff: Rp {{ number_format(abs($tbTotalDebit - $tbTotalCredit), 2, ',', '.') }}</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
