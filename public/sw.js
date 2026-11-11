const CACHE_NAME = 'bibliashow-v1';
const PRECACHE_URLS = [
  '/',
  '/jogar',
  '/curiosidades',
  '/sobre',
  '/manifest.json',
  '/data/enciclopedia.json',
  '/audio/classico/inicio.wav',
  '/audio/classico/fase1.wav',
  '/audio/classico/fase2.wav',
  '/audio/classico/fase3.wav',
  '/audio/classico/fase4.wav',
  '/audio/classico/certa1.wav',
  '/audio/classico/errou1.wav',
  '/audio/classico/dica.wav',
  '/audio/classico/ajuda.wav',
  '/audio/classico/tecla.wav'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_URLS).catch(() => {});
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Não cachear chamadas de API de tempo real nem rotas POST
  if (event.request.method !== 'GET' || url.pathname.startsWith('/api/')) {
    return;
  }

  // Network First com fallback para Cache para páginas HTML dinâmicas
  if (event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          const cloned = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
          return response;
        })
        .catch(() => caches.match(event.request).then((res) => res || caches.match('/jogar')))
    );
    return;
  }

  // Cache First com Network fallback para assets estáticos (áudio, imagens, dados JSON)
  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((response) => {
        if (response.ok && (url.pathname.startsWith('/audio/') || url.pathname.startsWith('/data/') || url.pathname.endsWith('.json'))) {
          const cloned = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
        }
        return response;
      });
    })
  );
});
