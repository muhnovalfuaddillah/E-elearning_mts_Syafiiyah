@extends('layouts.app')

@section('title', 'Jadwal Pelajaran - MTs Syafiiyah')
@section('breadcrumb', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Header info -->
    <div class="luxury-card p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="luxury-icon w-12 h-12">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <h4 class="text-white font-bold text-lg">Jadwal Pelajaran Kelas</h4>
                <p class="text-white/50 text-sm">Jadwal pelajaran mingguan kelas Anda.</p>
            </div>
        </div>
        <div class="px-4 py-2 bg-purple-500/10 border border-purple-500/20 rounded-xl text-purple-400 text-sm font-semibold shrink-0">
            Kelas: {{ $siswa->kelas->kode_kelas }}
        </div>
    </div>

    <!-- Weekly Grid (Dikelompokkan per hari) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($jadwalGrouped as $hari => $listJadwal)
            <div class="luxury-card flex flex-col justify-between overflow-hidden">
                <!-- Header Hari -->
                <div class="p-4 bg-white/5 border-b border-white/10 flex justify-between items-center">
                    <span class="text-white font-bold text-base flex items-center gap-2">
                        <i class="far fa-calendar-check text-indigo-400"></i> {{ $hari }}
                    </span>
                    <span class="text-xs bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full font-semibold">
                        {{ $listJadwal->count() }} Sesi
                    </span>
                </div>

                <!-- List Sesi Pelajaran -->
                <div class="p-4 divide-y divide-white/5 flex-1">
                    @forelse($listJadwal as $session)
                        <div class="py-3.5 first:pt-1 last:pb-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                            <div class="flex items-start gap-3">
                                <!-- Clock Icon & Time -->
                                <div class="w-20 shrink-0 font-mono text-indigo-400 font-bold text-sm bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20 text-center">
                                    {{ \Carbon\Carbon::parse($session->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->jam_selesai)->format('H:i') }}
                                </div>
                                <!-- Subject and Guru -->
                                <div>
                                    <h6 class="text-white font-bold text-sm sm:text-base group-hover:text-indigo-300 transition-colors">
                                        {{ $session->mapel->nama_mapel }}
                                    </h6>
                                    <p class="text-white/50 text-xs mt-0.5">
                                        Guru: {{ $session->mapel->guru ? $session->mapel->guru->name : '-' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Room details -->
                            <div class="flex items-center gap-2 self-start sm:self-auto sm:text-right">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                                    <i class="fas fa-door-open mr-1"></i> {{ $session->ruangan ?? 'Kelas Reguler' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-white/30 text-xs">
                            <i class="fas fa-calendar-day text-xl mb-1 text-white/10"></i>
                            Tidak ada pelajaran pada hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

