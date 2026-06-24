@extends('layouts.app')

@section('title', 'Jadwal Mengajar - MTs Syafiiyah')
@section('breadcrumb', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Header info -->
    <div class="luxury-card p-6 mb-8 flex items-center gap-4">
        <div class="luxury-icon w-12 h-12">
            <i class="fas fa-calendar-alt text-white text-xl"></i>
        </div>
        <div>
            <h4 class="text-white font-bold text-lg">Jadwal Mengajar Mingguan</h4>
            <p class="text-white/50 text-sm">Jadwal mengampu kelas dan mata pelajaran Anda.</p>
        </div>
    </div>

    <!-- Weekly Grid (Dikelompokkan per hari) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($jadwalGrouped as $hari => $listJadwal)
            <div class="luxury-card flex flex-col justify-between overflow-hidden">
                <!-- Header Hari -->
                <div class="p-4 bg-white/5 border-b border-white/10 flex justify-between items-center">
                    <span class="text-white font-bold text-base flex items-center gap-2">
                        <i class="far fa-calendar-check text-purple-400"></i> {{ $hari }}
                    </span>
                    <span class="text-xs bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full font-semibold">
                        {{ $listJadwal->count() }} Sesi
                    </span>
                </div>

                <!-- List Sesi Pelajaran -->
                <div class="p-4 divide-y divide-white/5 flex-1">
                    @forelse($listJadwal as $session)
                        <div class="py-3.5 first:pt-1 last:pb-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                            <div class="flex items-start gap-3">
                                <!-- Clock Icon & Time -->
                                <div class="w-20 shrink-0 font-mono text-purple-400 font-bold text-sm bg-purple-500/10 px-2.5 py-1 rounded-lg border border-purple-500/20 text-center">
                                    {{ \Carbon\Carbon::parse($session->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->jam_selesai)->format('H:i') }}
                                </div>
                                <!-- Subject and Class -->
                                <div>
                                    <h6 class="text-white font-bold text-sm sm:text-base group-hover:text-purple-300 transition-colors">
                                        {{ $session->mapel->nama_mapel }}
                                    </h6>
                                    <p class="text-white/50 text-xs mt-0.5">
                                        Kode: {{ $session->mapel->kode_mapel }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Class & Room details -->
                            <div class="flex items-center gap-2 self-start sm:self-auto sm:text-right">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    <i class="fas fa-school mr-1"></i> {{ $session->kelas->kode_kelas }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                                    <i class="fas fa-door-open mr-1"></i> {{ $session->ruangan ?? 'Kelas' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-white/30 text-xs">
                            <i class="fas fa-calendar-day text-xl mb-1 text-white/10"></i>
                            Tidak ada jadwal mengajar pada hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

