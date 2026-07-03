<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1d4ed8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <title>Login | MTs Syafiiyah</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('ServiceWorker registered', reg))
            .catch(err => console.error('ServiceWorker registration failed', err));
        });
      }
    </script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Your existing CSS styles here (same as template) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .login-image {
            flex: 1;
            position: relative;
            background: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRiowz5xixVx1WncqK-Bj0m3IYDAn9Uf5VWAQ&s');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(88, 28, 135, 0.75) 50%, rgba(0, 0, 0, 0.85) 100%);
        }

        .image-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            color: white;
            animation: fadeInLeft 0.8s ease-out;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        .brand-icon i {
            color: white;
        }

        .brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff, #a7f3d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text h1 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2.5rem;
        }

        .stats-grid {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            flex: 1;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.25rem;
        }

        .login-form-side {
            flex: 1;
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-y: auto;
        }

        .login-card {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 2;
            animation: fadeInRight 0.8s ease-out;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #a7f3d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group i:not(.password-toggle) {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 1rem;
            transition: all 0.3s;
            z-index: 1;
        }

        .input-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            font-size: 0.95rem;
            color: white;
            transition: all 0.3s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .input-group input:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .input-group input:focus + i:not(.password-toggle) {
            color: #3b82f6;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.4);
            transition: color 0.3s;
            z-index: 2;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .forgot-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .forgot-link:hover {
            color: #fbbf24;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 1rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 1.5rem;
        }

        .login-btn i {
            margin-right: 0.5rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -5px rgba(59, 130, 246, 0.5);
        }

        .login-btn:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .divider span {
            margin: 0 1rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .social-btn {
            flex: 1;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .social-btn i {
            font-size: 1rem;
        }

        .social-btn:hover {
            background: rgba(168, 85, 247, 0.2);
            border-color: #a855f7;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .register-link a {
            color: #a855f7;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #ec4899;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
        }

        .error-message i {
            font-size: 1rem;
        }

        .error-message.show {
            display: flex;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
        }

        .success-message i {
            font-size: 1rem;
        }

        .success-message.show {
            display: flex;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 968px) {
            .login-image {
                display: none;
            }
            
            .login-form-side {
                flex: 1;
            }
            
            body {
                overflow: auto;
            }
        }

        @media (max-width: 640px) {
            .login-form-side {
                padding: 1rem;
            }
            
            .form-header h2 {
                font-size: 1.5rem;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- SISI KIRI -->
    <div class="login-image">
        <div class="image-overlay"></div>
        <div class="image-content">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="brand-text">MTs Syafiiyah</span>
            </div>
            
            <div class="welcome-text">
                <h1>
                    Aplikasi E-elearning<br>
                    <span class="gradient-text">Pembelajaran Digital</span>
                </h1>
                <p>Sistem pembelajaran digital modern untuk MTs Syafiiyah.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $siswa }}</div>
                    <div class="stat-label">Total Siswa</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $guru }}</div>
                    <div class="stat-label">Total Guru</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $totalAkses }}</div>
                    <div class="stat-label">Akses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SISI KANAN -->
    <div class="login-form-side">
        <div class="login-card">
            <div class="form-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Anda</p>
            </div>

            <div id="error-message" class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="error-text"></span>
            </div>

            <div id="success-message" class="success-message">
                <i class="fas fa-check-circle"></i>
                <span id="success-text"></span>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="email" name="email" placeholder="Email atau NIS" value="{{ old('email') }}" required autofocus>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                </div>
                
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-link" onclick="forgotPassword(event)">Lupa Password?</a>
                </div>
                
                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-arrow-right"></i>
                    Masuk
                </button>
            </form>

            <div class="divider">
                <span>Lupa Password?</span>
                <a href="#" class="forgot-link" onclick="forgotPassword(event)">Klik di sini</a>
            </div>

            <!-- Unduh & Pasang Aplikasi -->
            <div class="app-download-section" style="margin-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 1.5rem;">
                <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.8rem; margin-bottom: 0.75rem; text-align: center;">Pasang & Unduh Aplikasi Portal</p>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <!-- PWA Install Button -->
                    <button class="install-pwa-btn" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.75rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i class="fa-solid fa-laptop-code" style="color: #3b82f6; font-size: 1rem;"></i>
                        Pasang Aplikasi Web (PWA)
                    </button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
                        <!-- Android APK Download -->
                        <a href="/downloads/mts-syafiiyah.apk" download style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.75rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <i class="fab fa-android" style="font-size: 1rem;"></i>
                            Android (.APK)
                        </a>
                        <!-- iOS App Store -->
                        <a href="#" onclick="Swal.fire({title: 'Aplikasi iOS', text: 'Aplikasi iOS saat ini sedang dalam proses review di Apple App Store.', icon: 'info', confirmButtonColor: '#3b82f6', background: '#041e18', color: '#fff'}); return false;" style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.75rem; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            <i class="fab fa-apple" style="font-size: 1rem;"></i>
                            iOS App
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Password
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Handle Form Submit dengan AJAX
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const btn = document.getElementById('loginBtn');
    const errorDiv = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const successDiv = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    
    // Hide messages
    errorDiv.classList.remove('show');
    successDiv.classList.remove('show');
    
    // Disable button
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    btn.disabled = true;
    
    // Submit via AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            successText.innerHTML = data.message;
            successDiv.classList.add('show');
            
            // Redirect after success
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            errorText.innerHTML = data.message;
            errorDiv.classList.add('show');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        errorText.innerHTML = 'Terjadi kesalahan. Silakan coba lagi.';
        errorDiv.classList.add('show');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
});

