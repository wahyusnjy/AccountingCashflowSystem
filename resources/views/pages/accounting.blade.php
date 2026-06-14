@extends('layouts.app')

@section('title', 'Laporan Akuntansi & Rekonsiliasi - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pelaporan Akuntansi & General Ledger</h2>
        <p class="text-sm text-slate-500 mt-1">Laporan double-entry otomatis untuk audit keuangan Fikra Academy.</p>
    </div>
    <div>
        <a id="export-excel-btn" href="{{ route('export.excel', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl text-sm transition-all shadow-md shadow-emerald-600/10 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Ekspor Jurnal Umum (Excel)
        </a>
    </div>
</div>

<!-- Tabs Navigation Header -->
<div id="accounting-tabs" class="flex border-b border-slate-200 bg-white rounded-t-2xl shadow-sm overflow-hidden">
    <button onclick="switchTab('tab-gl', this)" class="tab-btn active px-6 py-4 text-sm font-bold border-b-2 border-[#1e3a8a] text-[#1e3a8a] focus:outline-none transition-colors">
        1. Jurnal Umum (General Ledger)
    </button>
    <button onclick="switchTab('tab-tb', this)" class="tab-btn px-6 py-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 focus:outline-none transition-colors">
        2. Neraca Saldo (Trial Balance)
    </button>
    <button onclick="switchTab('tab-lr', this)" class="tab-btn px-6 py-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 focus:outline-none transition-colors">
        3. Laporan Laba Rugi (Profit & Loss)
    </button>
    <button onclick="switchTab('tab-reconcile', this)" class="tab-btn px-6 py-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 focus:outline-none transition-colors">
        4. Rekonsiliasi Kas & Bank
    </button>
</div>

<!-- Tab Contents Container -->
<div id="accounting-data-tables" class="bg-white p-6 rounded-b-2xl border-x border-b border-slate-200 shadow-sm">

    <!-- ============================================== -->
    <!-- TAB 1: GENERAL LEDGER -->
    <!-- ============================================== -->
    <div id="tab-gl" class="tab-content block space-y-6">
        <!-- GL Filter Bar -->
        <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('accounting.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="active_tab" value="tab-gl">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bulan SPP</label>
                    <select name="period_month" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs bg-white focus:outline-[#1e3a8a]">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}" {{ request('period_month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-slate-300 font-light mt-4 text-xs">atau</div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Rentang Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs focus:outline-[#1e3a8a]">
                        <span class="text-xs text-slate-400">s/d</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs focus:outline-[#1e3a8a]">
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg text-xs transition-all shadow-sm">
                        Filter Ledger
                    </button>
                    @if(request()->anyFilled(['period_month', 'start_date', 'end_date']))
                        <a href="{{ route('accounting.index', ['active_tab' => 'tab-gl']) }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-lg text-xs transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- GL Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="p-4 text-xs uppercase tracking-wider">No. Bukti</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Kode Akun</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Keterangan / Nama Akun</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-right">Debit (Rp)</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-right">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($generalLedger as $tx)
                        <!-- Transaction Row Head -->
                        <tr class="bg-slate-50/50">
                            <td class="p-4 font-semibold text-slate-900 border-r border-slate-100">
                                {{ $tx->date->format('d/m/Y') }}
                            </td>
                            <td class="p-4 font-mono text-xs text-slate-500">
                                TX-{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td colspan="4" class="p-4 font-bold text-slate-800">
                                {{ $tx->description }}
                            </td>
                        </tr>
                        <!-- Journal Entries for this Transaction -->
                        @foreach($tx->journalEntries as $entry)
                            <tr class="hover:bg-slate-50/10">
                                <td class="p-4 border-r border-slate-100"></td>
                                <td class="p-4"></td>
                                <td class="p-4 font-mono text-xs text-slate-500">
                                    {{ $entry->account_id }}
                                </td>
                                <td class="p-4">
                                    <!-- Indentation for credit accounts -->
                                    <div class="flex items-center gap-2 {{ $entry->credit > 0 ? 'pl-8 text-slate-500 font-medium' : 'text-slate-900 font-semibold' }}">
                                        @if($entry->credit > 0)
                                            <span class="text-slate-400">&mdash;</span>
                                        @endif
                                        {{ $entry->account->account_name }}
                                    </div>
                                </td>
                                <td class="p-4 text-right font-medium text-slate-700">
                                    {{ $entry->debit > 0 ? number_format($entry->debit, 2, ',', '.') : '-' }}
                                </td>
                                <td class="p-4 text-right font-medium text-slate-700">
                                    {{ $entry->credit > 0 ? number_format($entry->credit, 2, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                Tidak ada transaksi yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 2: TRIAL BALANCE -->
    <!-- ============================================== -->
    <div id="tab-tb" class="tab-content hidden space-y-6">
        <div class="bg-blue-50 border border-blue-150 rounded-xl p-4 text-blue-900 text-xs md:text-sm flex gap-3">
            <svg class="h-5 w-5 text-blue-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="font-bold">Penjelasan Neraca Saldo:</span> Akumulasi debit dan kredit per akun pembukuan. Untuk menjamin akurasi GL, total saldo kolom debit dan kredit harus seimbang (Balanced). Selisih tidak seimbang akan ditandai dengan peringatan merah.
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs uppercase tracking-wider">No. Akun</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Keterangan / Nama Akun</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-center">Tipe Akun</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-right font-bold">Debit (Rp)</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-right font-bold">Kredit (Rp)</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-right font-bold">Saldo Akhir (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($trialBalance as $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-4 font-mono text-xs text-slate-500">{{ $item['id'] }}</td>
                            <td class="p-4 font-bold text-slate-800">{{ $item['account_name'] }}</td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ strtoupper($item['type']) }}
                                </span>
                            </td>
                            <td class="p-4 text-right text-slate-600 font-medium">
                                {{ $item['total_debit'] > 0 ? number_format($item['total_debit'], 2, ',', '.') : '-' }}
                            </td>
                            <td class="p-4 text-right text-slate-600 font-medium">
                                {{ $item['total_credit'] > 0 ? number_format($item['total_credit'], 2, ',', '.') : '-' }}
                            </td>
                            <td class="p-4 text-right font-black text-slate-900 bg-slate-50/30">
                                Rp {{ number_format($item['ending_balance'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="p-4 text-right font-bold text-[#1e3a8a]">TOTAL NERACA SALDO</td>
                        <td class="p-4 text-right text-[#1e3a8a] text-sm font-black border-r border-slate-200">
                            Rp {{ number_format($tbTotalDebit, 2, ',', '.') }}
                        </td>
                        <td class="p-4 text-right text-[#1e3a8a] text-sm font-black border-r border-slate-200">
                            Rp {{ number_format($tbTotalCredit, 2, ',', '.') }}
                        </td>
                        <td class="p-4 text-center bg-slate-100/50">
                            @if(abs($tbTotalDebit - $tbTotalCredit) < 0.01)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Balanced ✓
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">
                                    Unbalanced! Diff: Rp {{ number_format(abs($tbTotalDebit - $tbTotalCredit), 2, ',', '.') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 3: LABA RUGI -->
    <!-- ============================================== -->
    <div id="tab-lr" class="tab-content hidden max-w-3xl mx-auto space-y-6">
        <div class="border border-slate-200 shadow-sm rounded-2xl overflow-hidden bg-white">
            <div class="bg-[#1e3a8a] text-white p-6 text-center">
                <h3 class="text-xl font-bold tracking-tight">LAPORAN LABA RUGI</h3>
                <p class="text-indigo-150 text-xs mt-1">FIKRA ACADEMY &mdash; Periode Berjalan 2025-2026</p>
            </div>
            
            <div class="p-6 md:p-8 space-y-8">
                <!-- PENDAPATAN USAHA -->
                <div>
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center justify-between">
                        <span>PENDAPATAN USAHA</span>
                        <span>Saldo Terakumulasi</span>
                    </h4>
                    <ul class="divide-y divide-slate-100 mt-3">
                        @forelse($revenueAccounts as $rev)
                            <li class="py-3 flex justify-between items-center text-sm">
                                <span class="text-slate-700 font-bold">
                                    {{ $rev['account_name'] }}
                                    <code class="text-slate-400 font-mono text-[10px] ml-2">({{ $rev['id'] }})</code>
                                </span>
                                <span class="font-extrabold text-slate-900">
                                    Rp {{ number_format($rev['ending_balance'], 2, ',', '.') }}
                                </span>
                            </li>
                        @empty
                            <li class="py-3 text-slate-500 text-xs italic">Belum ada pendapatan terinput.</li>
                        @endforelse
                    </ul>
                    <div class="flex justify-between items-center bg-[#f0f4ff]/50 p-4 rounded-xl mt-4 border border-[#e2e8f0]">
                        <span class="font-black text-[#1e3a8a] text-sm">TOTAL PENDAPATAN OPERASIONAL</span>
                        <span class="font-black text-[#1e3a8a] text-base">Rp {{ number_format($totalRevenue, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- BEBAN USAHA -->
                <div>
                    <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2 flex items-center justify-between">
                        <span>BEBAN USAHA</span>
                        <span>Saldo Terakumulasi</span>
                    </h4>
                    <ul class="divide-y divide-slate-100 mt-3">
                        @forelse($expenseAccounts as $exp)
                            <li class="py-3 flex justify-between items-center text-sm">
                                <span class="text-slate-700 font-bold">
                                    {{ $exp['account_name'] }}
                                    <code class="text-slate-400 font-mono text-[10px] ml-2">({{ $exp['id'] }})</code>
                                </span>
                                <span class="font-extrabold text-slate-900">
                                    Rp {{ number_format($exp['ending_balance'], 2, ',', '.') }}
                                </span>
                            </li>
                        @empty
                            <li class="py-3 text-slate-500 text-xs italic">Belum ada biaya operasional terinput.</li>
                        @endforelse
                    </ul>
                    <div class="flex justify-between items-center bg-slate-50 p-4 rounded-xl mt-4 border border-slate-200">
                        <span class="font-black text-slate-800 text-sm">TOTAL BEBAN USAHA</span>
                        <span class="font-black text-slate-800 text-base">Rp {{ number_format($totalExpense, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- NET PROFIT -->
                <div class="pt-4 border-t border-slate-250">
                    <div class="p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $netProfitOrLoss >= 0 ? 'bg-emerald-50 text-emerald-950 border border-emerald-200' : 'bg-rose-50 text-rose-950 border border-rose-200' }}">
                        <div>
                            <h4 class="text-lg font-black tracking-tight flex items-center gap-2">
                                @if($netProfitOrLoss >= 0)
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    Laba Bersih Tahun Berjalan
                                @else
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                    Rugi Bersih Tahun Berjalan
                                @endif
                            </h4>
                            <p class="text-xs mt-1 opacity-80">Surplus total pendapatan terhadap beban biaya akademi.</p>
                        </div>
                        <div class="text-2xl md:text-3xl font-black {{ $netProfitOrLoss >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                            Rp {{ number_format($netProfitOrLoss, 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 4: REKONSILIASI KAS & BANK -->
    <!-- ============================================== -->
    <div id="tab-reconcile" class="tab-content hidden space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- KAS KECIL WALLET LEDGER -->
            <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-base">Kas Kecil H.O (Dompet Fisik)</h3>
                                <p class="text-[10px] text-slate-400 uppercase font-mono mt-0.5">Kode CoA: 1.10.01.01</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-wider">Saldo Kas</span>
                            <span class="text-lg font-black text-emerald-600">Rp {{ number_format($globalCashBalance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Cash ledger events -->
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        @forelse($cashLedgerEntries as $entry)
                            <div class="flex justify-between items-center p-3 hover:bg-slate-50 rounded-xl transition-all border border-slate-100/30">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block truncate max-w-[200px]">{{ $entry->transaction->description }}</span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $entry->transaction->date->format('d M Y') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-extrabold block {{ $entry->debit > 0 ? 'text-emerald-600' : 'text-slate-500' }}">
                                        {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs italic">Belum ada mutasi Kas Kecil.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- DANA BANK BANK LEDGER -->
            <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <span class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-base">Rekening Dana Bank</h3>
                                <p class="text-[10px] text-slate-400 uppercase font-mono mt-0.5">Kode CoA: 1.10.02.01</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-wider">Saldo Bank</span>
                            <span class="text-lg font-black text-indigo-600">Rp {{ number_format($globalBankBalance, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Bank ledger events -->
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                        @forelse($bankLedgerEntries as $entry)
                            <div class="flex justify-between items-center p-3 hover:bg-slate-50 rounded-xl transition-all border border-slate-100/30">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block truncate max-w-[200px]">{{ $entry->transaction->description }}</span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $entry->transaction->date->format('d M Y') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-extrabold block {{ $entry->debit > 0 ? 'text-[#1e3a8a]' : 'text-slate-500' }}">
                                        {{ $entry->debit > 0 ? '+' : '-' }} Rp {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs italic">Belum ada mutasi Rekening Bank.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId, element) {
        // Hide all tab contents
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });

        // Show target tab content
        const targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.remove('hidden');
            targetContent.classList.add('block');
        }

        // Remove active classes from all buttons
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('active', 'border-[#1e3a8a]', 'text-[#1e3a8a]');
            btn.classList.add('text-slate-500', 'border-transparent');
        });

        // Add active classes to selected button
        element.classList.add('active', 'border-[#1e3a8a]', 'text-[#1e3a8a]');
        element.classList.remove('text-slate-500', 'border-transparent');

        // Store active tab in localStorage
        localStorage.setItem('activeAccTab', tabId);
    }

    // Restore active tab on page reload
    document.addEventListener('DOMContentLoaded', () => {
        // Query param checks
        const urlParams = new URLSearchParams(window.location.search);
        let tabId = urlParams.get('active_tab') || localStorage.getItem('activeAccTab') || 'tab-gl';
        
        // Find tab button matching target
        const btns = document.querySelectorAll('.tab-btn');
        let targetBtn = btns[0]; // fallback
        
        if (tabId === 'tab-tb') {
            targetBtn = btns[1];
        } else if (tabId === 'tab-lr') {
            targetBtn = btns[2];
        } else if (tabId === 'tab-reconcile') {
            targetBtn = btns[3];
        }
        
        if (targetBtn) {
            targetBtn.click();
        }
    });
</script>
@endsection
