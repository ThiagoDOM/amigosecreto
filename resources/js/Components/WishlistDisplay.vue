<script setup>
import { computed } from 'vue';
import { parseWishlist } from '@/Utils/formatters';
import { Gift, ExternalLink, Info, ShoppingBag, Truck } from 'lucide-vue-next';

const props = defineProps({
    wishlist: {
        type: [String, Array],
        default: () => [],
    },
    showSearchSuggestions: {
        type: Boolean,
        default: true,
    },
});

const items = computed(() => {
    return parseWishlist(props.wishlist);
});

const mlTool = import.meta.env.VITE_MERCADOLIVRE_AFFILIATE_TOOL || '45253537';
const mlWord = import.meta.env.VITE_MERCADOLIVRE_AFFILIATE_WORD || 'thiagomtman';
const shopeePid = import.meta.env.VITE_SHOPEE_AFFILIATE_PID || 'an_18380670344';

function getShopeeSearchUrl(query) {
    const encoded = encodeURIComponent(query);
    return `https://shopee.com.br/search?keyword=${encoded}&utm_source=${shopeePid}&utm_medium=affiliates&mmp_pid=${shopeePid}`;
}

function getMercadoLivreSearchUrl(query) {
    const encoded = encodeURIComponent(query);
    return `https://lista.mercadolivre.com.br/${encoded}?matt_tool=${mlTool}&matt_word=${mlWord}&forceInApp=true`;
}
</script>

<template>
    <div class="space-y-2.5">
        <div v-if="items.length === 0" class="text-xs text-slate-400 py-3 text-center font-medium">
            Nenhum presente ou link especificado.
        </div>

        <div v-else class="space-y-2.5">
            <div
                v-for="(item, idx) in items"
                :key="idx"
                class="p-3.5 sm:p-4 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3 transition-all hover:border-rose-500/40"
            >
                <div class="space-y-0.5">
                    <div class="font-bold text-sm text-white flex items-center gap-1.5">
                        <Gift class="w-4 h-4 text-rose-400 flex-shrink-0" />
                        <span>{{ item.title }}</span>
                    </div>
                    <div v-if="item.notes" class="text-xs text-slate-300 flex items-center gap-1 pl-5.5">
                        <Info class="w-3 h-3 text-slate-400 flex-shrink-0" />
                        <span>{{ item.notes }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-1.5 self-start md:self-center flex-shrink-0 pt-1 md:pt-0">
                    <!-- User Provided Direct Link -->
                    <a
                        v-if="item.url"
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer sponsored"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/60 border border-rose-800/60 hover:bg-rose-900/60 transition-colors shadow-sm"
                        title="Abrir link original indicado pelo participante"
                    >
                        <span>Link Indicado</span>
                        <ExternalLink class="w-3.5 h-3.5" />
                    </a>

                    <!-- Smart Search Buttons on Shopee & Mercado Livre -->
                    <template v-if="showSearchSuggestions">
                        <a
                            :href="getShopeeSearchUrl(item.title)"
                            target="_blank"
                            rel="noopener noreferrer sponsored"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-bold text-[#ff643d] bg-[#EE4D2D]/10 hover:bg-[#EE4D2D]/20 border border-[#EE4D2D]/30 transition-all hover:scale-[1.02] shadow-sm"
                            title="Pesquisar este produto na Shopee com cupons"
                        >
                            <ShoppingBag class="w-3 h-3 text-[#EE4D2D]" />
                            <span>Shopee</span>
                        </a>

                        <a
                            :href="getMercadoLivreSearchUrl(item.title)"
                            target="_blank"
                            rel="noopener noreferrer sponsored"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-bold text-amber-300 bg-amber-400/10 hover:bg-amber-400/20 border border-amber-400/30 transition-all hover:scale-[1.02] shadow-sm"
                            title="Pesquisar este produto no Mercado Livre com entrega rápida"
                        >
                            <Truck class="w-3 h-3 text-amber-400" />
                            <span>Mercado Livre</span>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