function forgotPassword(event) {
    event.preventDefault();
    const errorDiv = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    errorText.innerHTML = 'Silakan hubungi administrator untuk reset password.';
    errorDiv.classList.add('show');
    
    setTimeout(() => {
        errorDiv.classList.remove('show');
    }, 4000);
}

function socialLogin(provider) {
    const errorDiv = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    errorText.innerHTML = `Login dengan ${provider} akan segera hadir.`;
    errorDiv.classList.add('show');
    
    setTimeout(() => {
        errorDiv.classList.remove('show');
    }, 3000);
}

// Auto clear messages after page load
document.addEventListener('DOMContentLoaded', function() {
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    
    if (errorDiv) errorDiv.classList.remove('show');
    if (successDiv) successDiv.classList.remove('show');
});

// PWA Install Flow
let deferredPrompt;
const pwaInstallButtons = document.querySelectorAll('.install-pwa-btn');

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    pwaInstallButtons.forEach(btn => {
        btn.style.borderColor = 'rgba(59, 130, 246, 0.4)';
        btn.style.background = 'rgba(59, 130, 246, 0.05)';
    });
});

pwaInstallButtons.forEach(btn => {
    btn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`PWA install outcome: ${outcome}`);
            deferredPrompt = null;
        } else {
            Swal.fire({
                title: 'Instal Aplikasi Web (PWA)',
                html: `<div class="text-left space-y-3 font-sans text-sm text-slate-300">
                        <p>Aplikasi ini dapat diinstal langsung tanpa Play Store/App Store:</p>
                        <p><strong>1. Pengguna Android / Chrome:</strong><br>Klik tombol menu titik tiga di pojok kanan atas browser, lalu pilih <strong>"Tambahkan ke Layar Utama"</strong> atau <strong>"Instal Aplikasi"</strong>.</p>
                        <p><strong>2. Pengguna iPhone / Safari:</strong><br>Klik tombol <strong>"Share" (Bagikan)</strong> <i class="fa-solid fa-share-from-square text-blue-400"></i> di menu bawah, lalu pilih <strong>"Tambahkan ke Layar Utama"</strong>.</p>
                       </div>`,
                icon: 'info',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3b82f6',
                background: '#041e18',
                color: '#fff'
            });
        }
    });
});
</script>

</body>
</html>
