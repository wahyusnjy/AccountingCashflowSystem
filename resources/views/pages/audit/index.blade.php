@extends('layouts.app')

@section('title', 'Audit & Log Aktivitas - Fikra Academy')

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Audit & Log Aktivitas Sistem</h2>
    <p class="text-sm text-slate-500 mt-1">Lacak sesi login pengguna, deteksi perangkat, dan histori tindakan administratif secara detail.</p>
</div>

<!-- Search & Filters Panel -->
<div id="audit-filter-form" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
    <form action="{{ route('accounting.index') /* or specific route */ }}" method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Pengguna</label>
            <select name="user_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
                <option value="">-- Semua Pengguna --</option>
                @foreach($usersList as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs bg-white focus:outline-[#1e3a8a]">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-slate-700 hover:bg-slate-800 text-white font-bold py-2 rounded-xl text-xs transition-all shadow-sm">
                Filter
            </button>
            @if(request()->anyFilled(['user_id', 'start_date', 'end_date']))
                <a href="{{ url()->current() }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabs Control -->
<div class="mb-6 border-b border-slate-200">
    <nav id="audit-tabs" class="flex gap-6" aria-label="Tabs">
        <button onclick="switchTab('activity-tab', 'session-tab', this)" class="border-[#1e3a8a] text-[#1e3a8a] whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center gap-2 tab-btn-active">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Riwayat Aktivitas & Tindakan
        </button>
        <button onclick="switchTab('session-tab', 'activity-tab', this)" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Sesi Login & Perangkat
        </button>
    </nav>
</div>

<div id="audit-tables-card">
<!-- TAB CONTENT 1: RIWAYAT AKTIVITAS -->
<div id="activity-tab-content" class="tab-pane">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                    <tr>
                        <th class="p-4 text-xs uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Pengguna (Role)</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Aktivitas Tindakan</th>
                        <th class="p-4 text-xs uppercase tracking-wider">IP Address</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Device Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activityLogs as $activity)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $activity->user ? $activity->user->name : 'Sistem/Guest' }}</div>
                                <div class="text-[10px] text-slate-400 capitalize">{{ $activity->user ? $activity->user->role : 'Guest' }}</div>
                            </td>
                            <td class="p-4 font-semibold text-slate-800">
                                {{ $activity->activity }}
                            </td>
                            <td class="p-4 text-slate-500 font-mono text-xs whitespace-nowrap">
                                {{ $activity->ip_address }}
                            </td>
                            <td class="p-4 text-xs text-slate-400 max-w-[200px] truncate" title="{{ $activity->user_agent }}">
                                {{ $activity->user_agent }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                Tidak ada log aktivitas yang tercatat untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div>
        {{ $activityLogs->links() }}
    </div>
</div>

<!-- TAB CONTENT 2: SESI LOGIN & PERANGKAT -->
<div id="session-tab-content" class="tab-pane hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold">
                    <tr>
                        <th class="p-4 text-xs uppercase tracking-wider">Pengguna</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Login At</th>
                        <th class="p-4 text-xs uppercase tracking-wider">Logout At</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-center">Perangkat</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-center">Platform/OS</th>
                        <th class="p-4 text-xs uppercase tracking-wider text-center">Peramban</th>
                        <th class="p-4 text-xs uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($authLogs as $sessionLog)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $sessionLog->user->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $sessionLog->user->email }}</div>
                            </td>
                            <td class="p-4 text-slate-650 font-mono text-xs whitespace-nowrap">
                                {{ $sessionLog->login_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-4 font-mono text-xs whitespace-nowrap">
                                @if($sessionLog->logout_at)
                                    <span class="text-slate-500">{{ $sessionLog->logout_at->format('d/m/Y H:i:s') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-150">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Aktif/Online
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($sessionLog->device === 'Mobile')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">📱 Mobile</span>
                                @elseif($sessionLog->device === 'Tablet')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">📟 Tablet</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">💻 Desktop</span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap font-bold text-slate-700">
                                {{ $sessionLog->platform }}
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $sessionLog->browser }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                {{ $sessionLog->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 italic">
                                Belum ada riwayat sesi login yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div>
        {{ $authLogs->links() }}
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(activeId, inactiveId, btnElement) {
        // Hide inactive content, show active content
        document.getElementById(activeId + '-content').classList.remove('hidden');
        document.getElementById(inactiveId + '-content').classList.add('hidden');

        // Style the active button
        btnElement.classList.add('border-[#1e3a8a]', 'text-[#1e3a8a]', 'tab-btn-active');
        btnElement.classList.remove('border-transparent', 'text-slate-500');

        // Style the inactive button
        const parentNode = btnElement.parentNode;
        const buttons = parentNode.getElementsByTagName('button');
        for (let btn of buttons) {
            if (btn !== btnElement) {
                btn.classList.remove('border-[#1e3a8a]', 'text-[#1e3a8a]', 'tab-btn-active');
                btn.classList.add('border-transparent', 'text-slate-500');
            }
        }
        
        // Preserve tab in URL parameter if desired
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('tab', activeId.replace('-tab', ''));
        window.history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Auto switch tab if tab parameter is active
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab === 'session') {
            const btns = document.querySelectorAll('nav[aria-label="Tabs"] button');
            if (btns && btns[1]) {
                btns[1].click();
            }
        }
    });
</script>
@endsection
