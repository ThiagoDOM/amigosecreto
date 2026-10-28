// Service Worker for GiftMatch PWA & Web Push Notifications
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Take control of all open clients immediately so the new SW serves fresh assets.
    // We defer claim() by one tick to avoid aborting in-flight WebKit/iOS module fetches.
    event.waitUntil(
        Promise.resolve().then(() => self.clients.claim())
    );
});

// Allow the page to explicitly trigger skipWaiting (used by AppUpdateBanner)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Push notification listener
self.addEventListener('push', (event) => {
    let payload = {};
    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload = {
                title: 'GiftMatch 🎁',
                body: event.data.text(),
            };
        }
    }

    const title = payload.title || 'GiftMatch 🎁 Sorteio Realizado!';
    const options = {
        body: payload.body || 'Seu amigo secreto já foi sorteado! Toque para conferir.',
        icon: payload.icon || '/apple-touch-icon.png',
        badge: payload.badge || '/favicon-32x32.png',
        tag: payload.tag || 'giftmatch-draw',
        data: {
            url: payload.url || '/',
        },
        vibrate: [100, 50, 100],
        renotify: true,
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// Notification click listener
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            // If window already open with the target URL, focus it
            for (const client of windowClients) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new window
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// IMPORTANT: No fetch listener to avoid caching issues, Vite chunk errors, or Inertia navigation freezes.
