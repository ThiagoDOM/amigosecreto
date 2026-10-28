// Service Worker for GiftMatch PWA & Web Push Notifications
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Activate smoothly without calling clients.claim() which aborts in-flight module script downloads on WebKit/iOS
    event.waitUntil(Promise.resolve());
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
