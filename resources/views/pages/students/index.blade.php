@extends('layouts.app')

@section('title', 'Manajemen Siswa - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftar & Manajemen Siswa Aktif</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola data siswa terdaftar dan program kelas Fikra Academy.</p>
    </div>
    <div>
        <button id="add-student-btn" onclick="openCreateModal()" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-5 py-3 rounded-xl text-sm transition-all shadow-md shadow-blue-900/10 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Siswa Baru
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
    <form action="{{ route('students.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs bg-slate-50/50 focus:outline-[#1e3a8a]">
        </div>
        <div>
            <select name="program" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Program --</option>
                <option value="Reguler" {{ request('program') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                <option value="Intensif" {{ request('program') == 'Intensif' ? 'selected' : '' }}>Intensif</option>
            </select>
        </div>
        <div>
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="non_aktif" {{ request('status') == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'program', 'status']))
                <a href="{{ route('students.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Data Table -->
<div id="student-table-card" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                <tr>
                    <th class="p-4 text-xs uppercase tracking-wider">Nama Siswa</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Nomor HP</th>
                    <th class="p-4 text-xs uppercase tracking-wider">Program Kelas</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Status</th>
                    <th class="p-4 text-xs uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($students as $student)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4 font-bold text-slate-900">
                            {{ $student->name }}
                        </td>
                        <td class="p-4 text-slate-600 font-mono text-xs">
                            {{ $student->phone ?? '-' }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $student->program === 'Intensif' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $student->program }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if($student->status === 'aktif')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($student) }})" class="p-1.5 rounded-lg bg-indigo-50 text-[#1e3a8a] hover:bg-indigo-100 transition-colors" title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Belum ada data siswa yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Links -->
<div class="mt-6">
    {{ $students->links() }}
</div>

<!-- ============================================== -->
<!-- MODAL: TAMBAH/EDIT SISWA -->
<!-- ============================================== -->
<div id="studentModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="relative z-10 inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
            <!-- Modal Header -->
            <div id="modalHeader" class="bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] px-6 py-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold tracking-tight" id="modalTitle">Tambah Siswa Baru</h3>
                    <p class="text-xs text-blue-100 opacity-90 mt-0.5" id="modalSub">Input profil siswa terdaftar di Fikra Academy.</p>
                </div>
                <button onclick="closeModal()" class="p-1 rounded-lg hover:bg-white/10 text-white transition-colors focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="studentForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                <div id="methodContainer"></div> <!-- For PUT method on updates -->

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap Siswa</label>
                    <input type="text" name="name" id="student_name" placeholder="Contoh: Ahmad Hidayat" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Program Kelas</label>
                        <select name="program" id="student_program" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            <option value="Reguler">Reguler</option>
                            <option value="Intensif">Intensif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Aktif</label>
                        <select name="status" id="student_status" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="non_aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" id="student_phone" placeholder="Contoh: 0812XXXXXXXX" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-[#1e3a8a] focus:outline-none font-mono">
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" onclick="closeModal()" class="bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold px-5 py-2.5 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-6 py-2.5 rounded-xl text-xs transition-all shadow-md">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modal = document.getElementById('studentModal');
    const form = document.getElementById('studentForm');
    const methodContainer = document.getElementById('methodContainer');
    
    const modalTitle = document.getElementById('modalTitle');
    const modalSub = document.getElementById('modalSub');

    const inputName = document.getElementById('student_name');
    const inputProgram = document.getElementById('student_program');
    const inputStatus = document.getElementById('student_status');
    const inputPhone = document.getElementById('student_phone');

    function openCreateModal() {
        modalTitle.textContent = 'Tambah Siswa Baru';
        modalSub.textContent = 'Input profil siswa terdaftar di Fikra Academy.';
        form.action = "{{ route('students.store') }}";
        methodContainer.innerHTML = ''; // no method spoofing needed for POST
        
        // Clear fields
        inputName.value = '';
        inputProgram.value = 'Reguler';
        inputStatus.value = 'aktif';
        inputPhone.value = '';
        
        modal.classList.remove('hidden');
    }

    function openEditModal(student) {
        modalTitle.textContent = 'Edit Data Siswa';
        modalSub.textContent = 'Perbarui detail program kelas atau profil siswa.';
        form.action = `/students/${student.id}`;
        
        // Spoof PUT method for update action
        methodContainer.innerHTML = '@method("PUT")';
        
        // Populate fields
        inputName.value = student.name;
        inputProgram.value = student.program;
        inputStatus.value = student.status;
        inputPhone.value = student.phone || '';
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endsection
