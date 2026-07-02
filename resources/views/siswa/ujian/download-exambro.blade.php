@extends('layouts.app')

@section('title', 'ExamBrowser Diperlukan - Pembelajaran Digital')
@section('breadcrumb', 'ExamBrowser')
@section('page-title', 'ExamBrowser Diperlukan')

@section('content')
<div class="w-full px-4 md:px-6 py-12 text-white">
    <div class="max-w-md mx-auto">
        
        <div class="luxury-card overflow-hidden text-center p-8 border border-red-500/20 bg-slate-950/80 backdrop-blur-md relative">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full bg-red-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-48 h-48 rounded-full bg-blue-500/10 blur-3xl"></div>

            <!-- Shield/Lock Icon -->
            <div class="w-20 h-20 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 text-3xl mx-auto mb-6 shadow-[0_0_20px_rgba(239,68,68,0.15)] animate-pulse">
                <i class="fas fa-user-shield"></i>
            </div>

            <!-- Header -->
            <h4 class="text-white font-bold text-xl md:text-2xl leading-snug">Akses Ujian Diblokir!</h4>
            <p class="text-red-400 font-semibold text-xs uppercase tracking-wider mt-1">Wajib Menggunakan ExamBrowser</p>

            <!-- Description -->
            <div class="my-6 text-sm leading-relaxed text-white/75 text-left bg-white/5 p-4 rounded-xl border border-white/5">
                Untuk menjaga keamanan, integritas, dan mencegah kecurangan selama ujian berlangsung, Anda **diwajibkan** menggunakan aplikasi resmi **ExamBrowser**. 
                <br><br>
                Sistem mendeteksi Anda saat ini menggunakan browser standar (seperti Chrome, Safari, atau Opera) yang **tidak diperbolehkan** untuk mengakses lembar ujian.
                <br><br>
                <span class="text-white/40 text-[10px] block border-t border-white/5 pt-2 font-mono break-all">
                    User-Agent Terdeteksi:<br>
                    <span class="text-red-400/80 font-bold">{{ $userAgent }}</span>
                </span>
            </div>

            <!-- Download Action Buttons -->
            <div class="space-y-3">
                <a href="{{ $downloadUrl }}" target="_blank" class="block w-full py-3 bg-gradient-to-r from-red-500 to-rose-600 hover:opacity-90 rounded-xl text-white font-bold text-xs tracking-wider transition shadow-[0_4px_12px_rgba(244,63,94,0.3)]">
                    <i class="fab fa-android mr-1.5 text-sm"></i> UNDUH EXAMBROWSER (APK/PLAYSTORE)
                </a>
                
                <a href="{{ route('siswa.ujian.index') }}" class="block w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-white/80 font-semibold text-xs transition">
                    Kembali ke Daftar Ujian
                </a>
            </div>

            <!-- Instructions -->
            <div class="mt-8 pt-6 border-t border-white/5 text-left text-xs">
                <h6 class="text-white font-bold mb-2 uppercase tracking-wide text-[10px] text-white/50">Petunjuk Pengerjaan:</h6>
                <ol class="list-decimal list-inside space-y-1.5 text-white/60">
                    <li>Klik tombol **Unduh** di atas untuk menginstal aplikasi.</li>
                    <li>Buka aplikasi **ExamBrowser** di perangkat Anda.</li>
                    <li>Masukkan alamat web sekolah atau scan QR Code ujian.</li>
                    <li>Login menggunakan akun siswa Anda dan mulai ujian.</li>
                </ol>
            </div>
        </div>

    </div>
</div>
@endsection
