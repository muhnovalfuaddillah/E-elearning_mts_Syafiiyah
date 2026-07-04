@extends('layouts.app')

@section('title', 'Tahun Akademik - Pembelajaran Digital')
@section('breadcrumb', 'Tahun Akademik')
@section('page-title', 'Tahun Akademik')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1">
            <div class="luxury-card p-6">
                <h5 class="text-white font-bold text-lg mb-4 pb-2 border-b border-white/10">
                    <i class="fas fa-calendar-plus text-blue-400 mr-2"></i>
                    {{ isset($editTahunAkademik) ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik' }}
                </h5>

                <form action="{{ isset($editTahunAkademik) ? route('admin.tahun-akademik.update', $editTahunAkademik->id) : route('admin.tahun-akademik.store') }}" method="POST">
                    @csrf
                    @if(isset($editTahunAkademik))
                        @method('PUT')
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Nama Tahun Akademik</label>
                            <input type="text" name="nama_tahun" value="{{ old('nama_tahun', isset($editTahunAkademik) && $editTahunAkademik ? $editTahunAkademik->nama_tahun : '') }}" required placeholder="Contoh: 2026/2027" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Semester</label>
                            <select name="semester" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                                <option value="Ganjil" class="text-black bg-white" {{ old('semester', isset($editTahunAkademik) && $editTahunAkademik ? $editTahunAkademik->semester : '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" class="text-black bg-white" {{ old('semester', isset($editTahunAkademik) && $editTahunAkademik ? $editTahunAkademik->semester : '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($editTahunAkademik) && $editTahunAkademik && $editTahunAkademik->tanggal_mulai ? $editTahunAkademik->tanggal_mulai->format('Y-m-d') : '') }}" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', isset($editTahunAkademik) && $editTahunAkademik && $editTahunAkademik->tanggal_selesai ? $editTahunAkademik->tanggal_selesai->format('Y-m-d') : '') }}" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" name="status_aktif" value="1" {{ old('status_aktif', isset($editTahunAkademik) && $editTahunAkademik ? $editTahunAkademik->status_aktif : false) ? 'checked' : '' }} class="rounded border-white/20 bg-white/10 text-blue-500 focus:ring-blue-500">
                            <label class="text-white/70 text-sm">Jadikan sebagai tahun akademik aktif</label>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow flex items-center justify-center gap-1.5">
                            <i class="fas {{ isset($editTahunAkademik) ? 'fa-save' : 'fa-plus' }}"></i>
                            {{ isset($editTahunAkademik) ? 'Perbarui' : 'Simpan' }}
                        </button>
                        @if(isset($editTahunAkademik))
                            <a href="{{ route('admin.tahun-akademik.index') }}" class="px-4 py-2.5 rounded-lg border border-white/10 text-white/70 text-sm hover:text-white hover:bg-white/10 flex items-center justify-center">
                                Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="luxury-card overflow-hidden">
                <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
                    <h6 class="text-white font-semibold text-lg">Daftar Tahun Akademik</h6>
                    <p class="text-white/40 text-xs md:text-sm">Data ini bisa dipakai kembali untuk periode tahun pelajaran berikutnya.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[700px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tahun Akademik</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Semester</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Periode</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAkademik as $index => $item)
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $tahunAkademik->firstItem() + $index }}</td>
                                    <td class="p-3 md:p-4 text-white font-semibold">{{ $item->nama_tahun }}</td>
                                    <td class="p-3 md:p-4 text-white/80">{{ $item->semester }}</td>
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->tanggal_mulai->format('d M Y') }} s/d {{ $item->tanggal_selesai->format('d M Y') }}</td>
                                    <td class="p-3 md:p-4">
                                        @if($item->status_aktif)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-semibold">Aktif</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-white/10 text-white/60 border border-white/10 text-xs font-semibold">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.tahun-akademik.edit', $item->id) }}" class="text-blue-400 hover:text-blue-300 text-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.tahun-akademik.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tahun akademik ini?')" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-white/40">
                                        <i class="fas fa-calendar-times text-5xl mb-3 text-white/10"></i>
                                        <p>Belum ada data tahun akademik. Silakan tambahkan data pertama.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tahunAkademik->hasPages())
                <div class="p-4 border-t border-white/10">
                    {{ $tahunAkademik->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
