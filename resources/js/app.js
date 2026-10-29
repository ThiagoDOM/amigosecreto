import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import AppUpdateBanner from '@/Components/AppUpdateBanner.vue';

const appName = import.meta.env.VITE_APP_NAME || 'GiftMatch';

if (typeof window !== 'undefined') {
    // Handle clean-up / reset query params safely
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
            // Clean up cache buster parameter from address bar cleanly
            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('v');
            cleanUrl.searchParams.delete('refresh');
            window.history.replaceState({}, '', cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : '') + cleanUrl.hash);
        }
    } catch (e) {}

    // Prevent benign modulepreload network failures (common on mobile / Samsung Internet / Data Saver)
    // from halting script execution or throwing uncaught errors.
    window.addEventListener('vite:preloadError', (event) => {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
    });
}

createInertiaApp({
    title: (title) => (title ? (title.includes(appName) ? title : `${title} - ${appName}`) : appName),
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
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
