@extends('layouts.app')

@section('title', 'Dashboard Utama - Fikra Academy')

@section('content')
<!-- Welcome Banner -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Selamat Datang di Fikra Academy</h2>
        <p class="text-sm text-slate-500 mt-1">Ringkasan aktivitas keuangan dan indikator operasional sekolah bimbel hari ini.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('pemasukan.index', ['open_modal' => 'true']) }}" class="bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all shadow shadow-blue-900/10 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Input Pemasukan
        </a>
        <a href="{{ route('pengeluaran.index', ['open_modal' => 'true']) }}" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all shadow shadow-rose-600/10 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
            Catat Pengeluaran
        </a>
    </div>
</div>

<!-- Metrics Summary Cards -->
<section id="metric-cards-section" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- 1. Total Income -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group">
        <div class="absolute top-0 right-0 h-24 w-24 bg-emerald-500/5 rounded-full translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform"></div>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Total Pendapatan</span>
            <h3 class="text-2xl font-black text-slate-900 leading-none">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </h3>
            <span class="inline-flex items-center text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-2 border border-emerald-100/50">
                Inflow SPP
            </span>
        </div>
        <span class="p-3.5 rounded-xl bg-emerald-50 text-emerald-600 shadow-sm border border-emerald-100">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </span>
    </div>

    <!-- 2. Total Expenses -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group">
        <div class="absolute top-0 right-0 h-24 w-24 bg-rose-500/5 rounded-full translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform"></div>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Total Pengeluaran</span>
            <h3 class="text-2xl font-black text-slate-900 leading-none">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </h3>
            <span class="inline-flex items-center text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded mt-2 border border-rose-100/50">
                Beban Usaha
            </span>
        </div>
        <span class="p-3.5 rounded-xl bg-rose-50 text-rose-600 shadow-sm border border-rose-100">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
        </span>
    </div>

    <!-- 3. Net Profit -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group">
        <div class="absolute top-0 right-0 h-24 w-24 rounded-full translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform {{ $netProfitOrLoss >= 0 ? 'bg-blue-500/5' : 'bg-rose-500/5' }}"></div>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Laba Bersih</span>
            <h3 class="text-2xl font-black leading-none {{ $netProfitOrLoss >= 0 ? 'text-blue-900' : 'text-rose-600' }}">
                Rp {{ number_format($netProfitOrLoss, 0, ',', '.') }}
            </h3>
            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded mt-2 border {{ $netProfitOrLoss >= 0 ? 'text-blue-600 bg-blue-50 border-blue-100/50' : 'text-rose-600 bg-rose-50 border-rose-100/50' }}">
                {{ $netProfitOrLoss >= 0 ? 'Surplus' : 'Defisit' }}
            </span>
        </div>
        <span class="p-3.5 rounded-xl {{ $netProfitOrLoss >= 0 ? 'bg-blue-50 text-blue-900 border border-blue-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }} shadow-sm">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </span>
    </div>

    <!-- 4. Active Student Count -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group">
        <div class="absolute top-0 right-0 h-24 w-24 bg-indigo-500/5 rounded-full translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform"></div>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Siswa Aktif</span>
            <h3 class="text-2xl font-black text-slate-900 leading-none">
                {{ $activeStudents }} Siswa
            </h3>
            <span class="inline-flex items-center text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded mt-2 border border-indigo-100/50">
                Terdaftar
            </span>
        </div>
        <span class="p-3.5 rounded-xl bg-indigo-50 text-indigo-600 shadow-sm border border-indigo-100">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </span>
    </div>
</section>

<!-- Trends & Recent Transactions Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Trend Chart Container (2 columns span) -->
    <div id="cashflow-chart-card" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-extrabold text-slate-800 text-base">Tren Arus Kas Masuk vs Keluar</h3>
                <p class="text-xs text-slate-500 mt-0.5">Komparasi bulanan penerimaan SPP vs beban biaya bimbel.</p>
            </div>
            <span class="text-xs font-bold text-[#1e3a8a] bg-blue-50 border border-blue-100 px-3 py-1 rounded-lg">8 Bulan Terakhir</span>
        </div>
        
        <div class="relative w-full" style="height: 300px;">
            <canvas id="cashflowTrendChart"></canvas>
        </div>
    </div>

    <!-- Recent Activities list (1 column span) -->
    <div id="recent-tx-card" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
        <div class="pb-4 border-b border-slate-100 mb-4">
            <h3 class="font-extrabold text-slate-800 text-base">Aktivitas Jurnal Terbaru</h3>
            <p class="text-xs text-slate-500 mt-0.5">5 catatan transaksi terakhir yang masuk GL.</p>
        </div>

        <div class="flex-1 space-y-4 overflow-y-auto pr-1">
            @forelse($recentTransactions as $tx)
                <div class="flex items-start justify-between gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-all border border-transparent hover:border-slate-100">
                    <div class="flex items-start gap-3">
                        <span class="p-2 rounded-lg mt-0.5 shrink-0 {{ $tx->reference_type === 'pengeluaran' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                            @if($tx->reference_type === 'pengeluaran')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-6l-7 7-7-7"></path>
                                </svg>
                            @else
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                            @endif
                        </span>
                        <div>
                            <span class="text-xs font-black text-slate-900 block truncate max-w-[150px]">
                                {{ $tx->description }}
                            </span>
                            <span class="text-[10px] text-slate-500 block mt-0.5">{{ $tx->date->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black block {{ $tx->reference_type === 'pengeluaran' ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $tx->reference_type === 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($tx->journalEntries->where('debit', '>', 0)->sum('debit'), 0, ',', '.') }}
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 block mt-0.5">TX-{{ str_pad($tx->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs italic">
                    Belum ada transaksi.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('cashflowTrendChart').getContext('2d');
        
        // Dynamic values injected from backend
        const labels = {!! json_encode($chartLabels) !!};
        const inflowData = {!! json_encode($chartInflows) !!};
        const outflowData = {!! json_encode($chartOutflows) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Inflow (Pemasukan)',
                        data: inflowData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Outflow (Pengeluaran)',
                        data: outflowData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.02)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Outfit',
                                size: 12,
                                weight: '500'
                            },
                            color: '#475569',
                            boxWidth: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: {
                            family: 'Outfit',
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Outfit',
                            size: 12
                        },
                        padding: 12,
                        borderRadius: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                size: 11
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                family: 'Outfit',
                                size: 11
                            },
                            color: '#64748b',
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
