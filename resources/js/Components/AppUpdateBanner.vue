<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { RotateCcw, X, ArrowUpCircle } from 'lucide-vue-next';

const page = usePage();
const isVisible = ref(false);
const isUpdating = ref(false);
const updateMessage = ref('Uma nova versão do GiftMatch está disponível com melhorias.');

let initialVersion = null;
let pollInterval = null;

async function checkServerVersion() {
    if (typeof window === 'undefined') return;

    try {
        const res = await fetch('/api/app-version', {
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                'Cache-Control': 'no-cache',
            },
        });

        if (res.ok) {
            const data = await res.json();
            if (data && data.version) {
                if (!initialVersion) {
                    initialVersion = data.version;
                } else if (data.version !== initialVersion) {
                    showUpdateBanner('Nova versão disponível! Toque para atualizar o GiftMatch.');
                }
            }
        }
    } catch (e) {
        // Silently ignore network failures
    }
}

function showUpdateBanner(msg) {
    if (msg) updateMessage.value = msg;
    try {
        const dismissedAt = sessionStorage.getItem('gm_update_dismissed_at');
        if (dismissedAt && Date.now() - parseInt(dismissedAt, 10) < 5 * 60 * 1000) {
            // Dismissed within last 5 minutes
            return;
        }
    } catch (e) {}

    isVisible.value = true;
}

function dismiss() {
    isVisible.value = false;
    try {
        sessionStorage.setItem('gm_update_dismissed_at', Date.now().toString());
    } catch (e) {}
}

async function triggerUpdate() {
    if (isUpdating.value) return;
    isUpdating.value = true;

    try {
        // 1. Purge CacheStorage
        if ('caches' in window) {
            try {
                const keys = await caches.keys();
                await Promise.all(keys.map((k) => caches.delete(k)));
            } catch (e) {}
        }

        // 2. Unregister legacy service workers to force fresh fetch
        if ('serviceWorker' in navigator) {
            try {
                const registrations = await navigator.serviceWorker.getRegistrations();
                await Promise.all(registrations.map((r) => r.unregister()));
            } catch (e) {}
        }

        // 3. Clear session storage flags
        try {
            sessionStorage.removeItem('gm_update_dismissed_at');
            sessionStorage.removeItem('vite_preload_reload');
        } catch (e) {}
    } catch (e) {}

    // 4. Force hard reload with timestamp query param to bust browser disk/HTTP cache
    const targetUrl = new URL(window.location.href);
    targetUrl.searchParams.set('v', Date.now().toString());
    window.location.href = targetUrl.toString();
}

function handleCustomUpdateEvent(e) {
    const detailMsg = e?.detail?.message;
    showUpdateBanner(detailMsg || 'Uma nova versão do GiftMatch está disponível.');
}

function handleVisibilityChange() {
    if (document.visibilityState === 'visible') {
        checkServerVersion();
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration().then((reg) => {
                if (reg) reg.update().catch(() => {});
            }).catch(() => {});
        }
    }
}

onMounted(() => {
    // Record initial page version
    initialVersion = page.props?.app_version || null;

    // Listen for custom trigger from app.js (e.g. chunk preload errors or SW updatefound)
    window.addEventListener('giftmatch:update-available', handleCustomUpdateEvent);
    document.addEventListener('visibilitychange', handleVisibilityChange);

    // Check service worker waiting status
    if (typeof navigator !== 'undefined' && 'serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistration().then((reg) => {
            if (reg) {
                if (reg.waiting) {
                    showUpdateBanner('Nova versão pronta para instalar.');
                }
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                showUpdateBanner('Nova versão baixada! Toque para atualizar.');
                            }
                        });
                    }
                });
            }
        }).catch(() => {});
    }

    // Initial check after 2 seconds, then periodically every 90 seconds
    setTimeout(checkServerVersion, 2000);
    pollInterval = setInterval(checkServerVersion, 90 * 1000);
});

onUnmounted(() => {
    window.removeEventListener('giftmatch:update-available', handleCustomUpdateEvent);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    if (pollInterval) clearInterval(pollInterval);
});

// Expose update trigger globally for app.js failsafes
if (typeof window !== 'undefined') {
    window.performAppUpdate = triggerUpdate;
    window.showAppUpdateBanner = showUpdateBanner;
}
</script>

<template>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-8 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-8"
    >
        <div
            v-if="isVisible"
            role="status"
            aria-live="polite"
            class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-[100] rounded-2xl bg-slate-900/95 border border-amber-500/40 p-4 sm:p-5 shadow-2xl backdrop-blur-xl text-slate-100 space-y-3 shadow-amber-950/20"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 flex items-center justify-center shrink-0">
                        <RotateCcw class="w-5 h-5" :class="{ 'animate-spin': isUpdating }" />
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-0.5">
                            Atualização Disponível
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-white leading-tight">
                            Há uma nova versão do GiftMatch!
                        </p>
                    </div>
                </div>

                <button
                    @click="dismiss"
                    type="button"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                    title="Fechar aviso"
                    aria-label="Fechar aviso de atualização"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                {{ updateMessage }}
            </p>

            <div class="pt-1 flex items-center justify-end gap-2.5 text-xs">
                <button
                    type="button"
                    @click="dismiss"
                    class="px-3 py-1.5 rounded-xl border border-slate-700 bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-semibold transition-all active:scale-95 cursor-pointer"
                >
                    Depois
                </button>
                <button
                    type="button"
                    @click="triggerUpdate"
                    :disabled="isUpdating"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-400 hover:to-rose-400 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20 transition-all active:scale-95 cursor-pointer disabled:opacity-75"
                >
                    <RotateCcw class="w-3.5 h-3.5" :class="{ 'animate-spin': isUpdating }" />
                    <span>{{ isUpdating ? 'Atualizando...' : 'Atualizar Agora' }}</span>
                </button>
            </div>
        </div>
    </transition>
</template>
