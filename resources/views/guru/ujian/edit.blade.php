@extends('layouts.app')

@section('title', 'Edit Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Edit Ujian')
@section('page-title', 'Edit Informasi Ujian')

<style>
    select option {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6 text-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
        </a>

        <div class="luxury-card">
            <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
                <h6 class="text-white font-semibold text-lg">Form Edit Ujian</h6>
                <p class="text-white/40 text-sm">Perbarui detail informasi ujian.</p>
            </div>

            <form action="{{ route('guru.ujian.update', $ujian->id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Judul Ujian</label>
                    <input type="text" name="judul" value="{{ old('judul', $ujian->judul) }}" required
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                    @error('judul') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Deskripsi / Petunjuk Ujian</label>
                    <textarea name="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan ujian..."
                              class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                    @error('deskripsi') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Mata Pelajaran</label>
                        <select name="mapel_id" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ old('mapel_id', $ujian->mapel_id) == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                        @error('mapel_id') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Kelas</label>
                        <select name="kelas_id" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id', $ujian->kelas_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Waktu Mulai Ujian</label>
                        <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai', $ujian->waktu_mulai->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        @error('waktu_mulai') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Waktu Selesai Ujian</label>
                        <input type="datetime-local" name="waktu_selesai" value="{{ old('waktu_selesai', $ujian->waktu_selesai->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        @error('waktu_selesai') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Durasi (Menit)</label>
                        <input type="number" name="durasi" value="{{ old('durasi', $ujian->durasi) }}" min="1" required
                               class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        @error('durasi') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Status Penerbitan</label>
                    <select name="status" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                        <option value="draft" {{ old('status', $ujian->status) == 'draft' ? 'selected' : '' }}>Draf (Disimpan, belum bisa dilihat siswa)</option>
                        <option value="published" {{ old('status', $ujian->status) == 'published' ? 'selected' : '' }}>Publikasikan (Bisa dikerjakan sesuai jadwal)</option>
                        <option value="closed" {{ old('status', $ujian->status) == 'closed' ? 'selected' : '' }}>Ditutup (Tidak dapat dikerjakan lagi)</option>
                    </select>
                    @error('status') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-white/10 flex justify-end gap-3">
                    <a href="{{ route('guru.ujian.index') }}" class="px-5 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow transition">
                        Perbarui Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
