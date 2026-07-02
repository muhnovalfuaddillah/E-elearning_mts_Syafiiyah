@extends('layouts.app')

@section('title', 'Kalender Akademik - Pembelajaran Digital')
@section('breadcrumb', 'Kalender')
@section('page-title', 'Kalender Akademik Sekolah')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-400 flex items-center justify-between">
        <div class="text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah Agenda (Kiri) -->
        <div class="lg:col-span-1">
            <div class="luxury-card p-6">
                <h5 class="text-white font-bold text-lg mb-4 pb-2 border-b border-white/10"><i class="fas fa-calendar-plus text-blue-400"></i> Tambah Agenda Akademik</h5>
                
                <form action="{{ route('admin.calendar.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Judul Agenda</label>
                            <input type="text" name="title" required placeholder="Contoh: UTS Semester Ganjil"
                                   class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tipe Kegiatan</label>
                            <select name="type" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                                <option value="umum" class="text-black bg-white">Kegiatan Umum (Ungu)</option>
                                <option value="libur" class="text-black bg-white">Libur Sekolah (Merah)</option>
                                <option value="ujian" class="text-black bg-white">Jadwal Ujian (Kuning)</option>
                                <option value="kegiatan" class="text-black bg-white">Kegiatan Siswa (Biru)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Mulai</label>
                                <input type="date" name="start_date" required
                                       class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Selesai (Opsional)</label>
                                <input type="date" name="end_date"
                                       class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Keterangan Singkat</label>
                            <textarea name="description" rows="3" placeholder="Tambahkan informasi rinci mengenai agenda..."
                                      class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow flex items-center justify-center gap-1.5">
                            <i class="fas fa-save"></i> Simpan Agenda
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Agenda Terdaftar (Kanan) -->
        <div class="lg:col-span-2">
            <div class="luxury-card overflow-hidden">
                <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
                    <h6 class="text-white font-semibold text-lg">Agenda Terdaftar</h6>
                    <p class="text-white/40 text-xs md:text-sm">Menampilkan seluruh agenda akademik dan kalender pendidikan sekolah.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[600px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Agenda</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Tipe</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-48">Tanggal</th>
                                <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $index => $item)
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $index + 1 }}</td>
                                    <td class="p-3 md:p-4">
                                        <span class="text-white font-bold text-sm block">{{ $item->title }}</span>
                                        @if($item->description)
                                            <span class="text-xs text-white/50 block mt-1 leading-snug">{{ $item->description }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4">
                                        @php
                                            $types = [
                                                'libur' => 'bg-red-500/20 text-red-400 border border-red-500/30',
                                                'ujian' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
                                                'kegiatan' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                                'umum' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                                            ];
                                            $class = $types[$item->type] ?? 'bg-blue-500/20 text-blue-400';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $class }}">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td class="p-3 md:p-4 text-white/80 text-xs font-medium">
                                        @if($item->end_date && $item->end_date !== $item->start_date)
                                            {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4 text-center">
                                        <form action="{{ route('admin.calendar.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs flex items-center justify-center gap-1 hover:underline w-full font-bold">
                                                <i class="fas fa-trash-alt text-sm"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-white/40">
                                        <i class="fas fa-calendar-times text-5xl mb-3 text-white/10"></i>
                                        <p>Belum ada agenda akademik terdaftar. Silakan gunakan form di samping untuk menambahkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
