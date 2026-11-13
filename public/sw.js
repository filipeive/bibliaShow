const CACHE_NAME = 'bibliashow-v2';

const SCOPE_URL = new URL(self.registration ? self.registration.scope : self.location.href);
const BASE_PATH = SCOPE_URL.pathname.replace(/\/+$/, '');

function withBase(path) {
  const clean = path.startsWith('/') ? path : '/' + path;
  return (BASE_PATH + clean) || '/';
}

function relativePath(pathname) {
  if (BASE_PATH && pathname.startsWith(BASE_PATH)) {
    const rest = pathname.slice(BASE_PATH.length);
    return rest.startsWith('/') ? rest : '/' + rest;
  }
  return pathname;
}

const PUBLIC_HTML_ROUTES = new Set(['/', '/jogar', '/curiosidades', '/sobre']);
const PRIVATE_PREFIXES = ['/admin', '/apresentador', '/perguntas', '/login', '/register', '/logout', '/perfil', '/api', '/broadcasting', '/sanctum', '/projecao', '/entrar'];

const PRECACHE_URLS = [
  withBase('/'),
  withBase('/jogar'),
  withBase('/curiosidades'),
  withBase('/sobre'),
  withBase('/manifest.json'),
  withBase('/css/show-milhao.css'),
  withBase('/data/enciclopedia.json'),
  withBase('/audio/classico/inicio.wav'),
  withBase('/audio/classico/fase1.wav'),
  withBase('/audio/classico/fase2.wav'),
  withBase('/audio/classico/fase3.wav'),
  withBase('/audio/classico/fase4.wav'),
  withBase('/audio/classico/certa1.wav'),
  withBase('/audio/classico/errou1.wav'),
  withBase('/audio/classico/dica.wav'),
  withBase('/audio/classico/ajuda.wav'),
  withBase('/audio/classico/tecla.wav'),
  withBase('/audio/classico/suspense.wav'),
  withBase('/audio/classico/tempo_tick.wav'),
  withBase('/audio/classico/tempo_alerta.wav'),
  withBase('/audio/classico/ks.wav'),
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return Promise.allSettled(PRECACHE_URLS.map((u) => cache.add(u)));
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
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) {
    return;
  }

  const rel = relativePath(url.pathname);

  // Nunca interceptar nem cachear rotas privadas, autenticadas ou APIs em tempo real
  if (PRIVATE_PREFIXES.some((prefix) => rel === prefix || rel.startsWith(prefix + '/'))) {
    return;
  }

  // Network First apenas para rotas HTML públicas explicitamente permitidas
  if (event.request.headers.get('accept')?.includes('text/html')) {
    if (!PUBLIC_HTML_ROUTES.has(rel)) {
      return;
    }

    event.respondWith(
      fetch(event.request)
        .then((response) => {
          const cc = (response.headers.get('cache-control') || '').toLowerCase();
          if (response.ok && !response.redirected && !cc.includes('no-store')) {
            const cloned = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
          }
          return response;
        })
        .catch(() => caches.match(event.request).then((res) => res || caches.match(withBase('/jogar'))))
    );
    return;
  }

  // Cache First com Network fallback para assets estáticos permitidos
  event.respondWith(
    caches.match(event.request).then((cached) => {
      if (cached) return cached;
      return fetch(event.request).then((response) => {
        if (
          response.ok &&
          !response.redirected &&
          (rel.startsWith('/audio/') || rel.startsWith('/data/') || rel.startsWith('/css/') || rel.startsWith('/images/') || rel.endsWith('.json'))
        ) {
          const cloned = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
        }
        return response;
      });
    })
  );
});
