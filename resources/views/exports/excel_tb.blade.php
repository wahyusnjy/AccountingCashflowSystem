<table>
    <thead>
        <!-- Title Banner -->
        <tr>
            <th colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                FIKRA ACADEMY
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 12px; text-align: center; height: 20px; vertical-align: middle;">
                NERACA SALDO (TRIAL BALANCE)
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 10px; text-align: center; height: 20px; vertical-align: middle; font-style: italic;">
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
            <th colspan="6" style="height: 10px;"></th>
        </tr>
        <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #000000;">
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 15px;">No. Akun</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 30px;">Nama Akun</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 15px;">Tipe Akun</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 18px;">Debit (Rp)</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 18px;">Kredit (Rp)</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 20px;">Saldo Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trialBalance as $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; font-family: monospace;">{{ $item['id'] }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">{{ $item['account_name'] }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ strtoupper($item['type']) }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item['total_debit'] > 0 ? (float)$item['total_debit'] : 0 }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item['total_credit'] > 0 ? (float)$item['total_credit'] : 0 }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ (float)$item['ending_balance'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000;">
            <td colspan="3" style="border: 1px solid #000000; text-align: right; font-weight: bold; height: 25px; vertical-align: middle;">
                TOTAL NERACA SALDO
            </td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">
                {{ (float)$tbTotalDebit }}
            </td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">
                {{ (float)$tbTotalCredit }}
            </td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; color: {{ abs($tbTotalDebit - $tbTotalCredit) < 0.01 ? '#047857' : '#b91c1c' }}">
                @if(abs($tbTotalDebit - $tbTotalCredit) < 0.01)
                    BALANCED
                @else
                    UNBALANCED! Diff: {{ abs($tbTotalDebit - $tbTotalCredit) }}
                @endif
            </td>
        </tr>
    </tfoot>
</table>
