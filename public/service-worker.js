const CACHE_NAME = 'sigebr-v1.0.0';
const OFFLINE_URL = '/offline.html';

// Assets que serão cacheados imediatamente ao instalar
const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/css/app.css',
    '/images/logo_sige_app.png',
    // Bootstrap e FontAwesome vem de CDN, não precisa cachear
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Instalando...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Pre-cache de assets críticos');
            return cache.addAll(PRECACHE_ASSETS.map(url => new Request(url, { cache: 'reload' })));
        }).catch((error) => {
            console.error('[Service Worker] Erro no pre-cache:', error);
        })
    );
    // Força ativação imediata
    self.skipWaiting();
});

// Ativação - limpa caches antigos
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Ativando...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Assume controle imediato
    return self.clients.claim();
});

// Estratégia de cache
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignora requisições que não são HTTP/HTTPS
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Ignora requisições de API externas (ZapSign, etc)
    if (url.hostname !== self.location.hostname) {
        return;
    }

    // Ignora requisições POST, PUT, DELETE (não cacheia formulários)
    if (request.method !== 'GET') {
        return;
    }

    // Estratégia: Network First para páginas HTML (sempre atualizado)
    if (request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Clona a resposta para cachear
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                    return response;
                })
                .catch(() => {
                    // Se falhar (offline), tenta cache
                    return caches.match(request).then((cachedResponse) => {
                        // Se não tem em cache, mostra página offline
                        return cachedResponse || caches.match(OFFLINE_URL);
                    });
                })
        );
        return;
    }

    // Estratégia: Cache First para assets estáticos (CSS, JS, imagens)
    if (
        request.url.includes('/css/') ||
        request.url.includes('/js/') ||
        request.url.includes('/images/') ||
        request.url.includes('/fonts/') ||
        request.url.includes('/build/')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Retorna do cache e atualiza em background
                    fetch(request).then((response) => {
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, response);
                        });
                    }).catch(() => {
                        // Falhou ao atualizar, mas temos cache
                    });
                    return cachedResponse;
                }
                // Não está em cache, busca da rede
                return fetch(request).then((response) => {
                    // Cacheia para próxima vez
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                    return response;
                });
            })
        );
        return;
    }

    // Para todo o resto (API, formulários, etc): sempre rede
    event.respondWith(
        fetch(request).catch(() => {
            // Se falhar e for GET, tenta cache
            if (request.method === 'GET') {
                return caches.match(request);
            }
            // Se for POST/PUT/DELETE, deixa falhar (mostrará erro no sistema)
            return new Response('Sem conexão. Por favor, tente novamente quando estiver online.', {
                status: 503,
                statusText: 'Service Unavailable',
                headers: new Headers({
                    'Content-Type': 'text/plain; charset=UTF-8'
                })
            });
        })
    );
});

// Mensagens do cliente (para limpar cache manualmente, se necessário)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(cacheNames.map((cacheName) => caches.delete(cacheName)));
            })
        );
    }
});

console.log('[Service Worker] Registrado e pronto!');
