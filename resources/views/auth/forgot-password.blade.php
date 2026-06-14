<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Fikra Academy</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0f172a] via-[#1e293b] to-[#1e3a8a] flex items-center justify-center p-4 antialiased">

    <div class="relative w-full max-w-md">
        <!-- Decorative blobs -->
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl"></div>

        <!-- Glassmorphism Card -->
        <div class="relative bg-white/10 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl transition-all">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <span class="inline-flex p-3 bg-emerald-500 rounded-2xl text-white font-black text-xl shadow-lg shadow-emerald-500/20 mb-4">FA</span>
                <h2 class="text-2xl font-black text-white tracking-tight">Lupa Kata Sandi?</h2>
                <p class="text-xs text-slate-300 opacity-80 mt-2">Masukkan email masuk Anda. Kami akan mengirimkan tautan reset kata sandi ke email cadangan (backup) Anda.</p>
            </div>

            <!-- Status Alerts -->
            @if (session('status'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs">
                    <div class="flex gap-2">
                        <svg class="h-4.5 w-4.5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75-4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5M12 14v-4m0 0l-1.5 1.5M12 10l1.5 1.5"></path>
                        </svg>
                        <p class="font-medium leading-relaxed">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Local Testing simulated link banner -->
            @if (session('simulated_link'))
                <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs space-y-3">
                    <p class="font-bold flex items-center gap-1.5 text-amber-400">
                        <span>💡</span> MOCK EMAIL CLIENT (Uji Coba Lokal)
                    </p>
                    <p class="leading-relaxed">Sistem mendeteksi Anda berjalan di lokal. Anda dapat mengeklik tombol hijau di bawah ini untuk mensimulasikan pembukaan tautan reset dari inbox email backup <strong>({{ session('backup_email_raw') }})</strong>:</p>
                    <a href="{{ session('simulated_link') }}" class="block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow shadow-emerald-500/10">
                        Buka Link Reset Password &rarr;
                    </a>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email Masuk</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="bendahara1@fikra.com" class="w-full rounded-xl border border-white/10 bg-white/5 pl-10 pr-4 py-3 text-sm text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 focus:outline-none transition-all placeholder:text-slate-500">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-400 mt-1.5 font-medium flex items-center gap-1">
                            <span class="inline-block h-1 w-1 rounded-full bg-rose-400 shrink-0"></span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md shadow-emerald-500/10 hover:shadow-emerald-500/25 active:scale-[0.98]">
                    Kirim Link Reset
                </button>
            </form>
            
            <!-- Back to Login -->
            <div class="mt-8 pt-6 border-t border-white/5 text-center">
                <a href="{{ route('login') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-bold transition-colors">&larr; Kembali ke Halaman Login</a>
            </div>
        </div>
    </div>

</body>
</html>
