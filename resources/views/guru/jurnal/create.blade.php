@extends('layouts.app')

@section('title', 'Buat Jurnal Mengajar - Pembelajaran Digital')
@section('breadcrumb', 'Buat Jurnal')
@section('page-title', 'Buat Jurnal Mengajar')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <div class="max-w-2xl mx-auto">
        
        <!-- Back Link -->
        <a href="{{ route('guru.jurnal.index') }}" class="inline-flex items-center text-blue-400 hover:text-emerald-300 text-sm font-semibold mb-6 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Jurnal
        </a>

        <!-- Form Card -->
        <div class="luxury-card overflow-hidden">
            <div class="p-6 border-b border-white/10 bg-white/5">
                <h5 class="text-white font-bold text-lg"><i class="fas fa-file-signature text-blue-400 mr-2"></i> Form Jurnal Mengajar</h5>
                <p class="text-white/40 text-xs md:text-sm mt-1">Lengkapi informasi pelaksanaan mengajar Anda secara berkala.</p>
            </div>

            <form action="{{ route('guru.jurnal.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <!-- Kelas & Mapel Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select name="mapel_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id')
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal & Pertemuan Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Tanggal Mengajar <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                        @error('tanggal')
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Pertemuan Ke- <span class="text-red-500">*</span></label>
                        <input type="number" min="1" name="pertemuan_ke" value="{{ old('pertemuan_ke') }}" placeholder="Contoh: 1, 2, 3..." required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm font-semibold">
                        @error('pertemuan_ke')
                            <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Materi Pokok -->
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Materi Pokok / Bahasan <span class="text-red-500">*</span></label>
                    <textarea name="materi" rows="3" required placeholder="Tuliskan topik utama atau materi pelajaran yang dibahas..." class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm leading-relaxed">{{ old('materi') }}</textarea>
                    @error('materi')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kegiatan Pembelajaran -->
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Kegiatan Pembelajaran <span class="text-red-500">*</span></label>
                    <textarea name="kegiatan" rows="4" required placeholder="Jelaskan secara ringkas aktivitas kelas (diskusi, demonstrasi, praktek kelompok, ceramah, dsb)..." class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm leading-relaxed">{{ old('kegiatan') }}</textarea>
                    @error('kegiatan')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Khusus -->
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Catatan / Evaluasi Pembelajaran <span class="text-white/40">(Opsional)</span></label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan hambatan belajar, siswa yang tidak hadir, saran, atau hasil evaluasi mengajar..." class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm leading-relaxed">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Button -->
                <div class="pt-2 flex justify-end gap-3">
                    <a href="{{ route('guru.jurnal.index') }}" class="px-5 py-2.5 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 text-white font-semibold text-sm transition-all">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-bold text-sm shadow-glow flex items-center gap-1.5">
                        <i class="fas fa-save"></i> Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
