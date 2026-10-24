<script setup>
import { computed } from 'vue';
import { parseWishlist } from '@/Utils/formatters';
import { Gift, ExternalLink, Info } from 'lucide-vue-next';

const props = defineProps({
    wishlist: {
        type: [String, Array],
        default: () => [],
    },
});

const items = computed(() => {
    return parseWishlist(props.wishlist);
});
</script>

<template>
    <div class="space-y-2">
        <div v-if="items.length === 0" class="text-xs text-slate-400 py-3 text-center font-medium">
            Nenhum presente ou link especificado.
        </div>

        <div v-else class="space-y-2">
            <div
                v-for="(item, idx) in items"
                :key="idx"
                class="p-3.5 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-2 transition-all hover:border-rose-500/40"
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

                <div v-if="item.url" class="self-start sm:self-center flex-shrink-0">
                    <a
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/60 border border-rose-800/60 hover:bg-rose-900/60 transition-colors shadow-sm"
                    >
                        <span>Ver no site</span>
                        <ExternalLink class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
