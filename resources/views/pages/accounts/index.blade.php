@extends('layouts.app')

@section('title', 'Chart of Accounts (CoA) - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Chart of Accounts (CoA) Master</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar kode rekening akuntansi untuk klasifikasi transaksi ledger.</p>
    </div>
    <div>
        <button id="add-account-btn" onclick="openCreateModal()" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-5 py-3 rounded-xl text-sm transition-all shadow-md shadow-blue-900/10 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Akun Master
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

@if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
        <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<!-- Search & Filters Panel -->
<div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <form action="{{ route('accounts.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama akun..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs bg-slate-50/50 focus:outline-[#1e3a8a]">
        </div>
        <div>
            <select name="type" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Tipe Akun --</option>
                <option value="asset" {{ request('type') == 'asset' ? 'selected' : '' }}>Asset</option>
                <option value="liability" {{ request('type') == 'liability' ? 'selected' : '' }}>Liability</option>
                <option value="equity" {{ request('type') == 'equity' ? 'selected' : '' }}>Equity</option>
                <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'type']))
                <a href="{{ route('accounts.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Data Table -->
<div id="account-table-card" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                <tr>
                    <th class="p-4 text-xs uppercase tracking-wider">Kode Akun</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Nama Akun</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Tipe Akun</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Normal Balance</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($accounts as $account)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-mono font-bold text-slate-900">
                            {{ $account->id }}
                        </td>
                        <td class="p-4 font-bold text-slate-800">
                            {{ $account->account_name }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $account->type === 'expense' || $account->type === 'revenue' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800' }}">
                                {{ ucfirst($account->type) }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-slate-700 font-semibold uppercase">
                            {{ $account->normal_balance }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($account) }})" class="p-1.5 rounded-lg bg-indigo-50 text-[#1e3a8a] hover:bg-indigo-100 transition-colors" title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                
                                @if(!in_array($account->id, ['1.10.01.01', '1.10.02.01']))
                                    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Tidak ada akun CoA yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="mt-6">
    {{ $accounts->links() }}
</div>

<!-- ============================================== -->
<!-- MODAL: TAMBAH/EDIT AKUN MASTERS -->
<!-- ============================================== -->
<div id="accountModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="relative z-10 inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <!-- Modal Header -->
            <div id="modalHeader" class="bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold tracking-tight" id="modalTitle">Tambah Akun CoA Baru</h3>
                    <p class="text-xs text-blue-100 opacity-90 mt-0.5" id="modalSub">Input kode akun klasifikasi keuangan baru.</p>
                </div>
                <button onclick="closeModal()" class="p-1 rounded-lg hover:bg-white/10 text-white transition-colors focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="accountForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                <div id="methodContainer"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kode Rekening / ID Akun</label>
                    <input type="text" name="id" id="account_id" placeholder="Contoh: 1.10.03.01" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none font-mono">
                    <span class="text-[10px] text-slate-400 block mt-1" id="idHint">Gunakan format angka dan titik. Contoh: 5.16.01.08</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Akun Pembukuan</label>
                    <input type="text" name="account_name" id="account_name" placeholder="Contoh: Beban Operasional Lainnya" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Klasifikasi Tipe</label>
                        <select name="type" id="account_type" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Saldo Normal</label>
                        <select name="normal_balance" id="account_normal_balance" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            <option value="debit">Debit</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" onclick="closeModal()" class="bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold px-5 py-2.5 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modal = document.getElementById('accountModal');
    const form = document.getElementById('accountForm');
    const methodContainer = document.getElementById('methodContainer');
    
    const modalTitle = document.getElementById('modalTitle');
    const modalSub = document.getElementById('modalSub');

    const inputId = document.getElementById('account_id');
    const inputName = document.getElementById('account_name');
    const inputType = document.getElementById('account_type');
    const inputBalance = document.getElementById('account_normal_balance');
    const idHint = document.getElementById('idHint');

    function openCreateModal() {
        modalTitle.textContent = 'Tambah Akun CoA Baru';
        modalSub.textContent = 'Input kode akun klasifikasi keuangan baru.';
        form.action = "{{ route('accounts.store') }}";
        methodContainer.innerHTML = '';
        
        // Clear & enable id
        inputId.value = '';
        inputId.disabled = false;
        inputId.readOnly = false;
        idHint.style.display = 'block';
        
        inputName.value = '';
        inputType.value = 'asset';
        inputBalance.value = 'debit';
        
        modal.classList.remove('hidden');
    }

    function openEditModal(account) {
        modalTitle.textContent = 'Edit Akun CoA';
        modalSub.textContent = 'Perbarui klasifikasi tipe atau nama akun pembukuan.';
        form.action = `/accounts/${account.id}`;
        
        methodContainer.innerHTML = '@method("PUT")';
        
        // Populate and disable id editing
        inputId.value = account.id;
        inputId.disabled = true; // prevent editing primary key in form submit
        inputId.readOnly = true;
        idHint.style.display = 'none';
        
        inputName.value = account.account_name;
        inputType.value = account.type;
        inputBalance.value = account.normal_balance;
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endsection
