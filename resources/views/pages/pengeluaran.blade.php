@extends('layouts.app')

@section('title', 'Pengeluaran Operasional - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen & Riwayat Pengeluaran</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar historis pengeluaran kas bimbel dan pencatatan nota operasional.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('export.slip.gaji.blank') }}" class="bg-slate-600 hover:bg-slate-700 text-white font-bold px-4 py-3 rounded-xl text-sm transition-all shadow-md flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Slip Gaji Kosong (PDF)
        </a>
        <button id="add-pengeluaran-btn" onclick="toggleModal('pengeluaranModal', true)" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-3 rounded-xl text-sm transition-all shadow-md shadow-rose-600/10 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Catat Pengeluaran Baru
        </button>
    </div>
</div>

<!-- Flash Message Notifications -->
@if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
        <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif

<!-- Search & Filters Panel -->
<div id="pengeluaran-filter-form" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan pengeluaran..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs focus:outline-[#1e3a8a] bg-slate-50/50">
        </div>
        <div>
            <select name="coa_expense_id" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Kategori Biaya --</option>
                @foreach($expenseAccountsList as $exp)
                    <option value="{{ $exp->id }}" {{ request('coa_expense_id') == $exp->id ? 'selected' : '' }}>{{ $exp->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'coa_expense_id']))
                <a href="{{ route('pengeluaran.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Data Table -->
<div id="pengeluaran-table-card" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                <tr>
                    <th class="p-4 text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Kategori Biaya</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Keterangan Pengeluaran</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-right">Jumlah (Rp)</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Sumber Dana</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Status</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pengeluaranRecords as $tx)
                    @php
                        $debitEntry = $tx->journalEntries->where('debit', '>', 0)->first();
                        $creditEntry = $tx->journalEntries->where('credit', '>', 0)->first();
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-medium text-slate-600">
                            {{ $tx->date->format('d/m/Y') }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                {{ $debitEntry ? $debitEntry->account->account_name : 'Beban' }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-900 font-bold max-w-[250px] truncate">
                            {{ $tx->description }}
                        </td>
                        <td class="p-4 text-right font-black text-rose-600">
                            Rp {{ number_format($debitEntry ? (float)$debitEntry->debit : 0, 2, ',', '.') }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                {{ $creditEntry && $creditEntry->account_id === '1.10.02.01' ? 'Transfer Bank' : 'Kas Kecil' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-750 border border-rose-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                Pengeluaran Lunas
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if($debitEntry && $debitEntry->account_id === '5.16.01.01')
                                <a href="{{ route('export.slip.gaji', $tx->id) }}" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-3 py-1.5 rounded-lg text-xs transition-all inline-flex items-center gap-1 justify-center">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Cetak Slip
                                </a>
                            @else
                                <span class="text-slate-400 font-light">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 italic">
                            Belum ada riwayat pengeluaran operasional yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="mt-6">
    {{ $pengeluaranRecords->links() }}
</div>

<!-- ============================================== -->
<!-- CATAT PENGELUARAN MODAL DIALOG -->
<!-- ============================================== -->
<div id="pengeluaranModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop Overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="toggleModal('pengeluaranModal', false)"></div>

        <!-- Center modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel Container -->
        <div class="relative z-10 inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-rose-700 to-rose-550 px-6 py-5 text-white flex items-center justify-between" style="background-color: #be123c;">
                <div>
                    <h3 class="text-lg font-bold tracking-tight" id="modal-title">Catat Pengeluaran Bimbel</h3>
                    <p class="text-xs text-rose-100 opacity-90 mt-0.5">Catat pengeluaran operasional dan biaya harian Fikra Academy.</p>
                </div>
                <button onclick="toggleModal('pengeluaranModal', false)" class="p-1 rounded-lg hover:bg-white/10 text-white transition-colors focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('pengeluaran.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Transaksi</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-100 focus:border-rose-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori Pengeluaran</label>
                        <select name="coa_expense_id" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-100 focus:border-rose-600 focus:outline-none bg-white">
                            @foreach($expenseAccountsList as $exp)
                                <option value="{{ $exp->id }}">{{ $exp->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sumber Dana</label>
                        <select name="payment_method" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-100 focus:border-rose-600 focus:outline-none bg-white">
                            <option value="Cash">Cash Kecil H.O</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jumlah (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" name="amount" placeholder="200000" min="1" required class="w-full rounded-xl border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-100 focus:border-rose-600 focus:outline-none font-bold">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi / Keterangan Pembelian</label>
                    <textarea name="description" placeholder="Contoh: Pembelian tinta printer Epson H.O dan ATK kelas reguler" required rows="3" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-100 focus:border-rose-600 focus:outline-none"></textarea>
                </div>

                <!-- Drag & Drop Nota -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Foto Nota / Faktur</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-rose-450 rounded-2xl p-5 text-center cursor-pointer bg-slate-50/50 hover:bg-rose-50/5 transition-all group">
                        <input type="file" name="receipt_photo" class="hidden" id="receiptPhoto">
                        <label for="receiptPhoto" class="cursor-pointer">
                            <svg class="h-7 w-7 text-slate-400 mx-auto group-hover:text-rose-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-xs font-bold text-slate-650 block mt-2">Ambil Foto Nota Belanja</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" onclick="toggleModal('pengeluaranModal', false)" class="bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold px-5 py-2.5 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleModal(modalId, show) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Auto-open modal if requested via query parameter OR if there are validation errors
        if (urlParams.get('open_modal') === 'true' || {{ $errors->any() ? 'true' : 'false' }}) {
            toggleModal('pengeluaranModal', true);
        }
    });
</script>
@endsection
