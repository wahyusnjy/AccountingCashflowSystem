<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - Fikra Academy</title>
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
        .section-header {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 20px;
            font-size: 11px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .account-code {
            font-family: Courier, monospace;
            color: #666666;
            font-size: 10px;
        }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }
        .text-right {
            text-align: right;
        }
        .net-profit-card {
            margin-top: 30px;
            padding: 15px;
            border-radius: 6px;
            font-size: 13px;
        }
        .profit {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #064e3b;
        }
        .loss {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }
        .net-profit-label {
            font-weight: bold;
            display: inline-block;
        }
        .net-profit-val {
            font-weight: 900;
            float: right;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Fikra Academy</h1>
        <h2>Laporan Laba Rugi (Profit & Loss)</h2>
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

    <!-- REVENUE SECTION -->
    <div class="section-header">PENDAPATAN USAHA</div>
    <table>
        <tbody>
            @forelse($revenueAccounts as $rev)
                <tr>
                    <td style="width: 15%;" class="account-code">{{ $rev['id'] }}</td>
                    <td style="width: 55%; font-weight: bold;">{{ $rev['account_name'] }}</td>
                    <td style="width: 30%;" class="text-right">Rp {{ number_format($rev['ending_balance'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color: #777; font-style: italic; text-align: center; padding: 10px;">Belum ada pendapatan terinput.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL PENDAPATAN OPERASIONAL</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- EXPENSE SECTION -->
    <div class="section-header">BEBAN USAHA</div>
    <table>
        <tbody>
            @forelse($expenseAccounts as $exp)
                <tr>
                    <td style="width: 15%;" class="account-code">{{ $exp['id'] }}</td>
                    <td style="width: 55%; font-weight: bold;">{{ $exp['account_name'] }}</td>
                    <td style="width: 30%;" class="text-right">Rp {{ number_format($exp['ending_balance'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="color: #777; font-style: italic; text-align: center; padding: 10px;">Belum ada beban operasional terinput.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL BEBAN USAHA</td>
                <td class="text-right">Rp {{ number_format($totalExpense, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- NET RESULT -->
    <div class="net-profit-card {{ $netProfitOrLoss >= 0 ? 'profit' : 'loss' }}">
        <span class="net-profit-label">
            {{ $netProfitOrLoss >= 0 ? 'LABA BERSIH TAHUN BERJALAN' : 'RUGI BERSIH TAHUN BERJALAN' }}
        </span>
        <span class="net-profit-val">
            Rp {{ number_format($netProfitOrLoss, 2, ',', '.') }}
        </span>
        <div class="clear"></div>
    </div>

</body>
</html>
