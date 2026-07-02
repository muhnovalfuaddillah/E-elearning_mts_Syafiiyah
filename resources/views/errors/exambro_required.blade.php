<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Akses Ditolak - Wajib Exambro</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse-glow {
      0%, 100% { opacity: 0.5; transform: scale(1); }
      50%       { opacity: 1;   transform: scale(1.05); }
    }
    .anim-fadeup { animation: fadeInUp .7s ease both; }
    .delay-1 { animation-delay: .1s; }
    .delay-2 { animation-delay: .25s; }
    .delay-3 { animation-delay: .4s; }
    .delay-4 { animation-delay: .55s; }
    
    .glow-red { text-shadow: 0 0 40px rgba(239, 68, 68, 0.6), 0 2px 0 rgba(0,0,0,0.5); }
    
    .btn-primary { transition: all 0.2s; }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary:active { transform: translateY(1px); }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            dark: { DEFAULT: '#070715', 800: '#0e0e28', 700: '#141430' }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-[#070715] text-white min-h-screen flex flex-col items-center justify-center px-4 py-12 text-center overflow-hidden">

  <!-- Glow Background Effect -->
  <div class="absolute w-[300px] h-[300px] bg-red-600/10 rounded-full blur-[120px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

  <div class="relative z-10 max-w-md mx-auto">
    <!-- School Badge / App Name -->
    <div class="anim-fadeup delay-1 mb-2">
      <span class="text-[11px] tracking-[4px] uppercase text-red-400 font-semibold">Keamanan Ujian</span>
    </div>
    <div class="anim-fadeup delay-1 flex items-center justify-center gap-1.5 text-slate-400 text-sm mb-8">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
      Aplikasi Pembelajaran Digital MTs Syafiiyah
    </div>

    <!-- Lock Icon / Alert -->
    <div class="anim-fadeup delay-2 flex justify-center mb-6">
      <div class="p-5 bg-red-500/10 border border-red-500/20 rounded-full animate-[pulse-glow_3s_infinite_ease-in-out]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
    </div>

    <!-- Error Title -->
    <h1 class="anim-fadeup delay-3 text-2xl font-bold text-white mb-4 glow-red">
      Wajib Menggunakan Exambro!
    </h1>
    
    <!-- Description -->
    <p class="anim-fadeup delay-3 text-sm text-slate-400 leading-relaxed mb-8 px-2">
      Demi menjaga kejujuran dan keamanan ujian, halaman ini <span class="text-red-400 font-semibold">tidak dapat diakses</span> melalui browser biasa (Chrome, Safari, dll). 
      <br><br>
      Silakan buka aplikasi <span class="text-white font-medium">MTs Syafiiyah (Exambro)</span> di perangkat Anda untuk dapat mengikuti ujian ini.
    </p>

    <!-- Action Buttons -->
    <div class="anim-fadeup delay-4 flex flex-col sm:flex-row gap-3 justify-center items-center">
      <a href="https://mts.edu.yasbahu.sch.id" class="btn-primary w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium shadow-lg shadow-red-600/20 flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1V10"/>
        </svg>
        Kembali ke Beranda
      </a>
      <button onclick="window.location.reload()" class="btn-primary w-full sm:w-auto px-6 py-3 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 rounded-xl text-sm font-medium flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Periksa Ulang
      </button>
    </div>

    <!-- Debug Info -->
    <div class="anim-fadeup delay-4 mt-8 p-3 bg-white/5 border border-white/5 rounded-xl text-left text-[11px] text-slate-500 max-w-sm mx-auto">
      <p class="font-semibold text-slate-400 mb-1">Info Browser Anda (Untuk Laporan):</p>
      <div class="overflow-x-auto whitespace-pre-wrap break-all font-mono">
        <strong>User-Agent:</strong> {{ $userAgent }}<br>
        <strong>X-Requested-With:</strong> {{ $requestedWith }}
      </div>
    </div>

    <!-- Footer -->
    <p class="anim-fadeup delay-4 mt-8 text-xs text-slate-600">
      &copy; 2026 MTs Syafiiyah - Safe Exam Browser Detection System
    </p>
  </div>

</body>
</html>
