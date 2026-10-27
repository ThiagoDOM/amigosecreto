<script setup>
import { ref, computed } from 'vue';
import { 
    X, 
    Gift, 
    ShoppingBag, 
    ExternalLink, 
    Truck, 
    Tag, 
    Heart, 
    Search, 
    Filter,
    DollarSign,
    Info,
    Check
} from 'lucide-vue-next';
import { parseWishlist, formatCurrency } from '@/Utils/formatters';
import { getTheme, buildStoreSearchUrls } from '@/Utils/themes';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    friendName: {
        type: String,
        default: 'seu amigo(a)',
    },
    wishlist: {
        type: [Array, String],
        default: () => [],
    },
    theme: {
        type: String,
        default: 'traditional',
    },
    budgetLimit: {
        type: [Number, String],
        default: null,
    },
    clothingSize: {
        type: String,
        default: '',
    },
    shoeSize: {
        type: String,
        default: '',
    },
    likes: {
        type: String,
        default: '',
    },
    dislikes: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close']);

const activeFilter = ref('all'); // 'all', 'wishlist', 'theme', 'under50'

const parsedWishes = computed(() => {
    return parseWishlist(props.wishlist);
});

const currentTheme = computed(() => {
    return getTheme(props.theme);
});

const themeIdeas = computed(() => {
    return currentTheme.value.giftIdeas || [];
});

const allSuggestedItems = computed(() => {
    const list = [];

    // 1. Participant's specific wishlist items
    parsedWishes.value.forEach((item, index) => {
        list.push({
            id: `wish-${index}`,
            title: item.title,
            notes: item.notes,
            url: item.url,
            isFromWishlist: true,
            category: 'Pedido do Amigo',
            badge: 'Lista Oficial',
            badgeColor: 'bg-rose-500/20 text-rose-300 border-rose-500/30',
        });
    });

    // 2. Curated theme ideas
    themeIdeas.value.forEach((idea, index) => {
        list.push({
            id: `theme-${index}`,
            title: idea.title,
            notes: `Ideal para o tema ${currentTheme.value.label}`,
            price: idea.price,
            isFromTheme: true,
            category: idea.category,
            badge: currentTheme.value.label,
            badgeColor: currentTheme.value.badgeClass,
        });
    });

    return list;
});

const filteredItems = computed(() => {
    if (activeFilter.value === 'wishlist') {
        return allSuggestedItems.value.filter(i => i.isFromWishlist);
    }
    if (activeFilter.value === 'theme') {
        return allSuggestedItems.value.filter(i => i.isFromTheme);
    }
    return allSuggestedItems.value;
});

function getStoreLinks(itemTitle) {
    let query = itemTitle;
    if (props.clothingSize && /camis|roup|calç|vest/i.test(itemTitle)) {
        query += ` tamanho ${props.clothingSize}`;
    } else if (props.shoeSize && /chinel|tênis|calçad|sand/i.test(itemTitle)) {
        query += ` tamanho ${props.shoeSize}`;
    }
    return buildStoreSearchUrls(query);
}

const shopeeDirectUrl = (import.meta.env.VITE_AFFILIATE_SHOPEE_URL || '').trim();
const mercadoLivreDirectUrl = (import.meta.env.VITE_AFFILIATE_MERCADOLIVRE_URL || '').trim();

