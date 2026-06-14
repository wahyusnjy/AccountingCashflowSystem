<table>
    <thead>
        <!-- Title Banner -->
        <tr>
            <th colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                LAPORAN JURNAL UMUM (GENERAL LEDGER)
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 12px; text-align: center; height: 20px; vertical-align: middle;">
                CASHFLOW BIMBEL
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
        <!-- Table Header -->
        <tr style="background-color: #4f46e5; color: #ffffff; font-weight: bold; border: 1px solid #000000;">
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 12px;">Tanggal</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 15px;">No. Bukti / Ref</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 35px;">Keterangan Akun</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 12px;">Ref Post</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 18px;">Debit (Rp)</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 18px;">Kredit (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($generalLedger as $tx)
            @foreach($tx->journalEntries as $entry)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                        {{ $loop->first ? $tx->date->format('Y-m-d') : '' }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                        {{ $loop->first ? 'TX-' . str_pad($tx->id, 5, '0', STR_PAD_LEFT) : '' }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle;">
                        @if($entry->credit > 0)
                            <!-- Indent Credit account name for standard ledger formatting -->
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $entry->account->account_name }}
                        @else
                            {{ $entry->account->account_name }}
                        @endif
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                        {{ $entry->account_id }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle;">
                        {{ $entry->debit > 0 ? (float)$entry->debit : 0 }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle;">
                        {{ $entry->credit > 0 ? (float)$entry->credit : 0 }}
                    </td>
                </tr>
            @endforeach
            <!-- Keterangan Transaksi Row -->
            <tr>
                <td style="border: 1px solid #000000;"></td>
                <td colspan="5" style="border: 1px solid #000000; font-style: italic; color: #555555; text-align: left;">
                    Keterangan: {{ $tx->description }}
                </td>
            </tr>
            <!-- Blank divider row between transactions for visual clarity -->
            <tr style="height: 5px;">
                <td colspan="6" style="border-left: 1px solid #000000; border-right: 1px solid #000000;"></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <!-- Total Sum row -->
        <tr style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000;">
            <td colspan="4" style="border: 1px solid #000000; text-align: right; font-weight: bold; height: 25px; vertical-align: middle;">
                TOTAL / JUMLAH
            </td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">
                {{ (float)$totalDebitSum }}
            </td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">
                {{ (float)$totalCreditSum }}
            </td>
        </tr>
    </tfoot>
</table>
