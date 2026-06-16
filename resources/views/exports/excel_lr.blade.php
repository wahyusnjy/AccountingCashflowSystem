<table>
    <thead>
        <!-- Title Banner -->
        <tr>
            <th colspan="3" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                FIKRA ACADEMY
            </th>
        </tr>
        <tr>
            <th colspan="3" style="font-size: 12px; text-align: center; height: 20px; vertical-align: middle;">
                LAPORAN LABA RUGI (PROFIT AND LOSS)
            </th>
        </tr>
        <tr>
            <th colspan="3" style="font-size: 10px; text-align: center; height: 20px; vertical-align: middle; font-style: italic;">
                @if($startDate && $endDate)
                    Periode: {{ $startDate }} s.d {{ $endDate }}
                @elseif($periodMonth)
                    Periode Bulan: {{ $periodMonth }}
                @else
                    Periode: Semua Data
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="3" style="height: 10px;"></th>
        </tr>
        <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #000000;">
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 15px;">No. Akun</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 45px;">Nama Akun / Keterangan</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 22px;">Saldo (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <!-- PENDAPATAN SECTION HEADER -->
        <tr style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #000000;">
            <td colspan="2" style="border: 1px solid #000000; text-align: left; font-weight: bold; height: 22px; vertical-align: middle;">PENDAPATAN USAHA</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">Saldo</td>
        </tr>
        @foreach($revenueAccounts as $rev)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; font-family: monospace;">{{ $rev['id'] }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">{{ $rev['account_name'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ (float)$rev['ending_balance'] }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #f0f4ff; font-weight: bold;">
            <td colspan="2" style="border: 1px solid #000000; text-align: right; font-weight: bold; height: 22px; vertical-align: middle;">TOTAL PENDAPATAN OPERASIONAL</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">{{ (float)$totalRevenue }}</td>
        </tr>

        <!-- Space -->
        <tr>
            <td colspan="3" style="height: 15px;"></td>
        </tr>

        <!-- BEBAN SECTION HEADER -->
        <tr style="background-color: #e2e8f0; font-weight: bold; border: 1px solid #000000;">
            <td colspan="2" style="border: 1px solid #000000; text-align: left; font-weight: bold; height: 22px; vertical-align: middle;">BEBAN USAHA</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">Saldo</td>
        </tr>
        @foreach($expenseAccounts as $exp)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; font-family: monospace;">{{ $exp['id'] }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">{{ $exp['account_name'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ (float)$exp['ending_balance'] }}</td>
            </tr>
        @endforeach
        <tr style="background-color: #f3f4f6; font-weight: bold;">
            <td colspan="2" style="border: 1px solid #000000; text-align: right; font-weight: bold; height: 22px; vertical-align: middle;">TOTAL BEBAN USAHA</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">{{ (float)$totalExpense }}</td>
        </tr>

        <!-- Space -->
        <tr>
            <td colspan="3" style="height: 15px;"></td>
        </tr>

        <!-- NET PROFIT/LOSS -->
        <tr style="background-color: {{ $netProfitOrLoss >= 0 ? '#ecfdf5' : '#fff1f2' }}; font-weight: bold; border: 2px solid #000000;">
            <td colspan="2" style="border: 1px solid #000000; text-align: right; font-weight: bold; font-size: 11px; height: 28px; vertical-align: middle;">
                {{ $netProfitOrLoss >= 0 ? 'LABA BERSIH TAHUN BERJALAN' : 'RUGI BERSIH TAHUN BERJALAN' }}
            </td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; font-size: 11px; vertical-align: middle; color: {{ $netProfitOrLoss >= 0 ? '#047857' : '#b91c1c' }};">
                {{ (float)$netProfitOrLoss }}
            </td>
        </tr>
    </tbody>
</table>
