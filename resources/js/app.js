import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import AppUpdateBanner from '@/Components/AppUpdateBanner.vue';

const appName = import.meta.env.VITE_APP_NAME || 'GiftMatch';

// Safe storage helpers that never throw in private browsing mode
function safeSessionGet(key) {
    try {
        return typeof window !== 'undefined' && window.sessionStorage ? window.sessionStorage.getItem(key) : null;
    } catch (e) {
        return null;
    }
}

function safeSessionSet(key, value) {
    try {
        if (typeof window !== 'undefined' && window.sessionStorage) {
            window.sessionStorage.setItem(key, value);
        }
    } catch (e) {}
}

if (typeof window !== 'undefined') {
    // Handle reset / clear query params
    try {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('limpar') || urlParams.has('reset')) {
            try { sessionStorage.clear(); } catch (e) {}
            try { localStorage.clear(); } catch (e) {}
            if ('caches' in window) {
                caches.keys().then((keys) => Promise.all(keys.map((k) => caches.delete(k)))).catch(() => {});
            }
            window.location.replace(window.location.pathname);
        } else if (urlParams.has('v') || urlParams.has('refresh')) {
            // Clean up cache buster param from URL cleanly without reload
            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('v');
            cleanUrl.searchParams.delete('refresh');
            window.history.replaceState({}, '', cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : '') + cleanUrl.hash);
        }
    } catch (e) {}

    // Recover from Vite deployment chunk hash mismatches
    window.addEventListener('vite:preloadError', (event) => {
        if (event && event.preventDefault) event.preventDefault();

        const reloadKey = 'vite_preload_reload';
        const last = safeSessionGet(reloadKey);
        const now = Date.now();

        if (!last || now - parseInt(last, 10) > 8000) {
            safeSessionSet(reloadKey, now.toString());
            window.location.reload();
        } else {
            window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
                detail: { message: 'Nova versão disponível. Toque para atualizar.' },
            }));
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

            if (typeof window !== 'undefined') {
                const reloadKey = 'chunk_reload_' + name;
                const last = safeSessionGet(reloadKey);
                const now = Date.now();

                // If not reloaded within the last 8 seconds, reload to get fresh chunks
                if (!last || now - parseInt(last, 10) > 8000) {
                    safeSessionSet(reloadKey, now.toString());
                    window.location.reload();
                    return new Promise(() => {}); // Wait for the reload
                }

                // If already reloaded recently and still failed, show the update banner
                window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
                    detail: { message: 'Erro ao carregar a página. Toque para atualizar.' },
                }));
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
