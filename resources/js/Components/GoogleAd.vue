<script setup>
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    slotId: {
        type: String,
        default: '',
    },
    format: {
        type: String,
        default: 'auto',
    },
    responsive: {
        type: Boolean,
        default: true,
    },
    adLayoutKey: {
        type: String,
        default: '',
    },
    showPlaceholderInDev: {
        type: Boolean,
        default: true,
    },
});

const clientId = import.meta.env.VITE_GOOGLE_ADSENSE_ID || '';
const isDev = import.meta.env.DEV;
const adLoaded = ref(false);

onMounted(() => {
    if (clientId) {
        nextTick(() => {
            try {
                if (typeof window !== 'undefined') {
                    (window.adsbygoogle = window.adsbygoogle || []).push({});
                    adLoaded.value = true;
                }
            } catch (err) {
                // Ignore adblocker or script load errors silently
                console.debug('AdSense notice:', err);
            }
        });
    }
});
</script>

<template>
    <div class="my-6 w-full flex flex-col items-center justify-center overflow-hidden">
        <!-- Active AdSense Unit (when client ID is configured) -->
        <template v-if="clientId">
            <div class="w-full max-w-4xl min-h-[90px] flex items-center justify-center">
                <ins
                    class="adsbygoogle block w-full text-center"
                    :data-ad-client="clientId"
                    :data-ad-slot="slotId || undefined"
                    :data-ad-format="format"
                    :data-ad-layout-key="adLayoutKey || undefined"
                    :data-full-width-responsive="responsive ? 'true' : 'false'"
                ></ins>
            </div>
        </template>

        <!-- Development / Setup Placeholder (helps preview layout before approval) -->
        <template v-else-if="showPlaceholderInDev && isDev">
            <div class="w-full max-w-4xl p-4 sm:p-5 rounded-2xl border border-dashed border-slate-800 bg-slate-900/50 backdrop-blur-sm text-center flex flex-col items-center justify-center gap-1.5 transition-all">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-slate-800 border border-slate-700/80 text-[10px] font-bold text-amber-400 uppercase tracking-wider">
                    <span>Espaço de Anúncio • Google AdSense</span>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    Ativação automática: defina <code class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-[11px]">VITE_GOOGLE_ADSENSE_ID="ca-pub-XXXXXXXXXXXX"</code> no seu arquivo <code class="text-slate-200 font-mono text-[11px]">.env</code>
                </p>
                <span v-if="slotId" class="text-[10px] text-slate-500 font-mono">
                    Slot ID configurado: {{ slotId }}
                </span>
            </div>
        </template>
    </div>
</template>