function close() {
    emit('close');
}
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-950/85 backdrop-blur-md overflow-y-auto animate-in fade-in duration-200"
        @click.self="close"
    >
        <div class="relative w-full max-w-3xl my-auto rounded-3xl bg-slate-900 border border-slate-700/80 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
            <!-- Festive gradient top border accent -->
            <div class="h-1.5 w-full bg-gradient-to-r from-rose-500 via-amber-400 to-pink-500"></div>

            <!-- Modal Header -->
            <div class="p-5 sm:p-6 pb-4 border-b border-slate-800 flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                        <Gift class="w-3.5 h-3.5 text-rose-400" />
                        <span>Curadoria Inteligente de Presentes</span>
                    </div>

                    <!-- Requested exact title requirement -->
                    <h2 class="text-xl sm:text-2xl font-black font-display text-white">
                        Mostrando produtos ideais para {{ friendName }}
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                        Itens encontrados com base na lista de desejos, tema <strong>{{ currentTheme.label }}</strong>
                        <span v-if="budgetLimit"> e valor sugerido de <strong>{{ formatCurrency(budgetLimit) }}</strong></span>.
                    </p>
                </div>

                <button
                    @click="close"
                    type="button"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors shrink-0 cursor-pointer"
                    aria-label="Fechar"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 space-y-6 overflow-y-auto flex-grow custom-scrollbar">
                <!-- Participant Quick Preferences Pill Summary -->
                <div
                    v-if="clothingSize || shoeSize || likes || dislikes"
                    class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 flex flex-wrap items-center gap-2 text-xs"
                >
                    <span class="font-bold text-slate-300 flex items-center gap-1">
                        <Heart class="w-3.5 h-3.5 text-rose-400" />
                        Dicas de {{ friendName }}:
                    </span>
                    <span v-if="clothingSize" class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-200 border border-slate-700">
                        Roupa: <strong>{{ clothingSize }}</strong>
                    </span>
                    <span v-if="shoeSize" class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-200 border border-slate-700">
                        Calçado: <strong>{{ shoeSize }}</strong>
                    </span>
                    <span v-if="likes" class="px-2 py-0.5 rounded-lg bg-emerald-950/60 text-emerald-300 border border-emerald-800/60">
                        Gosta de: {{ likes }}
                    </span>
                    <span v-if="dislikes" class="px-2 py-0.5 rounded-lg bg-rose-950/60 text-rose-300 border border-rose-800/60">
                        Evitar: {{ dislikes }}
                    </span>
                </div>

                <!-- Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-semibold">
                    <button
                        @click="activeFilter = 'all'"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap cursor-pointer"
                        :class="activeFilter === 'all' ? 'bg-rose-600 text-white shadow-sm font-bold' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700'"
                    >
                        Todos os Itens ({{ allSuggestedItems.length }})
                    </button>

                    <button
                        v-if="parsedWishes.length > 0"
                        @click="activeFilter = 'wishlist'"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap cursor-pointer flex items-center gap-1.5"
                        :class="activeFilter === 'wishlist' ? 'bg-rose-600 text-white shadow-sm font-bold' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700'"
                    >
                        <Gift class="w-3.5 h-3.5" />
                        <span>Pedidos de {{ friendName }} ({{ parsedWishes.length }})</span>
                    </button>

                    <button
                        @click="activeFilter = 'theme'"
                        type="button"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap cursor-pointer flex items-center gap-1.5"
                        :class="activeFilter === 'theme' ? 'bg-rose-600 text-white shadow-sm font-bold' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700'"
                    >
                        <span>{{ currentTheme.emoji }} Tema {{ currentTheme.label }} ({{ themeIdeas.length }})</span>
                    </button>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <div
                        v-for="item in filteredItems"
                        :key="item.id"
                        class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 hover:border-slate-700 transition-all space-y-3 flex flex-col justify-between group"
                    >
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border" :class="item.badgeColor">
                                    {{ item.badge }}
                                </span>
                                <span v-if="item.price" class="text-[11px] font-bold text-amber-300">
                                    {{ item.price }}
                                </span>
                            </div>

                            <h4 class="text-sm font-bold text-white group-hover:text-rose-300 transition-colors leading-snug">
                                {{ item.title }}
                            </h4>

                            <p v-if="item.notes" class="text-xs text-slate-400 line-clamp-2">
                                {{ item.notes }}
                            </p>
                        </div>

                        <!-- Direct Store Search Links -->
                        <div class="pt-2 border-t border-slate-800/80 space-y-2">
                            <!-- Direct URL from user's wishlist item if already entered -->
                            <a
                                v-if="item.url"
                                :href="item.url"
                                target="_blank"
                                rel="noopener noreferrer sponsored"
                                class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-sm transition-all"
                            >
                                <ExternalLink class="w-3.5 h-3.5" />
                                <span>Abrir Link Escolhido pelo Amigo</span>
                            </a>

                            <!-- Ready-to-search store options -->
                            <div class="grid grid-cols-3 gap-1.5 text-[11px]">
                                <!-- Shopee -->
                                <a
                                    :href="getStoreLinks(item.title).shopee"
                                    target="_blank"
                                    rel="noopener noreferrer sponsored"
                                    class="flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-[#EE4D2D]/15 hover:bg-[#EE4D2D]/25 border border-[#EE4D2D]/35 text-[#ff6b4a] font-bold transition-all text-center"
                                    title="Buscar na Shopee com cupons"
                                >
                                    <span>Shopee</span>
                                    <ExternalLink class="w-2.5 h-2.5" />
                                </a>

                                <!-- Mercado Livre -->
                                <a
                                    :href="getStoreLinks(item.title).mercadolivre"
                                    target="_blank"
                                    rel="noopener noreferrer sponsored"
                                    class="flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-amber-400/15 hover:bg-amber-400/25 border border-amber-400/35 text-amber-300 font-bold transition-all text-center"
                                    title="Buscar no Mercado Livre com Entrega Full"
                                >
                                    <span>M. Livre</span>
                                    <ExternalLink class="w-2.5 h-2.5" />
                                </a>

                                <!-- Amazon -->
                                <a
                                    :href="getStoreLinks(item.title).amazon"
                                    target="_blank"
                                    rel="noopener noreferrer sponsored"
                                    class="flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-sky-500/15 hover:bg-sky-500/25 border border-sky-500/35 text-sky-300 font-bold transition-all text-center"
                                    title="Buscar na Amazon com Prime"
                                >
                                    <span>Amazon</span>
                                    <ExternalLink class="w-2.5 h-2.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Big Direct Portals (Shopee & Mercado Livre) -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 border border-slate-800 space-y-3">
                    <div class="flex items-center gap-2">
                        <ShoppingBag class="w-4 h-4 text-amber-400" />
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                            Explorar Lojas Parceiras com Cupons & Frete Grátis
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a
                            v-if="shopeeDirectUrl"
                            :href="shopeeDirectUrl"
                            target="_blank"
                            rel="noopener noreferrer sponsored"
                            class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-[#EE4D2D] to-[#ff5722] text-white font-bold text-xs shadow-md shadow-[#ee4d2d]/20 transition-transform hover:scale-[1.01]"
                        >
                            <span class="flex items-center gap-2">
                                <ShoppingBag class="w-4 h-4" />
                                <span>Ver Cupons na Shopee</span>
                            </span>
                            <ExternalLink class="w-3.5 h-3.5" />
                        </a>

                        <a
                            v-if="mercadoLivreDirectUrl"
                            :href="mercadoLivreDirectUrl"
                            target="_blank"
                            rel="noopener noreferrer sponsored"
                            class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-[#FFE600] to-[#ffd700] text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition-transform hover:scale-[1.01]"
                        >
                            <span class="flex items-center gap-2">
                                <Truck class="w-4 h-4" />
                                <span>Ver Ofertas no Mercado Livre</span>
                            </span>
                            <ExternalLink class="w-3.5 h-3.5" />
                        </a>
                    </div>
                </div>

                <!-- Transparency Note -->
                <div class="flex items-start gap-2.5 text-[11px] text-slate-400 leading-relaxed pt-1">
                    <Info class="w-4 h-4 text-amber-400/80 shrink-0 mt-0.5" />
                    <span>
                        <strong class="text-slate-300">Transparência:</strong> Os links acima direcionam para lojas parceiras. Você paga o mesmo valor (aproveitando os mesmos descontos e cupons) e o GiftMatch recebe uma pequena comissão para manter o serviço 100% gratuito.
                    </span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-800 bg-slate-950/60 flex items-center justify-end">
                <button
                    @click="close"
                    type="button"
                    class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors cursor-pointer"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>
</template>
