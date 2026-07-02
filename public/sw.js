const CACHE_NAME = 'mts-syafiiyah-cache-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/login',
  '/favicon.ico',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'
];

// Install Event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching shell assets');
      return cache.addAll(ASSETS_TO_CACHE).catch(err => {
        console.warn('Failed to cache some initial assets', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            console.log('Clearing old cache', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', event => {
  // Only cache GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip web socket or chrome extension requests
  if (!event.request.url.startsWith('http')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Check if we received a valid response
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }

        // Dynamically cache static assets
        const url = new URL(event.request.url);
        if (
          url.pathname.endsWith('.css') ||
          url.pathname.endsWith('.js') ||
          url.pathname.endsWith('.png') ||
          url.pathname.endsWith('.jpg') ||
          url.pathname.endsWith('.svg') ||
          url.pathname.includes('/build/assets/')
        ) {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });
        }

        return response;
      })
      .catch(() => {
        // Network failed, try to serve from cache
        return caches.match(event.request).then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If offline and request is for a page/HTML document, return a offline fallback
          if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html')) {
            return new Response(
              `<!DOCTYPE html>
              <html lang="id">
              <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Koneksi Terputus | MTs Syafiiyah</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
                <script src="https://cdn.tailwindcss.com"></script>
              </head>
              <body class="bg-[#0a0a0a] text-white font-['Plus_Jakarta_Sans'] min-h-screen flex items-center justify-center p-6">
                <div class="max-w-md w-full text-center space-y-6 bg-[#041e18] p-8 rounded-2xl border border-emerald-500/20 shadow-2xl">
                  <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto text-emerald-500 text-4xl animate-pulse">
                    <i class="fa-solid fa-wifi-slash"></i>
                  </div>
                  <div class="space-y-2">
                    <h1 class="text-2xl font-extrabold text-white">Koneksi Terputus</h1>
                    <p class="text-gray-400 text-sm">Aplikasi tidak dapat terhubung ke server. Periksa koneksi internet Anda dan coba lagi.</p>
                  </div>
                  <button onclick="window.location.reload()" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl font-semibold hover:opacity-90 transition-opacity">
                    Coba Lagi
                  </button>
                </div>
              </body>
              </html>`,
              {
                headers: { 'Content-Type': 'text/html; charset=utf-8' }
              }
            );
          }
        });
      })
  );
});
