import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import AppUpdateBanner from '@/Components/AppUpdateBanner.vue';

const appName = import.meta.env.VITE_APP_NAME || 'GiftMatch';

// Safe sessionStorage helpers that never throw in Safari Private Browsing mode
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

let lastClickedHref = null;

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

        window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
            detail: { message: 'Uma nova versão do GiftMatch está pronta! Toque para atualizar.' }
        }));

        if (lastClickedHref && lastClickedHref !== window.location.href) {
            const target = lastClickedHref;
            lastClickedHref = null;
            window.location.href = target;
            return;
        }

        const reloadKey = 'vite_preload_reload';
        const last = safeSessionGet(reloadKey);
        const now = Date.now();
        if (!last || now - parseInt(last, 10) > 4000) {
            safeSessionSet(reloadKey, now.toString());
            const bustUrl = new URL(window.location.href);
            bustUrl.searchParams.set('refresh', Date.now().toString());
            window.location.href = bustUrl.toString();
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

            // Notify user with the update banner
            if (typeof window !== 'undefined') {
                window.dispatchEvent(new CustomEvent('giftmatch:update-available', {
                    detail: { message: 'Nova versão disponível! Toque para atualizar o app.' }
                }));

                // If user was navigating to another page, navigate directly so server serves fresh HTML
                if (lastClickedHref && lastClickedHref !== window.location.href) {
                    const target = lastClickedHref;
                    lastClickedHref = null;
                    window.location.href = target;
                    return new Promise(() => {}); // prevent further uncaught error cascade
                }

                // If on same page, reload with cache-buster
                const reloadKey = 'chunk_reload_' + name;
                const last = safeSessionGet(reloadKey);
                const now = Date.now();
                if (!last || now - parseInt(last, 10) > 4000) {
                    safeSessionSet(reloadKey, now.toString());
                    const bustUrl = new URL(window.location.href);
                    bustUrl.searchParams.set('refresh', Date.now().toString());
                    window.location.href = bustUrl.toString();
                    return new Promise(() => {});
                }
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

