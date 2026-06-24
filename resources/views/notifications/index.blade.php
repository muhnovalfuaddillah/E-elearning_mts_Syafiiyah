@extends('layouts.app')

@section('title', 'Notifikasi - MTs Syafiiyah')
@section('breadcrumb', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="w-full px-4 md:px-6 py-6 flex justify-center">
    <div class="w-full max-w-4xl">

        <!-- Header Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h4 class="text-white font-bold text-xl">Semua Notifikasi</h4>
                <p class="text-white/50 text-sm">Lihat seluruh riwayat aktivitas dan informasi masuk untuk Anda.</p>
            </div>
            
            @if(auth()->user()->unreadAppNotifications()->count() > 0)
                <a href="{{ route('notifications.read-all') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-500/20 border border-purple-500/30 rounded-xl text-purple-400 text-sm font-semibold hover:bg-purple-500/35 transition-colors">
                    <i class="fas fa-check-double mr-1.5"></i> Tandai Semua Dibaca
                </a>
            @endif
        </div>

        <!-- Alert messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 flex items-center justify-between">
            <div class="text-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <!-- Notifications Container -->
        <div class="luxury-card overflow-hidden">
            <div class="p-2 divide-y divide-white/5">
                @forelse($notifications as $notif)
                    <div class="flex items-start gap-4 p-4 hover:bg-white/5 transition-all duration-200 relative group {{ !$notif->is_read ? 'bg-purple-500/5' : '' }}">
                        
                        <!-- Notification Type Icon -->
                        <div class="w-10 h-10 shrink-0 rounded-xl flex items-center justify-center text-sm shadow-inner
                            @if($notif->type === 'pengumuman') bg-amber-500/20 text-amber-400 border border-amber-500/30
                            @elseif($notif->type === 'tugas') bg-blue-500/20 text-blue-400 border border-blue-500/30
                            @elseif($notif->type === 'materi') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                            @elseif($notif->type === 'nilai') bg-pink-500/20 text-pink-400 border border-pink-500/30
                            @elseif($notif->type === 'absensi') bg-purple-500/20 text-purple-400 border border-purple-500/30
                            @else bg-slate-500/20 text-slate-400 border border-slate-500/30
                            @endif">
                            @if($notif->type === 'pengumuman') <i class="fas fa-bullhorn"></i>
                            @elseif($notif->type === 'tugas') <i class="fas fa-file-alt"></i>
                            @elseif($notif->type === 'materi') <i class="fas fa-book-open"></i>
                            @elseif($notif->type === 'nilai') <i class="fas fa-star"></i>
                            @elseif($notif->type === 'absensi') <i class="fas fa-user-check"></i>
                            @else <i class="fas fa-bell"></i>
                            @endif
                        </div>

                        <!-- Content details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-white/40 text-xs font-semibold uppercase tracking-wider">
                                    {{ $notif->type }}
                                </span>
                                <span class="text-white/30 text-xs whitespace-nowrap">
                                    {{ $notif->created_at->format('d M Y - H:i') }}
                                </span>
                            </div>

                            <h5 class="text-white font-bold text-sm sm:text-base mt-1 line-clamp-1 group-hover:text-purple-400 transition-colors">
                                {{ $notif->title }}
                            </h5>
                            
                            <p class="text-white/60 text-xs sm:text-sm mt-0.5 leading-relaxed">
                                {{ $notif->message }}
                            </p>

                            <!-- Read more link / Redirect -->
                            @if($notif->link)
                                <a href="{{ route('notifications.read', $notif->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors mt-2">
                                    Lihat Detail <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @elseif(!$notif->is_read)
                                <a href="{{ route('notifications.read', $notif->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors mt-2">
                                    Tandai Dibaca <i class="fas fa-check text-[10px]"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Unread status dot -->
                        @if(!$notif->is_read)
                            <div class="w-2.5 h-2.5 rounded-full bg-pink-500 shadow-[0_0_8px_rgba(236,72,153,0.8)] mt-1.5 shrink-0 self-center sm:self-auto sm:mt-5" title="Belum dibaca"></div>
                        @endif

                    </div>
                @empty
                    <div class="p-10 text-center text-white/30">
                        <i class="far fa-bell text-5xl mb-4 block text-white/10"></i>
                        <h5 class="text-white font-semibold text-base mb-0.5">Kotak Masuk Kosong</h5>
                        <p class="text-white/40 text-xs">Anda belum menerima notifikasi apa pun.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination block -->
            @if($notifications->hasPages())
                <div class="p-4 bg-black/10 border-t border-white/5">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

