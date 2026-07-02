@extends('layouts.app')

@section('title', 'Kelola Soal Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Kelola Soal')
@section('page-title', 'Kelola Soal Ujian')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <!-- Back and Title Info -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
            </a>
            <h4 class="text-white font-bold text-xl">{{ $ujian->judul }}</h4>
            <p class="text-white/40 text-xs md:text-sm mt-1">
                Mapel: <span class="text-white/80 font-semibold">{{ $ujian->mapel->nama_mapel }}</span> | 
                Kelas: <span class="text-blue-400 font-semibold">{{ $ujian->kelas->kode_kelas }}</span> | 
                Total: <span class="text-purple-400 font-semibold">{{ $ujian->soals->count() }} Soal</span>
            </p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 rounded-lg text-white font-bold text-sm flex items-center gap-2 shadow-glow">
                <i class="fas fa-plus"></i> Tambah Soal Pilihan Ganda
            </button>
        </div>
    </div>

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

    <!-- Questions List -->
    <div class="space-y-6">
        @forelse($ujian->soals as $index => $soal)
            <div class="luxury-card p-5 relative border border-white/5 hover:border-white/10 transition-all">
                <div class="flex justify-between items-start gap-4">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-grow">
                        <!-- Pertanyaan -->
                        <div class="text-white text-sm leading-relaxed mb-4 whitespace-pre-line">{!! e($soal->pertanyaan) !!}</div>

                        @if($soal->gambar)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar Soal" class="max-h-48 rounded-lg border border-white/10">
                            </div>
                        @endif

                        <!-- Pilihan Jawaban -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-2">
                            <div class="p-2.5 rounded-lg border text-xs flex gap-2 {{ $soal->kunci_jawaban == 'A' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 font-semibold' : 'bg-white/5 border-white/5 text-white/70' }}">
                                <span class="font-bold">A.</span> <span>{{ $soal->opsi_a }}</span>
                                @if($soal->kunci_jawaban == 'A') <i class="fas fa-check-circle ml-auto my-auto text-emerald-400"></i> @endif
                            </div>
                            <div class="p-2.5 rounded-lg border text-xs flex gap-2 {{ $soal->kunci_jawaban == 'B' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 font-semibold' : 'bg-white/5 border-white/5 text-white/70' }}">
                                <span class="font-bold">B.</span> <span>{{ $soal->opsi_b }}</span>
                                @if($soal->kunci_jawaban == 'B') <i class="fas fa-check-circle ml-auto my-auto text-emerald-400"></i> @endif
                            </div>
                            <div class="p-2.5 rounded-lg border text-xs flex gap-2 {{ $soal->kunci_jawaban == 'C' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 font-semibold' : 'bg-white/5 border-white/5 text-white/70' }}">
                                <span class="font-bold">C.</span> <span>{{ $soal->opsi_c }}</span>
                                @if($soal->kunci_jawaban == 'C') <i class="fas fa-check-circle ml-auto my-auto text-emerald-400"></i> @endif
                            </div>
                            <div class="p-2.5 rounded-lg border text-xs flex gap-2 {{ $soal->kunci_jawaban == 'D' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 font-semibold' : 'bg-white/5 border-white/5 text-white/70' }}">
                                <span class="font-bold">D.</span> <span>{{ $soal->opsi_d }}</span>
                                @if($soal->kunci_jawaban == 'D') <i class="fas fa-check-circle ml-auto my-auto text-emerald-400"></i> @endif
                            </div>
                            @if($soal->opsi_e)
                            <div class="p-2.5 rounded-lg border text-xs flex gap-2 {{ $soal->kunci_jawaban == 'E' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 font-semibold' : 'bg-white/5 border-white/5 text-white/70' }}">
                                <span class="font-bold">E.</span> <span>{{ $soal->opsi_e }}</span>
                                @if($soal->kunci_jawaban == 'E') <i class="fas fa-check-circle ml-auto my-auto text-emerald-400"></i> @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Tindakan Kanan -->
                    <div class="flex flex-col gap-2">
                        <button type="button" 
                                onclick="openEditModal({{ json_encode($soal) }})" 
                                class="p-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg text-xs flex items-center justify-center" 
                                title="Edit Soal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('guru.ujian.soal.destroy', $soal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-xs flex items-center justify-center" title="Hapus Soal">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="luxury-card py-16 text-center text-white/40 text-sm">
                <i class="fas fa-list-ol text-6xl text-white/10 mb-4 block"></i>
                Belum ada soal ujian. Silakan klik tombol <strong>"Tambah Soal Pilihan Ganda"</strong> di atas.
            </div>
        @endforelse
    </div>
</div>

