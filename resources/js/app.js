import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'GiftMatch';
const CURRENT_APP_BUILD = 'gm_2026_10_26_v5';

// Unconditionally clear stale caches and unregister zombie service workers
if (typeof window !== 'undefined') {
    try {
        if ('caches' in window) {
            caches.keys().then((keys) => {
                keys.forEach((k) => caches.delete(k));
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('limpar') || urlParams.has('reset')) {
            window.location.replace(window.location.pathname);
        }
    } catch (e) {}

    // Recover from Vite deployment chunk hash mismatches (prevents mobile black screens on cached assets)
    window.addEventListener('vite:preloadError', () => {
        const reloadKey = 'vite_preload_reload';
        const last = sessionStorage.getItem(reloadKey);
        const now = Date.now();
        if (!last || now - parseInt(last, 10) > 6000) {
            sessionStorage.setItem(reloadKey, now.toString());
            window.location.reload();
        }
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
            // If chunk loading failed due to deployment hash change, force reload with debounce
            if (typeof window !== 'undefined') {
                const reloadKey = 'chunk_reload_' + name;
                const last = sessionStorage.getItem(reloadKey);
                const now = Date.now();
                if (!last || now - parseInt(last, 10) > 6000) {
                    sessionStorage.setItem(reloadKey, now.toString());
                    window.location.reload();
                    return;
                }
            }
            throw err;
        });
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

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

