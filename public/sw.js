const cacheName = 'wadahngopi-vue-assets-v3'
const coreAssets = ['/wadahngopi.png', '/favicon-32x32.png', '/manifest.json']

const offlineHtml = `<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sedang Offline | WadahNgopi</title>
<style>
body{margin:0;min-height:100svh;display:grid;place-items:center;padding:1.5rem;background:#F5F4F2;color:#241F1A;font:400 1rem/1.6 system-ui,-apple-system,sans-serif}
main{max-width:28rem;padding:2rem;border-radius:1rem;background:#FFF}
h1{font-family:Georgia,serif;font-weight:400;font-size:1.75rem;line-height:1.15;margin:0 0 .75rem}
p{margin:0 0 1.25rem;color:#5C534B}
button{min-height:44px;padding:.65rem 1.25rem;border:0;border-radius:.5rem;background:#302119;color:#F5F4F2;font:600 .95rem/1.2 inherit;cursor:pointer}
</style>
</head>
<body>
<main>
<h1>Koneksi sedang terputus.</h1>
<p>Halaman ini belum bisa dimuat tanpa jaringan internet. Periksa sambungan Anda lalu muat ulang.</p>
<button type="button" onclick="location.reload()">Muat ulang</button>
</main>
</body>
</html>`

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(cacheName)
            .then((cache) => cache.addAll(coreAssets))
            .catch(() => {})
            .finally(() => self.skipWaiting()),
    )
})

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        for (const key of await caches.keys()) {
            if (key.startsWith('wadahngopi-') && key !== cacheName) await caches.delete(key)
        }
        await self.clients.claim()
    })())
})

self.addEventListener('fetch', (event) => {
    const { request } = event
    const url = new URL(request.url)
    if (request.method !== 'GET' || url.origin !== self.location.origin) return

    const isStaticAsset =
        url.pathname.startsWith('/build/assets/') ||
        url.pathname.startsWith('/icons/') ||
        coreAssets.includes(url.pathname)

    if (isStaticAsset) {
        event.respondWith((async () => {
            const cache = await caches.open(cacheName)
            const cached = await cache.match(request)
            if (cached) return cached
            const response = await fetch(request)
            if (response.ok && response.type === 'basic') await cache.put(request, response.clone())
            return response
        })())
        return
    }

    const isPublicNavigate =
        request.mode === 'navigate' &&
        !url.pathname.startsWith('/admin') &&
        !url.pathname.startsWith('/api') &&
        url.pathname !== '/logout'

    if (isPublicNavigate) {
        event.respondWith(
            fetch(request).catch(() => new Response(offlineHtml, {
                status: 503,
                headers: { 'content-type': 'text/html; charset=utf-8' },
            })),
        )
    }
})
