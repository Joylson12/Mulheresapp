self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open('static-v1')
    .then(cache=> cache.addAll([
            'offline.html',
            'dist/css/adminlte.min.css',
            'imagens/offline.png',
            'imagens/logomc.png'
        ]))
  );
});

self.addEventListener('activate', function activator(event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys
        .filter(function (key) {
          return key.indexOf('static-v1') !== 0;
        })
        .map(function (key) {
          return caches.delete(key);
        })
      );
    })
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    caches.match(event.request)
    .then(response => response || fetch(event.request)) 
    .catch(() => {
        if(event.request.mode == 'navigate'){
            return caches.match('offline.html');
        }
    })
  );
});