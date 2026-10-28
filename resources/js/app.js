import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import AppUpdateBanner from '@/Components/AppUpdateBanner.vue';

const appName = import.meta.env.VITE_APP_NAME || 'GiftMatch';

let lastClickedHref = null;

// True when this page was loaded via a ?refresh= cache-buster URL.
// Used to break redirect loops: if we already loaded fresh and STILL get errors,
// show the banner instead of looping.
let loadedWithCacheBuster = false;

/** Redirect to a URL, always adding ?refresh=<timestamp> so the browser bypasses HTTP cache. */
function hardNavigate(url) {
    try {
        const dest = new URL(url, window.location.origin);
        dest.searchParams.set('refresh', Date.now().toString());
        window.location.href = dest.toString();
    } catch (e) {
        window.location.href = url;
    }
}

if (typeof window !== 'undefined') {
    // Intercept clicks to track target URL during navigation
    document.addEventListener('click', (e) => {
        const anchor = e.target.closest('a');
        if (anchor && anchor.href && anchor.href.startsWith(window.location.origin)) {
            lastClickedHref = anchor.href;
        }
    }, true);

    try {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('limpar') || urlParams.has('reset')) {
            window.location.replace(window.location.pathname);
        } else if (urlParams.has('refresh') || urlParams.has('v')) {
            loadedWithCacheBuster = true; // This page was already loaded fresh — don't loop
            // Clean up cache buster parameter from address bar cleanly
            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('refresh');
            cleanUrl.searchParams.delete('v');
            window.history.replaceState({}, '', cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : '') + cleanUrl.hash);
        }
    } catch (e) {}

    // Recover from Vite deployment chunk hash mismatches
    window.addEventListener('vite:preloadError', (event) => {
        if (event && event.preventDefault) event.preventDefault();

        // If this page was already loaded with a cache-buster and we STILL get errors,
        // don't loop — show the update banner and let the user decide.
        if (loadedWithCacheBuster) {
            window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
                detail: { message: 'Erro ao carregar. Toque em "Atualizar Agora" para continuar.' }
            }));
            return;
        }

        try { sessionStorage.setItem('gm_just_updated', '1'); } catch (e) {}
        if ('caches' in window) {
            caches.keys().then((keys) => keys.forEach((k) => caches.delete(k))).catch(() => {});
        }

        // Navigate to the intended target (or current page) with a cache-buster
        const target = (lastClickedHref && lastClickedHref !== window.location.href)
            ? lastClickedHref
            : window.location.href;
        lastClickedHref = null;
        hardNavigate(target);
    });
}

createInertiaApp({
    title: (title) => (title ? (title.includes(appName) ? title : `${title} - ${appName}`) : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const pagePromise = pages[`./Pages/${name}.vue`];

        if (!pagePromise) {
            throw new Error(`Page not found: ./Pages/${name}.vue`);
        }

        return pagePromise().catch((err) => {
            console.error('Error loading page chunk:', err);

            if (typeof window !== 'undefined') {
                // If this page was already loaded with a cache-buster, don't loop.
                // Show the banner and let the user decide.
                if (loadedWithCacheBuster) {
                    window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
                        detail: { message: 'Erro ao carregar. Toque em "Atualizar Agora" para continuar.' }
                    }));
                    // Return a never-resolving promise so Inertia doesn't crash —
                    // the banner guides the user to do a manual fresh reload.
                    return new Promise(() => {});
                }

                // First-time failure: navigate to the intended target with a cache-buster
                // so the browser fetches fresh HTML and new chunk hashes from the server.
                try { sessionStorage.setItem('gm_just_updated', '1'); } catch (e) {}
                if ('caches' in window) {
                    caches.keys().then((keys) => keys.forEach((k) => caches.delete(k))).catch(() => {});
                }

                const target = (lastClickedHref && lastClickedHref !== window.location.href)
                    ? lastClickedHref
                    : window.location.href;
                lastClickedHref = null;
                hardNavigate(target);
                return new Promise(() => {}); // prevent error cascade while navigating
            }
            throw err;
        });
    },
    setup({ el, App, props, plugin }) {
        const rootComponent = {
            render() {
                return h('div', { class: 'relative min-h-screen' }, [
                    h(App, props),
                    h(AppUpdateBanner),
                ]);
            },
        };

        const app = createApp(rootComponent);

        // Global Vue runtime error handler
        app.config.errorHandler = (err, instance, info) => {
            console.error('Vue runtime error:', err, info);
        };

        return app
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#f43f5e',
    },
});

