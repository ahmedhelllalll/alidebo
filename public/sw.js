const CACHE_NAME = 'alidebo-pwa-v1';
const urlsToCache = [
  '/',
  '/site.webmanifest',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Use addAll safely, but don't fail the SW install if it fails
        return cache.addAll(urlsToCache).catch(err => {
            console.log('SW Cache error: ', err);
        });
      })
  );
  // Force immediate activation
  self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  // A basic fetch handler is required for the PWA to be installable.
  // We try to fetch from the network first, fallback to cache if offline.
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});
