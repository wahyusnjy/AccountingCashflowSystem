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
                LAPORAN ARUS KAS (REKONSILIASI KAS DAN BANK)
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
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 15px;">Tanggal</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 45px;">Keterangan Transaksi</th>
            <th style="border: 1px solid #000000; text-align: center; font-weight: bold; width: 22px;">Mutasi (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <!-- KAS KECIL SECTION HEADER -->
        <tr style="background-color: #047857; color: #ffffff; font-weight: bold; border: 1px solid #000000;">
            <td colspan="2" style="border: 1px solid #000000; text-align: left; font-weight: bold; height: 22px; vertical-align: middle;">1. KAS KECIL H.O (DOMPET FISIK)</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; width: 22px; vertical-align: middle;">Saldo: Rp {{ number_format($globalCashBalance, 2, ',', '.') }}</td>
        </tr>
        @forelse($cashLedgerEntries as $entry)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $entry->transaction->date->format('Y-m-d') }}</td>
                <td style="border: 1px solid #000000; text-align: left;">{{ $entry->transaction->description }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold; color: {{ $entry->debit > 0 ? '#047857' : '#555555' }}">
                    {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="border: 1px solid #000000; text-align: center; font-style: italic; color: #555555;">Belum ada mutasi Kas Kecil.</td>
            </tr>
        @endforelse

        <!-- Space -->
        <tr>
            <td colspan="3" style="height: 20px;"></td>
        </tr>

        <!-- REKENING DANA BANK SECTION HEADER -->
        <tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #000000;">
            <td colspan="2" style="border: 1px solid #000000; text-align: left; font-weight: bold; height: 22px; vertical-align: middle;">2. REKENING DANA BANK</td>
            <td style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle;">Saldo: Rp {{ number_format($globalBankBalance, 2, ',', '.') }}</td>
        </tr>
        @forelse($bankLedgerEntries as $entry)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $entry->transaction->date->format('Y-m-d') }}</td>
                <td style="border: 1px solid #000000; text-align: left;">{{ $entry->transaction->description }}</td>
                <td style="border: 1px solid #000000; text-align: right; font-weight: bold; color: {{ $entry->debit > 0 ? '#1e3a8a' : '#555555' }}">
                    {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="border: 1px solid #000000; text-align: center; font-style: italic; color: #555555;">Belum ada mutasi Rekening Bank.</td>
            </tr>
        @endforelse
    </tbody>
</table>
