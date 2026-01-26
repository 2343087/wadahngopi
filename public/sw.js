const CACHE_NAME = 'wadahngopi-v2';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/favicon.ico',
];

// Cache Strategry: Stale-While-Revalidate for images & static assets
// Network-First for dynamic routes (cafes, explore)

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.map(key => {
                if (key !== CACHE_NAME) return caches.delete(key);
            })
        ))
    );
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Dynamic Pages: Network First
    if (url.pathname.startsWith('/explore') || url.pathname.startsWith('/cafes') || url.pathname === '/') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    const resClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, resClone));
                    return response;
                })
                .catch(() => caches.match(event.request))
        );
        return;
    }

    // Images & Static: Stale While Revalidate
    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                const fetchPromise = fetch(event.request).then(networkResponse => {
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, networkResponse.clone()));
                    return networkResponse;
                });
                return cachedResponse || fetchPromise;
            })
    );
});
