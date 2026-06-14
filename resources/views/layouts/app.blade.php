<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fikra Academy Accounting')</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
        .text-primary {
            color: #1e3a8a;
        }
        .bg-primary {
            background-color: #1e3a8a;
        }
        .bg-primary-light {
            background-color: #f0f4ff;
        }
        .border-primary {
            border-color: #1e3a8a;
        }
        .sidebar-item-active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #10b981;
            color: #ffffff;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen text-slate-800 antialiased flex bg-slate-50 relative overflow-x-hidden">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300" onclick="toggleMobileSidebar(false)"></div>

    <!-- Sidebar Navigation -->
    <aside id="appSidebar" class="w-64 bg-[#1e3a8a] text-indigo-100 flex flex-col shrink-0 border-r border-indigo-900/30 fixed md:static inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <!-- Brand Header -->
        <div class="p-6 border-b border-indigo-900/30 bg-[#172e6b] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-emerald-500 rounded-xl text-white font-black text-lg shadow-md shadow-emerald-500/20">FA</span>
                <div>
                    <h2 class="font-extrabold text-white text-base tracking-tight leading-none">Fikra Academy</h2>
                    <span class="text-[10px] text-indigo-300 font-medium">Accounting System</span>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button onclick="toggleMobileSidebar(false)" class="md:hidden p-1.5 rounded-lg text-indigo-350 hover:bg-white/10 hover:text-white focus:outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Items -->
        <nav class="flex-1 px-4 py-6 space-y-7 overflow-y-auto">
            <!-- Dashboard Main Link -->
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('dashboard') ? 'sidebar-item-active' : '' }}">
                    <span>🏠</span> Dashboard Utama
                </a>
            </div>

            <!-- Section 1: Menu Bendahara -->
            <div>
                <p class="px-3 text-[10px] font-bold text-indigo-300/80 uppercase tracking-widest mb-3">Menu Bendahara (Input Simple)</p>
                <div class="space-y-1">
                    <a href="{{ route('pemasukan.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('pemasukan.index') ? 'sidebar-item-active' : '' }}">
                        <span>📥</span> Form Pemasukan SPP
                    </a>
                    <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('pengeluaran.index') ? 'sidebar-item-active' : '' }}">
                        <span>📤</span> Form Pengeluaran
                    </a>
                </div>
            </div>

            <!-- Section 2: Menu Akuntansi -->
            <div>
                <p class="px-3 text-[10px] font-bold text-indigo-300/80 uppercase tracking-widest mb-3">Menu Akuntansi (Laporan Otomatis)</p>
                <div class="space-y-1">
                    <a href="{{ route('accounting.index', ['active_tab' => 'tab-gl']) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('accounting.index') && request('active_tab') === 'tab-gl' ? 'sidebar-item-active' : '' }}">
                        <span>📑</span> Jurnal Umum / GL
                    </a>
                    <a href="{{ route('accounting.index', ['active_tab' => 'tab-tb']) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('accounting.index') && request('active_tab') === 'tab-tb' ? 'sidebar-item-active' : '' }}">
                        <span>⚖️</span> Neraca Saldo / TB
                    </a>
                    <a href="{{ route('accounting.index', ['active_tab' => 'tab-lr']) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('accounting.index') && request('active_tab') === 'tab-lr' ? 'sidebar-item-active' : '' }}">
                        <span>📈</span> Laba Rugi / LR
                    </a>
                    <a href="{{ route('accounting.index', ['active_tab' => 'tab-reconcile']) }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('accounting.index') && request('active_tab') === 'tab-reconcile' ? 'sidebar-item-active' : '' }}">
                        <span>💸</span> Arus Kas / LAK
                    </a>
                </div>
            </div>

            <!-- Section 3: Data Master -->
            <div>
                <p class="px-3 text-[10px] font-bold text-indigo-300/80 uppercase tracking-widest mb-3">⚙️ Data Master (Pengaturan)</p>
                <div class="space-y-1">
                    <a href="{{ route('students.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('students.*') ? 'sidebar-item-active' : '' }}">
                        <span>👥</span> Manajemen Siswa
                    </a>
                    <a href="{{ route('accounts.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('accounts.*') ? 'sidebar-item-active' : '' }}">
                        <span>🗂️</span> Chart of Accounts (CoA)
                    </a>
                </div>
            </div>

            <!-- Section 4: System Logs & Audit -->
            <div>
                <p class="px-3 text-[10px] font-bold text-indigo-300/80 uppercase tracking-widest mb-3">🪵 Audit Sistem</p>
                <div class="space-y-1">
                    <a href="{{ route('audit.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-white/10 hover:text-white transition-all {{ request()->routeIs('audit.*') ? 'sidebar-item-active' : '' }}">
                        <span>🔍</span> Log Aktivitas & Sesi
                    </a>
                </div>
            </div>

            <!-- Logout Link -->
            <div class="pt-4 border-t border-indigo-900/30">
                <form action="{{ route('logout') }}" method="POST" id="logoutFormSidebar">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-rose-600 hover:text-white text-indigo-200 transition-all cursor-pointer">
                        <span>🚪</span> Keluar Sistem
                    </button>
                </form>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-indigo-900/30 text-xs text-indigo-300 bg-[#172e6b] text-center">
            &copy; 2026 Fikra Academy
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-6 shadow-sm shrink-0">
            <!-- Mobile Menu Toggle -->
            <div class="flex items-center gap-4">
                <button onclick="toggleMobileSidebar(true)" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Period Indicator -->
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        Periode: 2025-2026
                    </span>
                </div>
            </div>

            <!-- Balances & Profile -->
            <div class="flex items-center gap-6">
                <div id="header-balances" class="hidden lg:flex items-center gap-6">
                    <!-- Cash Balance Indicator -->
                    <div class="flex items-center gap-2 border-r border-slate-200/80 pr-6">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider leading-none">Kas Kecil H.O</span>
                            <span class="text-sm font-extrabold text-slate-800">
                                Rp {{ number_format($globalCashBalance ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </span>
                    </div>

                    <!-- Bank Balance Indicator -->
                    <div class="flex items-center gap-2 border-r border-slate-200/80 pr-6">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block tracking-wider leading-none">Dana di Bank</span>
                            <span class="text-sm font-extrabold text-slate-800">
                                Rp {{ number_format($globalBankBalance ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="text-sm font-bold text-slate-800 block">{{ auth()->check() ? auth()->user()->name : 'Fikra Admin' }}</span>
                        <span class="text-[10px] text-slate-550 block leading-none">{{ auth()->check() ? auth()->user()->email : 'admin@fikra.com' }}</span>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-[#1e3a8a] text-white flex items-center justify-center font-extrabold text-sm shadow">
                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'FA' }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline ml-1">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" title="Keluar Sistem">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Inner Scrollable Container -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <!-- Custom Mobile Balance Banner -->
            <div class="lg:hidden grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Kas Kecil</span>
                        <span class="text-xs font-black text-slate-800">Rp {{ number_format($globalCashBalance ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <span class="p-1 rounded bg-emerald-50 text-emerald-600 text-xs font-bold">Cash</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Bank Dana</span>
                        <span class="text-xs font-black text-slate-800">Rp {{ number_format($globalBankBalance ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <span class="p-1 rounded bg-indigo-50 text-indigo-600 text-xs font-bold">Bank</span>
                </div>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Mobile Drawer Handler Javascript -->
    <script>
        function toggleMobileSidebar(show) {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                if (show) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    backdrop.classList.add('hidden');
                }
            }
        }
    </script>

    <!-- ============================================== -->
    <!-- TOUR GUIDE MODALS & WIDGETS -->
    <!-- ============================================== -->
    <!-- Floating Help Button -->
    <button onclick="openTourPrompt()" class="fixed bottom-6 right-6 z-40 bg-[#1e3a8a] hover:bg-emerald-500 text-white rounded-full h-12 w-12 flex items-center justify-center shadow-lg hover:shadow-emerald-500/20 transition-all hover:scale-110 active:scale-95 cursor-pointer font-black text-lg border border-white/20" title="Bantuan Panduan Halaman">
        ❓
    </button>

    <!-- Tour Start Confirmation Prompt -->
    <div id="tourPromptModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop Overlay with blur -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeTourPrompt()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="relative z-10 inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-slate-100 p-6 space-y-4">
                <div class="text-center">
                    <span class="text-4xl">💡</span>
                    <h3 class="text-lg font-extrabold text-slate-800 mt-3">Mulai Panduan Halaman?</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">Apakah Anda ingin dipandu untuk mempelajari fitur dan elemen pada halaman ini step-by-step?</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="closeTourPrompt()" class="flex-1 bg-slate-100 hover:bg-slate-250 text-slate-650 font-bold py-2.5 rounded-xl text-xs transition-all">
                        Batal
                    </button>
                    <button onclick="startTour()" class="flex-1 bg-[#1e3a8a] hover:opacity-90 text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow-md">
                        Ya, Mulai!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tour Control Tooltip Card -->
    <div id="tourCard" class="fixed bottom-20 right-6 z-50 w-80 bg-slate-900 text-slate-100 border border-slate-800 rounded-2xl shadow-2xl p-5 transform transition-all scale-100 hidden">
        <div class="flex items-start justify-between gap-3">
            <div>
                <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest block" id="tourStepIndicator">Langkah 1 dari 3</span>
                <h4 class="text-sm font-black text-white mt-1 leading-snug" id="tourStepTitle">Judul Langkah</h4>
            </div>
            <button onclick="exitTour()" class="text-slate-400 hover:text-white transition-colors cursor-pointer text-xs font-bold p-1">
                &times;
            </button>
        </div>
        <p class="text-xs text-slate-300 mt-2.5 leading-relaxed" id="tourStepDesc">Penjelasan langkah fitur disini.</p>
        
        <div class="flex items-center justify-between pt-4 border-t border-slate-800 mt-4">
            <button onclick="prevTourStep()" id="tourPrevBtn" class="text-slate-400 hover:text-white font-bold text-xs transition-all disabled:opacity-30 disabled:pointer-events-none cursor-pointer">
                &larr; Sebelumnya
            </button>
            <div class="flex gap-2">
                <button onclick="exitTour()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-3 py-1.5 rounded-lg text-[10px] transition-all cursor-pointer">
                    Keluar
                </button>
                <button onclick="nextTourStep()" id="tourNextBtn" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-lg text-[10px] transition-all shadow shadow-emerald-500/20 cursor-pointer">
                    Berikutnya &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Tour Guide Logic -->
    <script>
        // Define tour steps for each path
        const tourStepsConfig = {
            'dashboard': [
                {
                    title: 'Statistik Ringkasan Utama',
                    selector: '#metric-cards-section',
                    desc: 'Bagian ini menampilkan total Pendapatan, Pengeluaran, dan Laba Bersih yang terhitung secara otomatis dari jurnal ledger double-entry.'
                },
                {
                    title: 'Indikator Saldo Kas & Bank',
                    selector: '#header-balances',
                    desc: 'Indikator ini memantau sisa saldo aktual di dompet Kas Kecil H.O dan Dana di Bank Fikra Academy secara real-time.'
                },
                {
                    title: 'Grafik Tren Arus Kas Bulanan',
                    selector: '#cashflow-chart-card',
                    desc: 'Grafik interaktif ini melacak visualisasi perbandingan Inflow SPP reguler/intensif dengan Outflow biaya harian bimbel.'
                },
                {
                    title: 'Histori Transaksi Terakhir',
                    selector: '#recent-tx-card',
                    desc: 'Daftar 5 transaksi keuangan terbaru yang masuk ke sistem. Anda bisa memantau aliran kas masuk-keluar secara ringkas.'
                }
            ],
            'pemasukan': [
                {
                    title: 'Form Pembayaran SPP Siswa',
                    selector: '#add-pemasukan-btn',
                    desc: 'Klik tombol ini untuk memunculkan modal form input SPP bulanan siswa reguler/intensif yang akan menjurnal ledger otomatis di latar belakang.'
                },
                {
                    title: 'Filter & Pencarian Riwayat',
                    selector: '#pemasukan-filter-form',
                    desc: 'Gunakan panel ini untuk mencari data setoran SPP siswa berdasarkan nama siswa, program kelas, atau periode bulan tagihan.'
                },
                {
                    title: 'Tabel Histori Setoran SPP',
                    selector: '#pemasukan-table-card',
                    desc: 'Tabel rincian riwayat seluruh pembayaran SPP siswa. Menampilkan tanggal, program reguler/intensif, nominal, dan metode bayar.'
                }
            ],
            'pengeluaran': [
                {
                    title: 'Form Catat Biaya Operasional',
                    selector: '#add-pengeluaran-btn',
                    desc: 'Klik tombol ini untuk mencatat transaksi pengeluaran operasional baru (seperti gaji, listrik, internet, konsumsi) lengkap dengan upload nota fisik.'
                },
                {
                    title: 'Penyaringan Kategori Pengeluaran',
                    selector: '#pengeluaran-filter-form',
                    desc: 'Filter daftar pengeluaran berdasarkan kategori biaya tertentu (CoA kepala 5.16) atau deskripsi nota pembelian.'
                },
                {
                    title: 'Tabel Histori Biaya Bimbel',
                    selector: '#pengeluaran-table-card',
                    desc: 'Menampilkan detail daftar riwayat transaksi pengeluaran lengkap beserta jumlah, sumber dana (Kas/Bank), dan link nota fisik jika ter-upload.'
                }
            ],
            'accounting': [
                {
                    title: 'Modul Laporan Akuntansi Otomatis',
                    selector: '#accounting-tabs',
                    desc: 'Klik tab untuk berpindah laporan: Jurnal Umum (GL), Neraca Saldo (TB), Laba Rugi (LR), atau Aliran Arus Kas (LAK).'
                },
                {
                    title: 'Ekspor Jurnal Ke Excel',
                    selector: '#export-excel-btn',
                    desc: 'Ekspor seluruh riwayat pencatatan transaksi jurnal umum (GL) double-entry ke berkas spreadsheet Excel berformat standar akuntansi.'
                },
                {
                    title: 'Tabel Laporan Keuangan Keakuntanan',
                    selector: '#accounting-data-tables',
                    desc: 'Rincian data laporan keuangan. Pada Neraca Saldo terdapat indikator penyeimbang otomatis (Balance Warning) jika sisi debit & kredit tidak balance.'
                }
            ],
            'students': [
                {
                    title: 'Tambah Siswa Baru',
                    selector: '#add-student-btn',
                    desc: 'Form pendaftaran master profil siswa aktif untuk memudahkan pendataan siswa reguler/intensif Fikra Academy.'
                },
                {
                    title: 'Tabel Manajemen Profil Siswa',
                    selector: '#student-table-card',
                    desc: 'Daftar semua siswa aktif & non-aktif. Anda dapat mengedit status aktif/non-aktif siswa atau memperbarui nomor HP di sini.'
                }
            ],
            'accounts': [
                {
                    title: 'Tambah Akun Rekening Baru (CoA)',
                    selector: '#add-account-btn',
                    desc: 'Membuat kode akun klasifikasi keuangan baru (misal: 1.10.xx.xx untuk aset atau 5.16.xx.xx untuk beban) sesuai kebutuhan bimbel.'
                },
                {
                    title: 'Tabel Struktur Chart of Accounts',
                    selector: '#account-table-card',
                    desc: 'Daftar kode pembukuan CoA. Akun utama (Kas/Bank) dan akun yang memiliki histori transaksi dilindungi dari penghapusan demi menjaga validitas ledger.'
                }
            ],
            'activity-logs': [
                {
                    title: 'Penyaringan Log Audit',
                    selector: '#audit-filter-form',
                    desc: 'Filter log berdasarkan nama pengguna (bendahara 1/2), tanggal mulai, atau tanggal akhir log yang ingin dilacak.'
                },
                {
                    title: 'Kategori Log Keamanan',
                    selector: '#audit-tabs',
                    desc: 'Pilih Tab "Riwayat Aktivitas" untuk melacak aksi tulis data, atau Tab "Sesi Login" untuk melacak riwayat IP/perangkat login.'
                },
                {
                    title: 'Tabel Log Riwayat Audit',
                    selector: '#audit-tables-card',
                    desc: 'Histori log detail yang menunjukkan nama operator, tanggal waktu, deskripsi aksi, alamat IP, serta detail tipe perangkat (OS, Browser, Device).'
                }
            ]
        };

        let currentTourSteps = [];
        let currentTourIndex = 0;
        let highlightedElement = null;

        function getPageKey() {
            const path = window.location.pathname.replace(/^\/|\/$/g, '');
            if (path === '' || path === 'dashboard') {
                return 'dashboard';
            }
            if (path.includes('pemasukan')) return 'pemasukan';
            if (path.includes('pengeluaran')) return 'pengeluaran';
            if (path.includes('accounting')) return 'accounting';
            if (path.includes('students')) return 'students';
            if (path.includes('accounts')) return 'accounts';
            if (path.includes('activity-logs')) return 'activity-logs';
            return null;
        }

        function openTourPrompt() {
            const pageKey = getPageKey();
            if (pageKey && tourStepsConfig[pageKey]) {
                document.getElementById('tourPromptModal').classList.remove('hidden');
            } else {
                alert('Panduan interaktif tidak tersedia untuk halaman ini.');
            }
        }

        function closeTourPrompt() {
            document.getElementById('tourPromptModal').classList.add('hidden');
        }

        function startTour() {
            closeTourPrompt();
            const pageKey = getPageKey();
            currentTourSteps = tourStepsConfig[pageKey] || [];
            currentTourIndex = 0;

            if (currentTourSteps.length > 0) {
                document.getElementById('tourCard').classList.remove('hidden');
                showTourStep();
            }
        }

        function showTourStep() {
            // Remove previous highlights
            removeHighlight();

            const step = currentTourSteps[currentTourIndex];
            if (!step) return;

            // Update tooltip text
            document.getElementById('tourStepIndicator').textContent = `Langkah ${currentTourIndex + 1} dari ${currentTourSteps.length}`;
            document.getElementById('tourStepTitle').textContent = step.title;
            document.getElementById('tourStepDesc').textContent = step.desc;

            // Configure button states
            document.getElementById('tourPrevBtn').disabled = (currentTourIndex === 0);
            
            const nextBtn = document.getElementById('tourNextBtn');
            if (currentTourIndex === currentTourSteps.length - 1) {
                nextBtn.textContent = 'Selesai';
                nextBtn.className = 'bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-3 py-1.5 rounded-lg text-[10px] transition-all shadow cursor-pointer';
            } else {
                nextBtn.textContent = 'Berikutnya \u2192';
                nextBtn.className = 'bg-[#1e3a8a] hover:opacity-90 text-white font-bold px-3 py-1.5 rounded-lg text-[10px] transition-all shadow cursor-pointer';
            }

            // Find and highlight element
            const el = document.querySelector(step.selector);
            if (el) {
                highlightedElement = el;
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Add highlight classes
                el.classList.add('ring-4', 'ring-amber-500', 'ring-offset-2', 'animate-pulse', 'transition-all');
            }
        }

        function nextTourStep() {
            if (currentTourIndex < currentTourSteps.length - 1) {
                currentTourIndex++;
                showTourStep();
            } else {
                exitTour();
            }
        }

        function prevTourStep() {
            if (currentTourIndex > 0) {
                currentTourIndex--;
                showTourStep();
            }
        }

        function removeHighlight() {
            if (highlightedElement) {
                highlightedElement.classList.remove('ring-4', 'ring-amber-500', 'ring-offset-2', 'animate-pulse', 'transition-all');
                highlightedElement = null;
            }
        }

        function exitTour() {
            removeHighlight();
            document.getElementById('tourCard').classList.add('hidden');
        }
    </script>
    @yield('scripts')
</body>
</html>
