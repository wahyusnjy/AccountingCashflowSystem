<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setel Ulang Kata Sandi - Fikra Academy</title>
    
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
                <h2 class="text-2xl font-black text-white tracking-tight">Kata Sandi Baru</h2>
                <p class="text-xs text-slate-300 opacity-80 mt-2">Buat kata sandi baru yang aman untuk akun Anda.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Display (Read-Only) -->
                <div>
                    <label for="email" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email Akun</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" readonly required class="w-full rounded-xl border border-white/10 bg-white/5 pl-10 pr-4 py-3 text-sm text-slate-300 cursor-not-allowed focus:outline-none transition-all">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-400 mt-1.5 font-medium flex items-center gap-1">
                            <span class="inline-block h-1 w-1 rounded-full bg-rose-400 shrink-0"></span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required autofocus placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-white/10 bg-white/5 pl-10 pr-4 py-3 text-sm text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 focus:outline-none transition-all placeholder:text-slate-500">
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-400 mt-1.5 font-medium flex items-center gap-1">
                            <span class="inline-block h-1 w-1 rounded-full bg-rose-400 shrink-0"></span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full rounded-xl border border-white/10 bg-white/5 pl-10 pr-4 py-3 text-sm text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-400 focus:outline-none transition-all placeholder:text-slate-500">
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md shadow-emerald-500/10 hover:shadow-emerald-500/25 active:scale-[0.98]">
                    Setel Ulang Password
                </button>
            </form>
        </div>
    </div>

</body>
</html>
