<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, X } from 'lucide-vue-next';

const isVisible = ref(false);

onMounted(() => {
    try {
        const consent = localStorage.getItem('giftmatch_cookies_accepted');
        if (!consent) {
            // Small delay for smooth entrance
            setTimeout(() => {
                isVisible.value = true;
            }, 600);
        }
    } catch (e) {
        // Fallback for private mode
    }
});

function acceptAll() {
    try {
        localStorage.setItem('giftmatch_cookies_accepted', 'all');
    } catch (e) {}
    isVisible.value = false;
}

function acceptEssential() {
    try {
        localStorage.setItem('giftmatch_cookies_accepted', 'essential');
    } catch (e) {}
    isVisible.value = false;
}
</script>

<template>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-6 sm:scale-95"
        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-6"
    >
        <div
            v-if="isVisible"
            role="dialog"
            aria-live="polite"
            aria-label="Aviso de Cookies"
            class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 rounded-2xl bg-slate-900/95 border border-slate-700/80 p-5 shadow-2xl backdrop-blur-xl text-slate-200 space-y-3"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                        <ShieldCheck class="w-4 h-4" />
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                            Sua Privacidade & Cookies
                        </h3>
                        <p class="text-[10px] text-slate-400">
                            Em conformidade com a LGPD e Google AdSense
                        </p>
                    </div>
                </div>

                <button
                    @click="acceptEssential"
                    type="button"
                    class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                    title="Fechar e aceitar apenas essenciais"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Utilizamos cookies essenciais para o funcionamento do site e tecnologias parceiras (como o <strong>Google AdSense</strong>) para personalizar anúncios e analisar nosso tráfego. Saiba mais em nossa
                <Link :href="route('privacy')" class="text-rose-400 hover:text-rose-300 underline font-semibold">
                    Política de Privacidade
                </Link>.
            </p>

            <div class="pt-1 flex items-center justify-end gap-2 text-xs">
                <button
                    type="button"
                    @click="acceptEssential"
                    class="px-3 py-1.5 rounded-xl border border-slate-700 bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-semibold transition-all active:scale-95"
                >
                    Apenas Essenciais
                </button>
                <button
                    type="button"
                    @click="acceptAll"
                    class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-sm shadow-rose-900/30 transition-all active:scale-95"
                >
                    Aceitar Todos
                </button>
            </div>
        </div>
    </transition>
</template>
