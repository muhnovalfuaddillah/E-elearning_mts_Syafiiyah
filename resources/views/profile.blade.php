@extends('layouts.app')

@section('title', 'Profil Saya - Pembelajaran Digital')
@section('breadcrumb', 'Profil')
@section('page-title', 'Profil Saya')

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

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
        <div class="flex items-center justify-between mb-2 pb-2 border-b border-red-500/20">
            <span class="text-sm font-semibold"><i class="fas fa-exclamation-triangle mr-2"></i> Terjadi Kesalahan Validasi:</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Sisi Kiri: Ringkasan Pengguna -->
        <div class="lg:col-span-1">
            <div class="luxury-card p-6 text-center">
                <!-- Avatar Simulation -->
                <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 mx-auto flex items-center justify-center text-white text-3xl font-extrabold shadow-glow relative border-2 border-white/20">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                    <!-- Status Indicator -->
                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-blue-500 border-2 border-[#0a0a0a] rounded-full"></span>
                </div>

                <h4 class="text-white font-bold text-lg mt-4">{{ $user->name }}</h4>
                <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider mt-1">{{ $user->role }}</p>
                <p class="text-white/40 text-xs mt-1">{{ $user->email }}</p>

                <!-- Detailed metadata based on role -->
                <div class="mt-6 pt-6 border-t border-white/10 text-left text-xs space-y-3">
                    @if($user->role === 'admin')
                        <div class="flex justify-between">
                            <span class="text-white/40">Hak Akses:</span>
                            <span class="text-white/85 font-semibold">Super Administrator</span>
                        </div>
                    @elseif($user->role === 'guru')
                        <div class="flex justify-between">
                            <span class="text-white/40">NIP:</span>
                            <span class="text-white/85 font-mono">{{ $user->nip ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/40">Mata Pelajaran:</span>
                            <span class="text-white/85">{{ $user->mapel ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/40">Jenis Kelamin:</span>
                            <span class="text-white/85">{{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    @elseif($user->role === 'siswa' && $user->siswa)
                        <div class="flex justify-between">
                            <span class="text-white/40">NIS / NISN:</span>
                            <span class="text-white/85 font-mono">{{ $user->siswa->nis }} / {{ $user->siswa->nisn ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/40">Kelas:</span>
                            <span class="text-white/85 font-semibold">{{ $user->siswa->kelas->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/40">Jenis Kelamin:</span>
                            <span class="text-white/85">{{ $user->siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-white/40">Bergabung Sejak:</span>
                        <span class="text-white/85">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Tab Actions -->
        <div class="lg:col-span-2">
            <div class="luxury-card overflow-hidden">
                <!-- Tab Headers -->
                <div class="flex border-b border-white/10 bg-white/5">
                    <button onclick="switchTab('edit-profile')" id="tab-edit-profile-btn" class="flex-1 py-4 text-center text-sm font-bold text-blue-400 border-b-2 border-blue-500 focus:outline-none transition">
                        <i class="fas fa-user-edit mr-1.5"></i> Edit Profil
                    </button>
                    <button onclick="switchTab('change-password')" id="tab-change-password-btn" class="flex-1 py-4 text-center text-sm font-bold text-white/50 hover:text-white border-b-2 border-transparent focus:outline-none transition">
                        <i class="fas fa-lock mr-1.5"></i> Ubah Password
                    </button>
                </div>

                <!-- Tab 1: Edit Profile -->
                <div id="tab-edit-profile" class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>

                            <!-- Additional Fields based on role -->
                            @if($user->role === 'guru')
                                <div>
                                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Nomor Telepon</label>
                                    <input type="text" name="telp" value="{{ old('telp', $user->telp) }}" placeholder="Contoh: 0812345678"
                                           class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="4" placeholder="Masukkan alamat lengkap tinggal saat ini..."
                                              class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">{{ old('alamat', $user->alamat) }}</textarea>
                                </div>
                            @elseif($user->role === 'siswa')
                                <div>
                                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Nomor Telepon (WA)</label>
                                    <input type="text" name="telp" value="{{ old('telp', $user->siswa->telp ?? '') }}" placeholder="Contoh: 0812345678"
                                           class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Alamat Lengkap Siswa</label>
                                    <textarea name="alamat" rows="4" placeholder="Masukkan alamat lengkap tempat tinggal saat ini..."
                                              class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">{{ old('alamat', $user->siswa->alamat ?? '') }}</textarea>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 pt-4 border-t border-white/10 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow flex items-center gap-1.5">
                                <i class="fas fa-save"></i> Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab 2: Change Password -->
                <div id="tab-change-password" class="p-6 hidden">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Password Saat Ini</label>
                                <input type="password" name="current_password" required placeholder="Masukkan password saat ini untuk keamanan"
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Password Baru</label>
                                <input type="password" name="password" required placeholder="Minimal 4 karakter"
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="text-white/70 text-xs block mb-1.5 uppercase tracking-wider font-semibold">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" required placeholder="Ketik ulang password baru Anda"
                                       class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="mt-8 pt-4 border-t border-white/10 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow flex items-center gap-1.5">
                                <i class="fas fa-key"></i> Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function switchTab(tabId) {
        const editProfileTab = document.getElementById('tab-edit-profile');
        const changePasswordTab = document.getElementById('tab-change-password');
        
        const editProfileBtn = document.getElementById('tab-edit-profile-btn');
        const changePasswordBtn = document.getElementById('tab-change-password-btn');
        
        if (tabId === 'edit-profile') {
            editProfileTab.classList.remove('hidden');
            changePasswordTab.classList.add('hidden');
            
            editProfileBtn.className = "flex-1 py-4 text-center text-sm font-bold text-blue-400 border-b-2 border-blue-500 focus:outline-none transition";
            changePasswordBtn.className = "flex-1 py-4 text-center text-sm font-bold text-white/50 hover:text-white border-b-2 border-transparent focus:outline-none transition";
        } else {
            editProfileTab.classList.add('hidden');
            changePasswordTab.classList.remove('hidden');
            
            editProfileBtn.className = "flex-1 py-4 text-center text-sm font-bold text-white/50 hover:text-white border-b-2 border-transparent focus:outline-none transition";
            changePasswordBtn.className = "flex-1 py-4 text-center text-sm font-bold text-blue-400 border-b-2 border-blue-500 focus:outline-none transition";
        }
    }
</script>
@endsection