<!-- ==================== MODAL TAMBAH SOAL ==================== -->
<div id="createModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeCreateModal()"></div>
    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-2xl p-6 overflow-hidden">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-plus text-blue-400 mr-1.5"></i> Tambah Soal Ujian</h5>
            <button onclick="closeCreateModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="{{ route('guru.ujian.soal.store', $ujian->id) }}" method="POST" enctype="multipart/form-data" class="max-h-[70vh] overflow-y-auto pr-2 space-y-4">
            @csrf
            <div>
                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pertanyaan / Soal</label>
                <textarea name="pertanyaan" rows="4" required placeholder="Tulis butir pertanyaan ujian di sini..."
                          class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Gambar Pendukung (Opsional)</label>
                <input type="file" name="gambar" accept="image/*"
                       class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                <p class="text-[10px] text-white/40 mt-1">Mendukung format gambar (JPG, JPEG, PNG, GIF). Maksimal 2MB.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan A</label>
                    <input type="text" name="opsi_a" required placeholder="Jawaban Opsi A"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan B</label>
                    <input type="text" name="opsi_b" required placeholder="Jawaban Opsi B"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan C</label>
                    <input type="text" name="opsi_c" required placeholder="Jawaban Opsi C"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan D</label>
                    <input type="text" name="opsi_d" required placeholder="Jawaban Opsi D"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan E (Opsional)</label>
                    <input type="text" name="opsi_e" placeholder="Jawaban Opsi E (Boleh dikosongkan)"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Kunci Jawaban</label>
                <select name="kunci_jawaban" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none" style="color: black;">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

            <div class="pt-4 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-xs shadow-glow">
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT SOAL ==================== -->
<div id="editModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeEditModal()"></div>
    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-2xl p-6 overflow-hidden">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-edit text-yellow-400 mr-1.5"></i> Edit Soal Ujian</h5>
            <button onclick="closeEditModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="editSoalForm" method="POST" enctype="multipart/form-data" class="max-h-[70vh] overflow-y-auto pr-2 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pertanyaan / Soal</label>
                <textarea name="pertanyaan" id="edit_pertanyaan" rows="4" required placeholder="Tulis butir pertanyaan ujian di sini..."
                          class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Ganti Gambar (Opsional)</label>
                <input type="file" name="gambar" accept="image/*"
                       class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                <p class="text-[10px] text-white/40 mt-1">Biarkan kosong jika tidak ingin mengubah gambar. Maksimal 2MB.</p>
                <div id="edit_gambar_preview_container" class="mt-3 hidden">
                    <p class="text-[10px] text-white/50 mb-1 uppercase font-semibold">Gambar Saat Ini:</p>
                    <img id="edit_gambar_preview" src="" class="max-h-32 rounded border border-white/10">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan A</label>
                    <input type="text" name="opsi_a" id="edit_opsi_a" required placeholder="Jawaban Opsi A"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan B</label>
                    <input type="text" name="opsi_b" id="edit_opsi_b" required placeholder="Jawaban Opsi B"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan C</label>
                    <input type="text" name="opsi_c" id="edit_opsi_c" required placeholder="Jawaban Opsi C"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan D</label>
                    <input type="text" name="opsi_d" id="edit_opsi_d" required placeholder="Jawaban Opsi D"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Pilihan E (Opsional)</label>
                    <input type="text" name="opsi_e" id="edit_opsi_e" placeholder="Jawaban Opsi E (Boleh dikosongkan)"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Kunci Jawaban</label>
                <select name="kunci_jawaban" id="edit_kunci_jawaban" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none" style="color: black;">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>

            <div class="pt-4 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-lg text-white font-bold text-xs shadow-glow">
                    Update Soal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openEditModal(soal) {
        // Set Action URL
        document.getElementById('editSoalForm').action = "{{ url('guru/ujian/soal') }}/" + soal.id;
        
        // Set values
        document.getElementById('edit_pertanyaan').value = soal.pertanyaan;
        document.getElementById('edit_opsi_a').value = soal.opsi_a;
        document.getElementById('edit_opsi_b').value = soal.opsi_b;
        document.getElementById('edit_opsi_c').value = soal.opsi_c;
        document.getElementById('edit_opsi_d').value = soal.opsi_d;
        document.getElementById('edit_opsi_e').value = soal.opsi_e || '';
        document.getElementById('edit_kunci_jawaban').value = soal.kunci_jawaban;

        // Image Preview
        const previewContainer = document.getElementById('edit_gambar_preview_container');
        const previewImg = document.getElementById('edit_gambar_preview');
        if (soal.gambar) {
            previewImg.src = "{{ asset('storage') }}/" + soal.gambar;
            previewContainer.classList.remove('hidden');
        } else {
            previewImg.src = "";
            previewContainer.classList.add('hidden');
        }

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
