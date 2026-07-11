const CACHE_NAME = 'alidebo-pwa-v1';
const urlsToCache = [
  '/',
  '/site.webmanifest',
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
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
