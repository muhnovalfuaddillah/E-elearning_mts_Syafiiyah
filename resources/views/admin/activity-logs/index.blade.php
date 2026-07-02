@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem - Pembelajaran Digital')
@section('breadcrumb', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas Sistem')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Search Section -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari aktivitas, deskripsi, user, atau IP..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Logs -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <div>
                <h6 class="text-white font-semibold text-lg">Riwayat Aktivitas</h6>
                <p class="text-white/40 text-xs md:text-sm">Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[700px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-40">Pengguna</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Aktivitas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Deskripsi</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-32">IP Address</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-40">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $item)
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-3 md:p-4 text-white/80 text-sm">{{ $logs->firstItem() + $index }}</td>
                            <td class="p-3 md:p-4">
                                <span class="text-white font-medium text-sm block">{{ $item->user->name ?? 'System' }}</span>
                                <span class="text-[10px] text-white/40 block mt-0.5">{{ $item->user->email ?? 'system@sch.id' }}</span>
                            </td>
                            <td class="p-3 md:p-4">
                                @php
                                    $actionColors = [
                                        'login' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                        'logout' => 'bg-slate-500/20 text-slate-400 border border-slate-500/30',
                                        'backup_database' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                        'create_calendar_event' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                        'absen_qr' => 'bg-teal-500/20 text-teal-400 border border-teal-500/30'
                                    ];
                                    $class = $actionColors[$item->action] ?? 'bg-teal-500/20 text-teal-400 border border-teal-500/30';
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $class }}">
                                    {{ str_replace('_', ' ', $item->action) }}
                                </span>
                            </td>
                            <td class="p-3 md:p-4 text-white/80 text-sm leading-normal">{{ $item->description }}</td>
                            <td class="p-3 md:p-4">
                                <code class="px-1.5 py-0.5 rounded text-xs bg-white/5 text-emerald-300 font-mono">
                                    {{ $item->ip_address ?? '127.0.0.1' }}
                                </code>
                            </td>
                            <td class="p-3 md:p-4 text-white/70 text-xs">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }} WIB
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-white/40">
                                <i class="fas fa-history text-5xl mb-3 text-white/10"></i>
                                <p>Tidak ada riwayat aktivitas log sistem yang cocok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
                <p class="text-white/40 text-xs">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                </p>
                <div class="flex gap-2">
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<script>
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchForm').submit();
    }
</script>
@endsection
