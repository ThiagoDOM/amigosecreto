<script setup>
import { computed } from 'vue';
import { ShoppingBag, Truck, ExternalLink, Info, Gift, Tag } from 'lucide-vue-next';

const props = defineProps({
    friendName: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'card', // 'card' or 'compact'
    },
});

const shopeeUrl = (import.meta.env.VITE_AFFILIATE_SHOPEE_URL || '').trim();
const mercadoLivreUrl = (import.meta.env.VITE_AFFILIATE_MERCADOLIVRE_URL || '').trim();
const hasAnyLink = computed(() => Boolean(shopeeUrl || mercadoLivreUrl));

const displayTitle = computed(() => {
    if (props.title) return props.title;
    if (props.friendName) {
        return `Dúvida no presente de ${props.friendName}?`;
    }
    return 'Inspire-se com Ideias de Presentes';
});

const displaySubtitle = computed(() => {
    if (props.subtitle) return props.subtitle;
    return 'Confira ofertas com descontos especiais, cupons e entrega rápida em lojas parceiras de confiança:';
});
</script>

<template>
    <div
        v-if="hasAnyLink"
        :class="[
            variant === 'compact'
                ? 'p-4 rounded-2xl bg-slate-900/90 border border-slate-800'
                : 'rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800/90 p-5 sm:p-7 shadow-2xl backdrop-blur-xl'
        ]"
        class="w-full relative overflow-hidden transition-all"
    >
        <!-- Subtle festive ambient background accent -->
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4">
            <!-- Header -->
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0 shadow-sm">
                    <Gift class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                    <h3 class="text-base sm:text-lg font-bold font-display text-white flex items-center gap-2">
                        <span>{{ displayTitle }}</span>
                    </h3>
                    <p class="text-xs text-slate-300 leading-relaxed font-normal">
                        {{ displaySubtitle }}
                    </p>
                </div>
            </div>

            <!-- Partner Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <!-- Shopee Button -->
                <a
                    v-if="shopeeUrl"
                    :href="shopeeUrl"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="group relative flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-[#EE4D2D]/90 to-[#ff5722] hover:from-[#EE4D2D] hover:to-[#f4511e] text-white shadow-lg shadow-[#ee4d2d]/20 transition-all hover:scale-[1.02] active:scale-[0.99] border border-white/10"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shrink-0">
                            <ShoppingBag class="w-5 h-5 transition-transform group-hover:scale-110" />
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-semibold text-white/90 uppercase tracking-wider flex items-center gap-1">
                                <span>Shopee</span>
                                <span class="px-1.5 py-0.2 rounded bg-white/25 text-[10px] font-bold">Cupons</span>
                            </div>
                            <div class="text-sm font-extrabold text-white">
                                Ver Ofertas na Shopee
                            </div>
                        </div>
                    </div>
                    <ExternalLink class="w-4 h-4 text-white/80 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 shrink-0" />
                </a>

                <!-- Mercado Livre Button -->
                <a
                    v-if="mercadoLivreUrl"
                    :href="mercadoLivreUrl"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="group relative flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-[#FFE600] to-[#ffd700] hover:brightness-105 text-slate-900 shadow-lg shadow-amber-500/15 transition-all hover:scale-[1.02] active:scale-[0.99] border border-amber-300/40"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-950/10 backdrop-blur-sm flex items-center justify-center text-slate-900 shrink-0">
                            <Truck class="w-5 h-5 transition-transform group-hover:scale-110" />
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-semibold text-slate-800 uppercase tracking-wider flex items-center gap-1">
                                <span>Mercado Livre</span>
                                <span class="px-1.5 py-0.2 rounded bg-slate-900 text-amber-300 text-[10px] font-bold">Full</span>
                            </div>
                            <div class="text-sm font-extrabold text-slate-950">
                                Ver Ofertas no Mercado Livre
                            </div>
                        </div>
                    </div>
                    <ExternalLink class="w-4 h-4 text-slate-800 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 shrink-0" />
                </a>
            </div>

            <!-- Mandatory Transparent Affiliate Disclaimer Notice -->
            <div class="pt-3 border-t border-slate-800/80 flex items-start gap-2.5 text-[11px] text-slate-400 leading-relaxed">
                <Info class="w-4 h-4 text-amber-400/90 shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold text-slate-300">Aviso de Transparência:</span>
                    Os links acima são de parceiros afiliados. Comprando por eles, você paga o mesmo valor (aproveitando os mesmos descontos e cupons) e o <strong>GiftMatch</strong> recebe uma pequena comissão das lojas parceiras, ajudando a manter nossa plataforma gratuita, rápida e sem cobranças para você e seus amigos! ❤️
                </div>
            </div>
        </div>
    </div>
</template>
