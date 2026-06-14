@extends('layouts.app')

@section('title', 'Pemasukan SPP Siswa - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen & Riwayat Pemasukan SPP</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar historis setoran SPP siswa reguler & intensif Fikra Academy.</p>
    </div>
    <div>
        <button id="add-pemasukan-btn" onclick="toggleModal('pemasukanModal', true)" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-5 py-3 rounded-xl text-sm transition-all shadow-md shadow-blue-900/10 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pemasukan SPP
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
<div id="pemasukan-filter-form" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <form action="{{ route('pemasukan.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs focus:outline-[#1e3a8a] bg-slate-50/50">
        </div>
        <div>
            <select name="period_month" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Bulan SPP --</option>
                @foreach($availableMonths as $month)
                    <option value="{{ $month }}" {{ request('period_month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="type_spp" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Program --</option>
                <option value="Reguler" {{ request('type_spp') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                <option value="Intensif" {{ request('type_spp') == 'Intensif' ? 'selected' : '' }}>Intensif</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'period_month', 'type_spp']))
                <a href="{{ route('pemasukan.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Data Table -->
<div id="pemasukan-table-card" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                <tr>
                    <th class="p-4 text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Nama Siswa</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Program</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Periode SPP</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-right">Jumlah (Rp)</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Metode</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pemasukanRecords as $tx)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-medium text-slate-600">
                            {{ $tx->date->format('d/m/Y') }}
                        </td>
                        <td class="p-4 font-bold text-slate-900">
                            {{ $tx->student_name }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $tx->reference_type === 'spp_intensif' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $tx->reference_type === 'spp_intensif' ? 'Intensif' : 'Reguler' }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-700 font-medium">
                            {{ $tx->period_month }}
                        </td>
                        <td class="p-4 text-right font-black text-slate-950">
                            Rp {{ number_format($tx->journalEntries->where('debit', '>', 0)->sum('debit'), 2, ',', '.') }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                {{ $tx->journalEntries->where('debit', '>', 0)->first()->account_id === '1.10.02.01' ? 'Transfer Bank' : 'Kas Kecil' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Lunas / Sukses
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 italic">
                            Belum ada riwayat pemasukan SPP yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="mt-6">
    {{ $pemasukanRecords->links() }}
</div>

<!-- ============================================== -->
<!-- TAMBAH PEMASUKAN MODAL DIALOG -->
<!-- ============================================== -->
<div id="pemasukanModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop Overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="toggleModal('pemasukanModal', false)"></div>

        <!-- Trick browser to center modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel Container -->
        <div class="relative z-10 inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold tracking-tight" id="modal-title">Catat Pemasukan SPP</h3>
                    <p class="text-xs text-blue-100 opacity-90 mt-0.5">Input data pembayaran SPP bulanan siswa Fikra Academy.</p>
                </div>
                <button onclick="toggleModal('pemasukanModal', false)" class="p-1 rounded-lg hover:bg-white/10 text-white transition-colors focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('pemasukan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Bayar</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bulan Tagihan</label>
                        <select name="period_month" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            @foreach($availableMonths as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Siswa Aktif</label>
                    <select name="student_name" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($activeStudentsList as $std)
                            <option value="{{ $std->name }}">{{ $std->name }} (Kelas {{ $std->program }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Radio Program Options -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Program Pembelajaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type_spp" value="Reguler" checked class="h-4 w-4 text-[#1e3a8a] focus:ring-0">
                            <div>
                                <span class="text-sm font-bold block text-slate-800">Kelas Reguler</span>
                                <span class="text-[10px] text-slate-400">Program SPP Reguler</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="type_spp" value="Intensif" class="h-4 w-4 text-[#1e3a8a] focus:ring-0">
                            <div>
                                <span class="text-sm font-bold block text-slate-800">Kelas Intensif</span>
                                <span class="text-[10px] text-slate-400">Program SPP Intensif</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Metode Bayar</label>
                        <select name="payment_method" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            <option value="Transfer">Transfer Bank</option>
                            <option value="Cash">Cash Kecil H.O</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jumlah Bayar (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" name="amount" placeholder="600000" min="1" required class="w-full rounded-xl border border-slate-200 pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none font-bold">
                        </div>
                    </div>
                </div>

                <!-- Drag & Drop Uploader -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nota / Bukti Transfer Digital</label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-6 text-center cursor-pointer bg-slate-50/50 hover:bg-blue-50/10 transition-all group">
                        <input type="file" name="receipt" class="hidden" id="receiptFile" onchange="updateFileLabel(this)">
                        <label for="receiptFile" class="cursor-pointer">
                            <svg class="h-8 w-8 text-slate-400 mx-auto group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            <span class="text-xs font-bold text-slate-600 block mt-2" id="fileLabel">Pilih file atau seret bukti bayar ke sini</span>
                            <span class="text-[10px] text-slate-400 block mt-1">PDF, JPG, PNG up to 2MB</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" onclick="toggleModal('pemasukanModal', false)" class="bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold px-5 py-2.5 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">
                        Simpan Pembayaran
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

    function updateFileLabel(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files[0]) {
            label.textContent = `File terpilih: ${input.files[0].name}`;
            label.classList.add('text-[#1e3a8a]');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Auto-open modal if requested via query parameter OR if there are validation errors
        if (urlParams.get('open_modal') === 'true' || {{ $errors->any() ? 'true' : 'false' }}) {
            toggleModal('pemasukanModal', true);
        }
    });
</script>
@endsection
