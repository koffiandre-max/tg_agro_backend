const CACHE_NAME = 'tg-agro-cache-v1';
const PRECACHE_URLS = ['/'];

const BACKGROUND_SYNC_TAG = 'tg-agro-sync';
const REFRESH_URL = '/session/refresh';
const LOGIN_URL = '/login';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const clone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, clone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => cachedResponse);

            return cachedResponse || fetchPromise;
        })
    );
});

self.addEventListener('sync', (event) => {
    if (event.tag === BACKGROUND_SYNC_TAG) {
        event.waitUntil(flushQueuedRequests());
    }
});

self.addEventListener('message', (event) => {
    const data = event.data;

    if (!data || !data.type) {
        return;
    }

    if (data.type === 'QUEUE_REQUEST') {
        const request = data.request;
        storeQueuedRequest(request).then(() => {
            if (self.registration.sync) {
                self.registration.sync.register(BACKGROUND_SYNC_TAG).catch(() => {});
            }
        });
    }

    if (data.type === 'TRY_REFRESH') {
        handleAuthRefresh().then((result) => {
            event.ports[0]?.postMessage(result);
        });
    }
});

async function flushQueuedRequests() {
    const queue = await getQueuedRequests();
    if (!queue.length) {
        return;
    }

    const refreshResult = await handleAuthRefresh();
    if (!refreshResult.success) {
        await broadcastClients({ type: 'AUTH_REQUIRED' });
        return;
    }

    const remaining = [];
    for (const item of queue) {
        try {
            const headers = new Headers(item.headers || {});
            headers.set('X-XSRF-TOKEN', refreshResult.csrfToken);
            if (refreshResult.token) {
                headers.set('Authorization', `Bearer ${refreshResult.token}`);
            }

            const response = await fetch(item.url, {
                method: item.method,
                headers,
                body: item.body,
                redirect: 'manual',
            });

            if (response.status === 419 || response.status === 401) {
                remaining.push(item);
                continue;
            }

            await broadcastClients({
                type: 'QUEUED_REQUEST_DONE',
                requestId: item.id,
                status: response.status,
            });
        } catch (error) {
            remaining.push(item);
        }
    }

    await saveQueuedRequests(remaining);

    if (remaining.length && 'sync' in self.registration) {
        self.registration.sync.register(BACKGROUND_SYNC_TAG).catch(() => {});
    }
}

async function handleAuthRefresh() {
    try {
        const response = await fetch(REFRESH_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        if (response.status === 419 || response.status === 401) {
            return { success: false, reason: 'session_expired' };
        }

        if (!response.ok) {
            return { success: false, reason: 'refresh_failed' };
        }

        const data = await response.json().catch(() => ({}));
        return {
            success: true,
            csrfToken: data?.csrf_token || '',
            token: data?.token || null,
            user: data?.user || null,
        };
    } catch (error) {
        return { success: false, reason: 'network_error' };
    }
}

async function storeQueuedRequest(request) {
    const queue = await getQueuedRequests();
    queue.push({
        id: crypto.randomUUID(),
        url: request.url,
        method: request.method,
        headers: Object.fromEntries(request.headers.entries()),
        body: request.body,
        createdAt: new Date().toISOString(),
    });
    await saveQueuedRequests(queue);
}

async function getQueuedRequests() {
    const cache = await caches.open(CACHE_NAME);
    const response = await cache.match('tg-agro-queue');
    if (!response) {
        return [];
    }
    try {
        const data = await response.json();
        return Array.isArray(data) ? data : [];
    } catch {
        return [];
    }
}

async function saveQueuedRequests(queue) {
    const cache = await caches.open(CACHE_NAME);
    const response = new Response(JSON.stringify(queue), {
        headers: { 'Content-Type': 'application/json' },
    });
    await cache.put('tg-agro-queue', response);
}

async function broadcastClients(message) {
    const allClients = await self.clients.matchAll();
    allClients.forEach((client) => client.postMessage(message));
}
