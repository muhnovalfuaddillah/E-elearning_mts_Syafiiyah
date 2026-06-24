<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'LuxuryDash')</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
          boxShadow: {
            'luxury': '0 20px 35px -12px rgba(0, 0, 0, 0.15)',
            'glow': '0 0 20px rgba(168, 85, 247, 0.3)',
          }
        }
      }
    }
  </script>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #0a0a0a;
      overflow-x: hidden;
    }

    /* Luxury Gradient Background */
    .bg-luxury {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      height: 40%;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
      z-index: 0;
    }

    /* Floating Sidebar */
    #sidenav {
      position: fixed;
      top: 1rem;
      bottom: 1rem;
      left: -280px;
      width: 260px;
      background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%);
      border-radius: 1.5rem;
      backdrop-filter: blur(20px);
      border: 1px solid rgba(168, 85, 247, 0.2);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
    }

    @media (min-width: 1280px) {
      #sidenav { left: 1.5rem; }
    }
    #sidenav.open { left: 1rem; }

    #sidenav::-webkit-scrollbar { width: 4px; }
    #sidenav::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
    #sidenav::-webkit-scrollbar-thumb { background: #a855f7; border-radius: 10px; }

    #overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      backdrop-filter: blur(4px);
      z-index: 989;
    }
    #overlay.show { display: block; }

    @media (min-width: 1280px) {
      #main-content { margin-left: 18rem; }
    }

    /* Nav Links */
    .nav-link-luxury {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      margin: 0.25rem 0.75rem;
      border-radius: 0.75rem;
      color: rgba(255,255,255,0.7);
      transition: all 0.3s ease;
    }
    .nav-link-luxury:hover {
      background: rgba(168, 85, 247, 0.15);
      color: white;
      transform: translateX(4px);
    }
    .nav-link-active {
      background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(236, 72, 153, 0.1));
      color: white;
      border: 1px solid rgba(168, 85, 247, 0.3);
    }

    /* Cards & Icons */
    .luxury-card {
      background: linear-gradient(135deg, rgba(26, 26, 46, 0.95), rgba(15, 15, 26, 0.95));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(168, 85, 247, 0.15);
      border-radius: 1.5rem;
      box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }
    .luxury-card:hover {
      transform: translateY(-4px);
      border-color: rgba(168, 85, 247, 0.4);
      box-shadow: 0 25px 40px -12px rgba(168, 85, 247, 0.2);
    }
    .luxury-icon {
      background: linear-gradient(135deg, #a855f7, #ec4899);
      width: 3rem;
      height: 3rem;
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Dropdown Animation */
    .dropdown-enter {
      animation: fadeIn 0.2s ease-out forwards;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px) scale(0.95); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Floating Button */
    .floating-btn {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      width: 3.5rem;
      height: 3.5rem;
      background: linear-gradient(135deg, #a855f7, #ec4899);
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 990;
      box-shadow: 0 4px 14px rgba(168, 85, 247, 0.4);
      transition: all 0.3s;
    }
    @media (min-width: 768px) {
      .floating-btn { bottom: 2rem; right: 2rem; }
    }
    .floating-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(168, 85, 247, 0.6);
    }

    /* Style untuk Light Theme */
    body.light-theme {
      background: #f4f5f7;
    }
    body.light-theme .bg-luxury {
      background: linear-gradient(135deg, #e0e6ed 0%, #d5deea 50%, #c8d6e5 100%);
    }
    body.light-theme #sidenav {
      background: linear-gradient(135deg, #ffffff 0%, #f0f3f8 100%);
      border: 1px solid rgba(168, 85, 247, 0.15);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
    }
    body.light-theme .nav-link-luxury {
      color: rgba(0, 0, 0, 0.6);
    }
    body.light-theme .nav-link-luxury:hover {
      background: rgba(168, 85, 247, 0.08);
      color: #7c3aed;
    }
    body.light-theme .nav-link-active {
      background: linear-gradient(135deg, rgba(168, 85, 247, 0.1), rgba(236, 72, 153, 0.05));
      color: #7c3aed;
      border: 1px solid rgba(168, 85, 247, 0.2);
    }
    body.light-theme .luxury-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(245, 247, 250, 0.95));
      border: 1px solid rgba(168, 85, 247, 0.15);
      box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.05);
    }
    body.light-theme .luxury-card:hover {
      border-color: rgba(168, 85, 247, 0.3);
      box-shadow: 0 25px 40px -12px rgba(168, 85, 247, 0.1);
    }
    body.light-theme .text-white {
      color: #1e293b !important;
    }
    body.light-theme .text-white\/80 {
      color: #334155 !important;
    }
    body.light-theme .text-white\/70 {
      color: #475569 !important;
    }
    body.light-theme .text-white\/60 {
      color: #475569 !important;
    }
    body.light-theme .text-white\/50 {
      color: #64748b !important;
    }
    body.light-theme .text-white\/40 {
      color: #64748b !important;
    }
    body.light-theme .text-white\/30 {
      color: #94a3b8 !important;
    }
    body.light-theme .text-slate-400 {
      color: #475569 !important;
    }
    body.light-theme .text-slate-300 {
      color: #334155 !important;
    }
    body.light-theme select,
    body.light-theme input,
    body.light-theme textarea {
      background-color: rgba(0, 0, 0, 0.03) !important;
      border: 1px solid rgba(0, 0, 0, 0.08) !important;
      color: #1e293b !important;
    }
    body.light-theme select:focus,
    body.light-theme input:focus,
    body.light-theme textarea:focus {
      border-color: #7c3aed !important;
      background-color: #ffffff !important;
    }
    body.light-theme .luxury-table thead {
      background-color: rgba(0, 0, 0, 0.02) !important;
    }
    body.light-theme .luxury-table tr:hover {
      background-color: rgba(0, 0, 0, 0.02) !important;
    }
    body.light-theme .border-white\/10,
    body.light-theme .border-white\/5 {
      border-color: rgba(0, 0, 0, 0.06) !important;
    }
    body.light-theme .bg-white\/5 {
      background-color: rgba(0, 0, 0, 0.02) !important;
    }
    body.light-theme select option {
      color: #1e293b !important;
      background-color: #ffffff !important;
    }
    body.light-theme .active-indicator {
      background-color: #7c3aed;
    }

    /* AI Chatbot styles */
    .chatbot-floating-btn {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      width: 3.5rem;
      height: 3.5rem;
      background: linear-gradient(135deg, #6366f1, #a855f7);
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 990;
      box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4), 0 0 0 0px rgba(99, 102, 241, 0.4);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: chatbotPulse 2s infinite;
    }
    @media (min-width: 768px) {
      .chatbot-floating-btn { bottom: 2rem; right: 2rem; }
    }
    .chatbot-floating-btn:hover {
      transform: scale(1.1) rotate(10deg);
      box-shadow: 0 0 25px rgba(99, 102, 241, 0.7);
    }
    @keyframes chatbotPulse {
      0% {
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4), 0 0 0 0px rgba(99, 102, 241, 0.4);
      }
      70% {
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4), 0 0 0 10px rgba(99, 102, 241, 0);
      }
      100% {
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4), 0 0 0 0px rgba(99, 102, 241, 0);
      }
    }
    
    /* Shift settings button left to make room for chatbot */
    .floating-btn {
      right: 5.5rem !important;
      background: rgba(255, 255, 255, 0.1) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: rgba(255, 255, 255, 0.7) !important;
    }
    @media (min-width: 768px) {
      .floating-btn { right: 6rem !important; }
    }
    body.light-theme .floating-btn {
      background: rgba(0, 0, 0, 0.05) !important;
      border: 1px solid rgba(0, 0, 0, 0.05) !important;
      color: rgba(0, 0, 0, 0.6) !important;
    }

    #chatbot-panel {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform: translateY(20px) scale(0.95);
      opacity: 0;
      pointer-events: none;
    }
    #chatbot-panel.open {
      transform: translateY(0) scale(1);
      opacity: 1;
      pointer-events: auto;
    }
    
    /* Chat bubbles styling */
    .chat-bubble-user {
      background: linear-gradient(135deg, #a855f7, #6366f1);
      color: white !important;
      border-bottom-right-radius: 0.25rem;
    }
    .chat-bubble-ai {
      background: rgba(255, 255, 255, 0.05);
      color: #e2e8f0;
      border-bottom-left-radius: 0.25rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    body.light-theme .chat-bubble-ai {
      background: rgba(0, 0, 0, 0.05);
      color: #1e293b;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    /* Dot typing animation */
    .typing-dot {
      width: 6px;
      height: 6px;
      background-color: #a855f7;
      border-radius: 50%;
      animation: typingBounce 1.4s infinite ease-in-out both;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce {
      0%, 80%, 100% { transform: scale(0); }
      40% { transform: scale(1); }
    }
  </style>
</head>

<body class="antialiased pb-24 lg:pb-0">

<!-- Luxury Background & Overlay -->
<div class="bg-luxury"></div>
<div id="overlay" onclick="closeSidenav()"></div>

<!-- ========== SIDEBAR LUXURY ========== -->
<aside id="sidenav">
  <div class="p-6 border-b border-white/10">
    <div class="flex items-center gap-3">
      <div class="luxury-icon w-10 h-10 rounded-xl">
        <i class="fas fa-user-astronaut"></i>
      </div>
      <div>
        <h5 class="text-white font-bold text-lg">Pembelajaran Digital  </h5>
        <p class="text-white/40 text-xs">{{ auth()->user()->role }} Dashboard</p>
      </div>
    </div>
    <button onclick="closeSidenav()" class="xl:hidden absolute top-4 right-4 text-white/50 hover:text-white">
      <i class="fas fa-times text-xl"></i>
    </button>
  </div>
 <nav class="flex-1 py-4">

    {{-- MENU ADMIN --}}
    @if(auth()->user()->role == 'admin')

        <div class="px-4 mb-3">
            <p class="text-white/40 text-xs uppercase tracking-wider px-3">
                Administrator
            </p>
        </div>

        
        <a href="{{ route('admin.dashboard') }}" class="nav-link-luxury {{ request()->is('admin/dashboard') ? 'nav-link-active text-white font-semibold bg-white/10 rounded-lg' : 'text-white/50 hover:text-white transition-colors duration-200' }}">
                <i class="fas fa-chart-line w-5"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('admin.kelas.index') }}" 
              class="nav-link-luxury {{ request()->routeIs('admin.kelas.*') ? 'nav-link-active' : '' }}">
                <i class="fas fa-school w-5"></i>
                <span>Data Kelas</span>
            </a>

            <a href="{{ route('admin.guru.index') }}" 
              class="nav-link-luxury {{ request()->routeIs('admin.guru.*') ? 'nav-link-active' : '' }}">
                <i class="fas fa-chalkboard-teacher w-5"></i>
                <span>Data Guru</span>
            </a>

            <a href="{{ route('admin.siswa.index') }}" 
              class="nav-link-luxury {{ request()->routeIs('admin.siswa.*') ? 'nav-link-active' : '' }}">
                <i class="fas fa-users w-5"></i>
                <span>Data Siswa</span>
            </a>

            <a href="{{ route('admin.mata-pelajaran.index') }}" 
              class="nav-link-luxury {{ request()->routeIs('admin.mata-pelajaran.*') ? 'nav-link-active' : '' }}">
                <i class="fas fa-book w-5"></i>
                <span>Mata Pelajaran</span>
            </a>

        <a href="{{ route('admin.materi.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.materi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-book w-5"></i>
            <span>Materi</span>
        </a>

        <a href="{{ route('admin.penilaian.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.penilaian.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-star w-5"></i>
            <span>Penilaian</span>
        </a>

        <a href="{{ route('admin.absensi.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.absensi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-calendar-check w-5"></i>
            <span>Absensi</span>
        </a>

        <a href="{{ route('admin.jurnal.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.jurnal.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-signature w-5"></i>
            <span>Jurnal Mengajar</span>
        </a>

        <a href="{{ route('admin.pengumuman.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.pengumuman.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-bullhorn w-5"></i>
            <span>Pengumuman Sekolah</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.jadwal.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-calendar-alt w-5"></i>
            <span>Jadwal Pelajaran</span>
        </a>

        <a href="{{ route('admin.calendar.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.calendar.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-calendar-alt w-5"></i>
            <span>Agenda Sekolah</span>
        </a>

        <a href="{{ route('admin.activity-logs.index') }}" class="nav-link-luxury {{ request()->routeIs('admin.activity-logs.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-history w-5"></i>
            <span>Log Aktivitas</span>
        </a>

        <a href="{{ route('admin.backup') }}" class="nav-link-luxury hover:text-red-400">
            <i class="fas fa-database w-5 text-red-500"></i>
            <span>Backup Database</span>
        </a>

        <div class="px-4 mt-4 mb-2">
            <p class="text-white/40 text-xs uppercase tracking-wider px-3">
                Laporan & Rekap
            </p>
        </div>
        <a href="{{ route('admin.laporan.siswa') }}" class="nav-link-luxury {{ request()->routeIs('admin.laporan.siswa') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-invoice w-5"></i>
            <span>Laporan Siswa</span>
        </a>
        <a href="{{ route('admin.laporan.guru') }}" class="nav-link-luxury {{ request()->routeIs('admin.laporan.guru') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-invoice w-5"></i>
            <span>Laporan Guru</span>
        </a>
        <a href="{{ route('admin.laporan.absensi') }}" class="nav-link-luxury {{ request()->routeIs('admin.laporan.absensi') ? 'nav-link-active' : '' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span>Rekap Absensi</span>
        </a>
        <a href="{{ route('admin.laporan.nilai') }}" class="nav-link-luxury {{ request()->routeIs('admin.laporan.nilai') ? 'nav-link-active' : '' }}">
            <i class="fas fa-graduation-cap w-5"></i>
            <span>Rekap Nilai</span>
        </a>

    {{-- MENU GURU --}}
    @elseif(auth()->user()->role == 'guru')

        <div class="px-4 mb-3">
            <p class="text-white/40 text-xs uppercase tracking-wider px-3">
                Guru
            </p>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="nav-link-luxury {{ request()->is('guru/dashboard') ? 'nav-link-active text-white font-semibold bg-white/10 rounded-lg' : 'text-white/50 hover:text-white transition-colors duration-200' }}">
                <i class="fas fa-chart-line w-5"></i><span>Dashboard</span>
            </a>


        <a href="{{ route('guru.materi.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.materi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-book-open w-5"></i>
            <span>Materi Pelajaran</span>
        </a>

        <a href="{{ route('guru.kelas.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.kelas.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-school w-5"></i>
            <span>Daftar Kelas</span>
        </a>

        <a href="{{ route('guru.tugas.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.tugas.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-alt w-5"></i>
            <span>Tugas</span>
        </a>

        <a href="{{ route('guru.penilaian.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.penilaian.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-star w-5"></i>
            <span>Penilaian</span>
        </a>

        <a href="{{ route('guru.absensi.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.absensi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-user-check w-5"></i>
            <span>Absensi</span>
        </a>

        <a href="{{ route('guru.jadwal.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.jadwal.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-calendar-alt w-5"></i>
            <span>Jadwal Mengajar</span>
        </a>

        <a href="{{ route('guru.jurnal.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.jurnal.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-signature w-5"></i>
            <span>Jurnal Mengajar</span>
        </a>

        <a href="{{ route('guru.pengumuman.index') }}" class="nav-link-luxury {{ request()->routeIs('guru.pengumuman.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-bullhorn w-5"></i>
            <span>Pengumuman Kelas</span>
        </a>

        <div class="px-4 mt-4 mb-2">
            <p class="text-white/40 text-xs uppercase tracking-wider px-3">
                Laporan & Rekap
            </p>
        </div>
        <a href="{{ route('guru.laporan.absensi') }}" class="nav-link-luxury {{ request()->routeIs('guru.laporan.absensi') ? 'nav-link-active' : '' }}">
            <i class="fas fa-clipboard-list w-5"></i>
            <span>Rekap Absensi</span>
        </a>
        <a href="{{ route('guru.laporan.nilai') }}" class="nav-link-luxury {{ request()->routeIs('guru.laporan.nilai') ? 'nav-link-active' : '' }}">
            <i class="fas fa-graduation-cap w-5"></i>
            <span>Rekap Nilai</span>
        </a>

    @elseif(auth()->user()->role == 'siswa')

        <div class="px-4 mb-3">
            <p class="text-white/40 text-xs uppercase tracking-wider px-3">
                Siswa
            </p>
        </div>

        <a href="{{ route('siswa.dashboard') }}" class="nav-link-luxury {{ request()->routeIs('siswa.dashboard') ? 'nav-link-active' : '' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('siswa.materi.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.materi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-book-open w-5"></i>
            <span>Materi Pelajaran</span>
        </a>

        <a href="{{ route('siswa.tugas.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.tugas.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-file-alt w-5"></i>
            <span>Tugas Mandiri</span>
        </a>

        <a href="{{ route('siswa.penilaian.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.penilaian.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-star w-5"></i>
            <span>Nilai Saya</span>
        </a>

        <a href="{{ route('siswa.absensi.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.absensi.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-user-check w-5"></i>
            <span>Kehadiran</span>
        </a>

        <a href="{{ route('siswa.pengumuman.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.pengumuman.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-bullhorn w-5"></i>
            <span>Pengumuman</span>
        </a>

        <a href="{{ route('siswa.jadwal.index') }}" class="nav-link-luxury {{ request()->routeIs('siswa.jadwal.*') ? 'nav-link-active' : '' }}">
            <i class="fas fa-calendar-alt w-5"></i>
            <span>Jadwal Pelajaran</span>
        </a>

    @endif

    {{-- MENU UMUM --}}
    <div class="px-4 mt-6 mb-3">
        <p class="text-white/40 text-xs uppercase tracking-wider px-3">
            Account
        </p>
    </div>

    <a href="{{ route('profile') }}" class="nav-link-luxury {{ request()->routeIs('profile') ? 'nav-link-active' : '' }}">
        <i class="fas fa-user w-5"></i>
        <span>Profile</span>
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-link-luxury w-full text-left">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </button>
    </form>

</nav>
</aside>

<!-- ========== MAIN CONTENT ========== -->
<main id="main-content" class="relative z-10 flex flex-col min-h-screen">

  <!-- Luxury Navbar -->
  <nav class="relative z-[999] w-full px-4 pt-4 pb-2 md:px-6">
    <div class="luxury-card p-4 md:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
      
      <!-- Container Utama Navbar -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 lg:gap-6">
        
        <!-- Sisi Kiri: Hamburger, Welcome Text, Tanggal -->
        <div class="flex flex-col sm:flex-row sm:items-center w-full lg:w-auto gap-4">
          
          <div class="flex items-center gap-3">
            <button onclick="openSidenav()" class="xl:hidden flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-white hover:bg-white/20 transition-all">
              <i class="fas fa-bars text-lg"></i>
            </button>
            
            <div class="min-w-0">
              <h1 class="text-lg sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2 truncate">
                Assalamualaikum, {{ auth()->user()->name }}!
              </h1>
              <p class="mt-0.5 text-xs sm:text-sm text-slate-400 truncate">
                Ringkasan aktivitas hari ini.
              </p>
            </div>
          </div>
          
          <div class="hidden sm:block h-10 w-px bg-white/10 mx-2"></div>
          
          <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/10 rounded-xl shrink-0 self-start sm:self-auto">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-xs font-medium text-slate-300 whitespace-nowrap">
              {{ date('l, j F Y') }}
            </span>
          </div>
        </div>

        <!-- Sisi Kanan: Search Bar, Settings, Notif -->
        <div class="flex items-center gap-2 sm:gap-4 w-full lg:w-auto justify-between lg:justify-end mt-1 lg:mt-0">
          
          <div class="relative group flex-1 lg:flex-none">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/40 text-xs group-focus-within:text-purple-400 transition-colors"></i>
            <input type="text" placeholder="Search..." 
              class="w-full lg:w-56 pl-9 pr-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white text-sm placeholder:text-white/30 focus:border-purple-500 focus:bg-white/10 focus:outline-none transition-all">
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <!-- Theme Toggle Button -->
            <button onclick="toggleTheme()" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-purple-400 hover:bg-white/20 transition-all" title="Toggle Theme">
              <i id="theme-toggle-icon" class="fas fa-sun"></i>
            </button>

            <button class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-purple-400 hover:bg-white/20 transition-all">
             <a href="/pengaturan" class="btn-floating"><i class="fas fa-cog"></i></a>
            </button>

            <!-- Jadwal Sholat Dropdown -->
            <div class="relative">
              <button onclick="toggleSholatDropdown(event)" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-purple-400 hover:bg-white/20 transition-all relative group z-10" title="Jadwal Sholat Probolinggo">
                <i class="fas fa-mosque"></i>
              </button>
              
              <!-- Dropdown Jadwal Sholat -->
              <div id="sholat-dropdown" class="hidden absolute right-[-10px] sm:right-0 mt-3 w-72 z-[1000] origin-top-right">
                <!-- Segitiga Atas -->
                <div class="absolute -top-2 right-[22px] sm:right-3.5 w-4 h-4 bg-slate-800 border-t border-l border-white/10 rotate-45 transform"></div>
                
                <!-- Inner Container -->
                <div class="relative z-50 overflow-hidden rounded-2xl w-full shadow-2xl bg-slate-900/95 border border-white/10">
                  <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center bg-black/20">
                    <span class="text-sm font-semibold text-white">Jadwal Sholat Hari Ini</span>
                    <span class="text-[10px] bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full font-bold border border-purple-500/30">
                      Probolinggo
                    </span>
                  </div>
                  
                  <div class="p-4 space-y-2.5 text-xs text-white" id="sholat-times-container">
                    <div class="flex items-center justify-center py-4 text-white/50">
                      <i class="fas fa-spinner animate-spin mr-2"></i> Loading...
                    </div>
                  </div>
                  
                  <div class="p-2 border-t border-white/10 bg-black/20 text-center">
                    <span class="text-[9px] text-white/30" id="sholat-date-info">equran.id API</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notification Wrapper -->
            @auth
              @php
                $unreadNotifications = auth()->user()->unreadAppNotifications()->latest()->take(5)->get();
                $unreadCount = auth()->user()->unreadAppNotifications()->count();
              @endphp
            @endauth
            <div class="relative">
              <button onclick="toggleNotif(event)" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 text-white/70 hover:text-purple-400 hover:bg-white/20 transition-all relative group z-10">
                <i class="fas fa-bell group-hover:animate-swing"></i>
                @if(isset($unreadCount) && $unreadCount > 0)
                  <span class="absolute top-2 right-2 w-2 h-2 bg-pink-500 border-2 border-[#1e1e2d] rounded-full animate-pulse"></span>
                @endif
              </button>
              
              <!-- Dropdown Notifikasi -->
              <div id="notif-dropdown" class="hidden absolute right-[-10px] sm:right-0 mt-3 w-[260px] sm:w-80 z-[1000] origin-top-right">
                
                <!-- Segitiga Atas -->
                <div class="absolute -top-2 right-[22px] sm:right-3.5 w-4 h-4 bg-slate-800 border-t border-l border-white/10 rotate-45 transform"></div>
                
                <!-- Inner Container -->
                <div class="relative z-50 overflow-hidden rounded-2xl w-full shadow-2xl bg-slate-800/80 backdrop-blur-xl border border-white/10">
                  <div class="px-4 py-3 border-b border-white/10 flex justify-between items-center bg-black/20">
                    <span class="text-sm font-semibold text-white">Notifications</span>
                    <span class="text-xs bg-white/10 text-white px-2 py-0.5 rounded-full font-medium shadow-sm border border-white/10">
                      {{ $unreadCount ?? 0 }} New
                    </span>
                  </div>
                  
                  <div class="p-2 space-y-1 max-h-[50vh] sm:max-h-[300px] overflow-y-auto">
                    @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                      @foreach($unreadNotifications as $notif)
                        <a href="{{ $notif->link ? route('notifications.read', $notif->id) : '#' }}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-white/5 cursor-pointer transition-colors w-full group">
                          <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-xs
                            @if($notif->type === 'pengumuman') bg-amber-500/20 text-amber-400
                            @elseif($notif->type === 'tugas') bg-blue-500/20 text-blue-400
                            @elseif($notif->type === 'materi') bg-emerald-500/20 text-emerald-400
                            @elseif($notif->type === 'nilai') bg-pink-500/20 text-pink-400
                            @elseif($notif->type === 'absensi') bg-purple-500/20 text-purple-400
                            @else bg-slate-500/20 text-slate-400
                            @endif">
                            @if($notif->type === 'pengumuman') <i class="fas fa-bullhorn"></i>
                            @elseif($notif->type === 'tugas') <i class="fas fa-file-alt"></i>
                            @elseif($notif->type === 'materi') <i class="fas fa-book-open"></i>
                            @elseif($notif->type === 'nilai') <i class="fas fa-star"></i>
                            @elseif($notif->type === 'absensi') <i class="fas fa-user-check"></i>
                            @else <i class="fas fa-bell"></i>
                            @endif
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="text-white text-xs font-bold truncate group-hover:text-purple-400 transition-colors">
                              {{ $notif->title }}
                            </p>
                            <p class="text-white/60 text-[10px] mt-0.5 truncate">
                              {{ $notif->message }}
                            </p>
                            <p class="text-white/40 text-[9px] mt-0.5">
                              {{ $notif->created_at->diffForHumans() }}
                            </p>
                          </div>
                          <div class="w-2 h-2 rounded-full bg-pink-500 mt-1.5 shrink-0 shadow-[0_0_8px_rgba(236,72,153,0.8)]"></div>
                        </a>
                      @endforeach
                    @else
                      <div class="p-6 text-center text-white/40 text-xs">
                        <i class="far fa-bell text-2xl mb-2 block text-white/10"></i>
                        Tidak ada notifikasi baru.
                      </div>
                    @endif
                  </div>
                  
                  <div class="p-2 border-t border-white/10 bg-black/20 flex gap-2 justify-between">
                    @if(isset($unreadCount) && $unreadCount > 0)
                      <a href="{{ route('notifications.read-all') }}" class="flex-1 py-1.5 text-[10px] text-center text-purple-400 hover:text-purple-300 hover:bg-white/5 transition-colors font-semibold rounded-lg">
                        Tandai Semua Dibaca
                      </a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="flex-1 py-1.5 text-[10px] text-center text-white/70 hover:text-white hover:bg-white/5 transition-colors font-semibold rounded-lg">
                      Lihat Semua
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Dashboard Content Wrapper -->
  <div class="flex-1 px-4 md:px-6 py-4">
    @yield('content')
  </div>

  <!-- Footer -->
  <footer class="mt-auto px-4 md:px-6 pt-6 pb-8 border-t border-white/10">
    <div class="flex flex-wrap justify-between items-center gap-4">
      <p class="text-white/30 text-sm w-full text-center sm:text-left sm:w-auto">Â© 2026 E-Learning MTs Syafiiyah. All rights reserved.</p>
      <div class="flex gap-4 w-full justify-center sm:w-auto">
        <span class="text-white/40 text-sm">Pembuat: <strong class="text-purple-400">Muh Noval Fuaddillah</strong></span>
      </div>
    </div>
  </footer>

</main>

<!-- ========== AI CHATBOT WIDGET ========== -->
<!-- Chatbot Floating Button -->
<div class="chatbot-floating-btn" id="chatbot-btn" onclick="toggleChatbot()" title="Tanya Asisten AI">
  <i class="fas fa-robot text-white text-xl"></i>
</div>

<!-- Floating Action Button (Settings) -->
<div class="floating-btn">
  <a href="/pengaturan" class="btn-floating"><i class="fas fa-cog"></i></a>
</div>

<!-- Chatbot Panel Container -->
<div id="chatbot-panel" class="fixed bottom-24 right-4 md:right-8 w-[380px] max-w-[calc(100vw-2rem)] h-[550px] max-h-[80vh] flex flex-col z-[999] rounded-2xl overflow-hidden border border-purple-500/20 bg-slate-900/95 backdrop-blur-xl shadow-2xl">
  
  <!-- Chat Header -->
  <div class="p-4 bg-gradient-to-r from-purple-900/90 to-indigo-900/90 border-b border-white/10 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 relative">
        <i class="fas fa-robot text-lg"></i>
        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
      </div>
      <div>
        <h5 class="text-white font-bold text-sm tracking-wide">Asisten AI Sekolah</h5>
        <span class="text-[10px] text-emerald-400 font-medium">Online â€¢ Siap membantu</span>
      </div>
    </div>
    <div class="flex items-center gap-1.5">
      <button onclick="clearChatHistory()" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white/60 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Hapus Riwayat Chat">
        <i class="fas fa-trash-alt text-xs"></i>
      </button>
      <button onclick="toggleChatbot()" class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/20 transition-all">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

  <!-- Chat Messages -->
  <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4 scrollbar-thin text-xs md:text-sm">
    <!-- AI Welcome Message -->
    <div class="flex items-start gap-2.5 max-w-[85%]">
      <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
        <i class="fas fa-robot text-xs"></i>
      </div>
      <div class="p-3 chat-bubble-ai rounded-2xl rounded-tl-none leading-relaxed text-slate-200">
        Halo <strong>{{ auth()->user()->name }}</strong>! ðŸ‘‹ Saya Asisten AI Pembelajaran Digital. Ada yang bisa saya bantu hari ini?
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div id="quick-actions" class="flex flex-col gap-2 pt-2 max-w-[85%] ml-10">
      <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Pilihan Cepat:</p>
      <button onclick="sendQuickMessage('Bagaimana format upload excel data siswa yang benar?')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
        ðŸ’¡ Format Excel error?
      </button>
      <button onclick="sendQuickMessage('Berikan daftar kode kelas yang valid di sistem')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
        ðŸ”‘ Daftar Kode Kelas Valid
      </button>
      <button onclick="sendQuickMessage('Jelaskan apa saja menu yang bisa saya akses sebagai {{ auth()->user()->role }}')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
        ðŸ“– Jelaskan menu dashboard saya
      </button>
    </div>
  </div>

  <!-- Chat Input -->
  <form id="chatbot-form" onsubmit="handleChatSubmit(event)" class="p-3 bg-black/20 border-t border-white/10 flex items-center gap-2">
    <input type="text" id="chat-input" placeholder="Tulis pesan Anda..." required autocomplete="off"
           class="flex-1 px-3.5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white text-xs md:text-sm placeholder:text-white/30 focus:border-purple-500 focus:outline-none transition-all">
    <button type="submit" id="chat-send-btn" class="w-9.5 h-9.5 shrink-0 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center text-white shadow-[0_0_10px_rgba(168,85,247,0.3)] hover:scale-105 transition-all">
      <i class="fas fa-paper-plane text-xs md:text-sm"></i>
    </button>
  </form>
</div>

<script>
  // Sidebar Logic
  function openSidenav() {
    document.getElementById('sidenav').classList.add('open');
    document.getElementById('overlay').classList.add('show');
  }
  
  function closeSidenav() {
    document.getElementById('sidenav').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
  }

  // Notifikasi Logic
  function toggleNotif(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('notif-dropdown');
    
    dropdown.classList.toggle('hidden');
    if (!dropdown.classList.contains('hidden')) {
      dropdown.classList.add('dropdown-enter');
    } else {
      dropdown.classList.remove('dropdown-enter');
    }
  }

  // Jadwal Sholat Logic
  function toggleSholatDropdown(event) {
    event.stopPropagation();
    
    // Tutup dropdown notif jika terbuka
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifDropdown && !notifDropdown.classList.contains('hidden')) {
      notifDropdown.classList.add('hidden');
      notifDropdown.classList.remove('dropdown-enter');
    }
    
    const dropdown = document.getElementById('sholat-dropdown');
    dropdown.classList.toggle('hidden');
    if (!dropdown.classList.contains('hidden')) {
      dropdown.classList.add('dropdown-enter');
      fetchJadwalSholat();
    } else {
      dropdown.classList.remove('dropdown-enter');
    }
  }
  
  let sholatDataCache = null;
  function fetchJadwalSholat() {
    const container = document.getElementById('sholat-times-container');
    if (sholatDataCache) {
      renderSholatTimes(sholatDataCache);
      return;
    }
    
    fetch('/api/jadwal-sholat?kabkota=Kab. Probolinggo')
      .then(response => response.json())
      .then(result => {
        if (result.success && result.data) {
          sholatDataCache = result.data;
          renderSholatTimes(result.data);
        } else {
          container.innerHTML = `<div class="text-center text-red-400 py-3"><i class="fas fa-exclamation-triangle mr-1.5"></i> Gagal memuat jadwal.</div>`;
        }
      })
      .catch(error => {
        container.innerHTML = `<div class="text-center text-red-400 py-3"><i class="fas fa-exclamation-triangle mr-1.5"></i> Error memuat jadwal.</div>`;
      });
  }
  
  function renderSholatTimes(data) {
    const container = document.getElementById('sholat-times-container');
    const dateInfo = document.getElementById('sholat-date-info');
    
    if (!data.jadwal || !Array.isArray(data.jadwal)) {
      container.innerHTML = `<div class="text-center text-red-400 py-3">Format data salah.</div>`;
      return;
    }
    
    const today = new Date();
    const dayOfMonth = today.getDate(); // 1-31
    const todayItem = data.jadwal[dayOfMonth - 1] || data.jadwal[0];
    
    if (!todayItem) {
      container.innerHTML = `<div class="text-center text-red-400 py-3">Jadwal hari ini tidak ditemukan.</div>`;
      return;
    }
    
    dateInfo.innerText = todayItem.tanggal + ' â€¢ Sumber: Bimas Islam';
    
    const times = [
      { name: 'Imsak', time: todayItem.imsak, icon: 'fa-hourglass-start' },
      { name: 'Subuh', time: todayItem.subuh, icon: 'fa-cloud-sun' },
      { name: 'Terbit', time: todayItem.terbit, icon: 'fa-sun' },
      { name: 'Dhuha', time: todayItem.dhuha, icon: 'fa-sun' },
      { name: 'Dzuhur', time: todayItem.dzuhur, icon: 'fa-sun' },
      { name: 'Ashar', time: todayItem.ashar, icon: 'fa-cloud-sun-rain' },
      { name: 'Maghrib', time: todayItem.maghrib, icon: 'fa-cloud-moon' },
      { name: 'Isya', time: todayItem.isya, icon: 'fa-moon' }
    ];
    
    let html = '<div class="grid grid-cols-2 gap-3">';
    times.forEach(t => {
      html += `
        <div class="flex items-center justify-between p-2 bg-white/5 rounded-xl border border-white/5 hover:bg-white/10 hover:border-purple-500/20 transition-all">
          <div class="flex items-center gap-2">
            <i class="fas ${t.icon} text-purple-400 text-[10px]"></i>
            <span class="font-medium text-slate-300">${t.name}</span>
          </div>
          <span class="font-bold font-mono text-white text-xs">${t.time}</span>
        </div>
      `;
    });
    html += '</div>';
    
    container.innerHTML = html;
  }

  // Tutup dropdown jika klik area di luarnya
  window.addEventListener('click', function(event) {
    // Tutup Notif
    const notifDropdown = document.getElementById('notif-dropdown');
    if (notifDropdown) {
      const isClickInsideNotif = notifDropdown.contains(event.target);
      const isClickOnNotifButton = event.target.closest('button[onclick="toggleNotif(event)"]');
      if (!isClickInsideNotif && !isClickOnNotifButton && !notifDropdown.classList.contains('hidden')) {
        notifDropdown.classList.add('hidden');
        notifDropdown.classList.remove('dropdown-enter');
      }
    }

    // Tutup Sholat
    const sholatDropdown = document.getElementById('sholat-dropdown');
    if (sholatDropdown) {
      const isClickInsideSholat = sholatDropdown.contains(event.target);
      const isClickOnSholatButton = event.target.closest('button[onclick="toggleSholatDropdown(event)"]');
      if (!isClickInsideSholat && !isClickOnSholatButton && !sholatDropdown.classList.contains('hidden')) {
        sholatDropdown.classList.add('hidden');
        sholatDropdown.classList.remove('dropdown-enter');
      }
    }
  });

  // Theme Switching Logic
  function applyTheme(theme) {
    const icon = document.getElementById('theme-toggle-icon');
    if (theme === 'light') {
      document.body.classList.add('light-theme');
      if (icon) {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
      }
    } else {
      document.body.classList.remove('light-theme');
      if (icon) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
      }
    }
  }

  function toggleTheme() {
    let currentTheme = localStorage.getItem('theme') || 'dark';
    let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('theme', newTheme);
    applyTheme(newTheme);
    
    // Dispatch custom event to notify charts to redraw
    window.dispatchEvent(new Event('theme-changed'));
  }

  // Load theme immediately
  (function() {
    let savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);
  })();

  // Global SweetAlert Delete Confirmation
  function confirmSweetDelete(button, type = 'data') {
    const form = button.closest('form');
    if (!form) {
      console.error('Form hapus tidak ditemukan');
      return;
    }

    const theme = localStorage.getItem('theme') || 'dark';
    const bg = theme === 'light' ? '#ffffff' : '#1a1a2e';
    const color = theme === 'light' ? '#1e293b' : '#ffffff';

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus ${type} ini. Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ec4899',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        background: bg,
        color: color
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
  }

  // Session notifications
  @if(session('success'))
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: "{{ session('success') }}",
          timer: 3000,
          showConfirmButton: false,
          background: (localStorage.getItem('theme') || 'dark') === 'light' ? '#ffffff' : '#1a1a2e',
          color: (localStorage.getItem('theme') || 'dark') === 'light' ? '#1e293b' : '#ffffff'
      });
  @endif

  @if(session('error'))
      Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: "{{ session('error') }}",
          background: (localStorage.getItem('theme') || 'dark') === 'light' ? '#ffffff' : '#1a1a2e',
          color: (localStorage.getItem('theme') || 'dark') === 'light' ? '#1e293b' : '#ffffff'
      });
  @endif

  // ==================== AI CHATBOT JAVASCRIPT ====================
  const chatbotPanel = document.getElementById('chatbot-panel');
  const chatMessages = document.getElementById('chat-messages');
  const chatInput = document.getElementById('chat-input');
  const chatSendBtn = document.getElementById('chat-send-btn');
  const chatbotForm = document.getElementById('chatbot-form');

  function toggleChatbot() {
    if (!chatbotPanel) return;
    chatbotPanel.classList.toggle('open');
    if (chatbotPanel.classList.contains('open')) {
      chatInput.focus();
      // Scroll to bottom
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
  }

  function parseSimpleMarkdown(text) {
    // Sanitasi HTML sederhana untuk keamanan
    let escaped = text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

    // **bold** -> <strong>bold</strong>
    escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    
    // *italic* -> <em>italic</em>
    escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
    
    // Bullet list - item -> <li>item</li>
    escaped = escaped.replace(/^\s*-\s+(.*?)$/gm, '<li>$1</li>');
    
    // Bungkus <li> berurutan ke dalam <ul>
    escaped = escaped.replace(/(<li>.*?<\/li>)/gs, '<ul class="list-disc pl-4 space-y-0.5 my-1.5">$1</ul>');
    escaped = escaped.replace(/<\/ul>\s*<ul class="list-disc pl-4 space-y-0.5 my-1.5">/g, '');
    
    // Baris baru -> <br>
    escaped = escaped.replace(/\n/g, '<br>');
    
    return escaped;
  }

  function appendMessage(role, text) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex items-start gap-2.5 max-w-[85%] ${role === 'user' ? 'ml-auto justify-end' : ''}`;
    
    const formattedText = parseSimpleMarkdown(text);

    if (role === 'user') {
      messageDiv.innerHTML = `
        <div class="p-3 chat-bubble-user rounded-2xl rounded-tr-none leading-relaxed shadow-md">
          ${formattedText}
        </div>
      `;
    } else {
      messageDiv.innerHTML = `
        <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
          <i class="fas fa-robot text-xs"></i>
        </div>
        <div class="p-3 chat-bubble-ai rounded-2xl rounded-tl-none leading-relaxed shadow-sm">
          ${formattedText}
        </div>
      `;
    }

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function showTypingIndicator() {
    const indicatorDiv = document.createElement('div');
    indicatorDiv.id = 'typing-indicator';
    indicatorDiv.className = 'flex items-start gap-2.5 max-w-[85%]';
    indicatorDiv.innerHTML = `
      <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
        <i class="fas fa-robot text-xs"></i>
      </div>
      <div class="p-3 chat-bubble-ai rounded-2xl rounded-tl-none flex items-center gap-1">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>
    `;
    chatMessages.appendChild(indicatorDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function removeTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    if (indicator) indicator.remove();
  }

  async function handleChatSubmit(event) {
    if (event) event.preventDefault();
    
    const message = chatInput.value.trim();
    if (!message) return;

    // Bersihkan input
    chatInput.value = '';
    
    // Sembunyikan quick actions jika masih ada
    const quickActions = document.getElementById('quick-actions');
    if (quickActions) quickActions.remove();

    // Tambah pesan user ke panel
    appendMessage('user', message);

    // Tampilkan loading & disable inputs
    showTypingIndicator();
    chatInput.disabled = true;
    chatSendBtn.disabled = true;

    try {
      const response = await fetch("{{ route('chatbot.chat') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: message })
      });

      const result = await response.json();
      removeTypingIndicator();

      if (result.success) {
        appendMessage('model', result.message);
      } else {
        appendMessage('model', `âš ï¸ **Kesalahan:** ${result.message || 'Gagal memproses pesan.'}`);
      }
    } catch (error) {
      removeTypingIndicator();
      appendMessage('model', 'âš ï¸ **Koneksi Gagal:** Tidak dapat terhubung ke server asisten AI.');
      console.error(error);
    } finally {
      chatInput.disabled = false;
      chatSendBtn.disabled = false;
      chatInput.focus();
    }
  }

  function sendQuickMessage(text) {
    chatInput.value = text;
    handleChatSubmit();
  }

  async function clearChatHistory() {
    const theme = localStorage.getItem('theme') || 'dark';
    const bg = theme === 'light' ? '#ffffff' : '#1a1a2e';
    const color = theme === 'light' ? '#1e293b' : '#ffffff';

    const confirm = await Swal.fire({
      title: 'Hapus Riwayat Chat?',
      text: "Anda akan mengosongkan riwayat obrolan dengan asisten AI ini.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#a855f7',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      background: bg,
      color: color
    });

    if (confirm.isConfirmed) {
      try {
        const response = await fetch("{{ route('chatbot.clear') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const result = await response.json();
        
        if (result.success) {
          // Bersihkan UI chat, kembalikan ke welcome message
          chatMessages.innerHTML = `
            <div class="flex items-start gap-2.5 max-w-[85%]">
              <div class="w-8 h-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
                <i class="fas fa-robot text-xs"></i>
              </div>
              <div class="p-3 chat-bubble-ai rounded-2xl rounded-tl-none leading-relaxed text-slate-200">
                Halo <strong>{{ auth()->user()->name }}</strong>! ðŸ‘‹ Saya Asisten AI Pembelajaran Digital. Ada yang bisa saya bantu hari ini?
              </div>
            </div>
            
            <div id="quick-actions" class="flex flex-col gap-2 pt-2 max-w-[85%] ml-10">
              <p class="text-[10px] text-white/40 uppercase tracking-wider font-semibold mb-1">Pilihan Cepat:</p>
              <button onclick="sendQuickMessage('Bagaimana format upload excel data siswa yang benar?')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
                ðŸ’¡ Format Excel error?
              </button>
              <button onclick="sendQuickMessage('Berikan daftar kode kelas yang valid di sistem')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
                ðŸ”‘ Daftar Kode Kelas Valid
              </button>
              <button onclick="sendQuickMessage('Jelaskan apa saja menu yang bisa saya akses sebagai {{ auth()->user()->role }}')" class="text-left px-3 py-2 bg-white/5 border border-white/10 hover:border-purple-500/30 hover:bg-purple-500/10 text-xs text-purple-300 font-medium rounded-xl transition-all">
                ðŸ“– Jelaskan menu dashboard saya
              </button>
            </div>
          `;
          
          Swal.fire({
            icon: 'success',
            title: 'Riwayat Dihapus',
            text: 'Riwayat obrolan Anda telah dibersihkan.',
            timer: 2000,
            showConfirmButton: false,
            background: bg,
            color: color
          });
        }
      } catch (error) {
        console.error(error);
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Gagal menghapus riwayat obrolan.',
          background: bg,
          color: color
        });
      }
    }
  }
</script>

</body>
</html>
