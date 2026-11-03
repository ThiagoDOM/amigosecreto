<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { 
    BookOpen, 
    Search, 
    Clock, 
    ArrowRight, 
    Gift, 
    Sparkles, 
    Tag, 
    ArrowLeft,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    guides: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
    activeCategory: {
        type: String,
        default: null,
    },
    searchQuery: {
        type: String,
        default: '',
    },
});

const searchInput = ref(props.searchQuery);

function filterCategory(cat) {
    router.get(route('guides.index'), {
        categoria: cat || undefined,
        q: searchInput.value || undefined,
    }, { preserveState: true, replace: true });
}

function handleSearch() {
    router.get(route('guides.index'), {
        categoria: props.activeCategory || undefined,
        q: searchInput.value || undefined,
    }, { preserveState: true, replace: true });
}

function clearSearch() {
    searchInput.value = '';
    filterCategory(props.activeCategory);
}
</script>

<template>
    <Head>
        <title>Central de Guias & Dicas de Amigo Secreto - GiftMatch</title>
        <meta name="description" content="Aprenda como organizar um amigo secreto online de sucesso. Regras, ideias de presentes até R$ 50 e R$ 100, Amigo da Onça, Amigo Caneca, brincadeiras e muito mais." />
        <meta name="keywords" content="amigo secreto online, como organizar amigo secreto, presentes amigo secreto, regras amigo secreto, amigo da onca, amigo chocolate" />
        <link rel="canonical" href="https://giftmatch.online/guias" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Central de Guias & Dicas de Amigo Secreto - GiftMatch" />
        <meta property="og:description" content="Artigos completos, ideias de presentes, regras e brincadeiras para fazer um amigo secreto perfeito em 2026." />
        <meta property="og:url" content="https://giftmatch.online/guias" />
        <meta property="og:site_name" content="GiftMatch" />
    </Head>

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-rose-500 selection:text-white flex flex-col justify-between">
        <!-- Ambient lighting -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[380px] bg-gradient-to-b from-rose-600/15 via-rose-500/5 to-transparent rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Top Navigation -->
        <header class="sticky top-0 z-40 bg-slate-950/85 backdrop-blur-md border-b border-slate-800/80">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <Link :href="route('home')" class="transition-transform hover:scale-[1.02]">
                    <ApplicationLogo />
                </Link>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        <span class="hidden sm:inline">Página Inicial</span>
                    </Link>

                    <Link
                        :href="route('register')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-900/30 transition-all hover:scale-[1.02]"
                    >
                        <Gift class="w-3.5 h-3.5" />
                        <span>Criar Sorteio Grátis</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-14 space-y-10 flex-1 w-full">
            <!-- Hero Section -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold">
                    <BookOpen class="w-3.5 h-3.5" />
                    <span>Central de Guias & Inspirações</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black font-display text-white tracking-tight">
                    Dicas, Regras e Ideias para o seu <span class="bg-gradient-to-r from-rose-400 via-rose-500 to-amber-400 bg-clip-text text-transparent">Amigo Secreto</span>
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto">
                    Tudo o que você precisa saber para organizar o sorteio perfeito, evitar gafes, escolher os melhores presentes e animar a confraternização.
                </p>

                <!-- Search & Filters -->
                <div class="pt-4 max-w-xl mx-auto">
                    <form @submit.prevent="handleSearch" class="relative flex items-center">
                        <Search class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" />
                        <input
                            v-model="searchInput"
                            type="text"
                            placeholder="Buscar guias (ex: presentes até 50 reais, casais, WhatsApp...)"
                            class="w-full pl-11 pr-24 py-3 bg-slate-900/90 border border-slate-800 rounded-2xl text-xs sm:text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all shadow-inner"
                        />
                        <button
                            type="submit"
                            class="absolute right-2 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 transition-colors"
                        >
                            Buscar
                        </button>
                    </form>
                </div>

                <!-- Category Pills -->
                <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                    <button
                        @click="filterCategory(null)"
                        type="button"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all"
                        :class="!activeCategory 
                            ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' 
                            : 'bg-slate-900 text-slate-400 border border-slate-800 hover:border-slate-700 hover:text-white'"
                    >
                        Todos ({{ guides.length }})
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="filterCategory(cat)"
                        type="button"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all"
                        :class="activeCategory === cat 
                            ? 'bg-rose-600 text-white shadow-md shadow-rose-900/30' 
                            : 'bg-slate-900 text-slate-400 border border-slate-800 hover:border-slate-700 hover:text-white'"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <!-- Articles Grid -->
            <div v-if="guides.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                <article
                    v-for="guide in guides"
                    :key="guide.slug"
                    class="group relative flex flex-col justify-between p-6 rounded-3xl bg-slate-900/70 border border-slate-800/80 hover:border-rose-500/40 hover:bg-slate-900 transition-all duration-300 shadow-lg hover:shadow-2xl hover:shadow-rose-950/20 hover:-translate-y-1"
                >
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold bg-rose-500/10 text-rose-300 border border-rose-500/20">
                                <Tag class="w-3 h-3" />
                                <span>{{ guide.category }}</span>
                            </span>
                            <span class="text-[11px] text-slate-500 flex items-center gap-1 font-medium">
                                <Clock class="w-3 h-3 text-slate-500" />
                                <span>{{ guide.read_time }}</span>
                            </span>
                        </div>

                        <h2 class="text-lg font-bold font-display text-white group-hover:text-rose-300 transition-colors line-clamp-2">
                            <Link :href="route('guides.show', guide.slug)" class="focus:outline-none">
                                {{ guide.title }}
                            </Link>
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-400 leading-relaxed line-clamp-3">
                            {{ guide.excerpt }}
                        </p>
                    </div>

                    <div class="pt-5 mt-4 border-t border-slate-800/60 flex items-center justify-between text-xs">
                        <span class="text-[11px] text-slate-500">
                            Atualizado em {{ guide.updated_at }}
                        </span>
                        <Link
                            :href="route('guides.show', guide.slug)"
                            class="inline-flex items-center gap-1 font-bold text-rose-400 group-hover:text-rose-300 transition-transform group-hover:translate-x-1"
                        >
                            <span>Ler artigo</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </article>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 p-8 rounded-3xl bg-slate-900/50 border border-slate-800 space-y-4">
                <BookOpen class="w-12 h-12 text-slate-600 mx-auto" />
                <h3 class="text-lg font-bold text-white">Nenhum guia encontrado</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Não encontramos artigos com os termos pesquisados. Tente outra palavra-chave ou limpe os filtros.
                </p>
                <button
                    @click="clearSearch"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 transition-colors"
                >
                    Limpar Filtros
                </button>
            </div>

            <!-- Bottom CTA Banner -->
            <div class="rounded-3xl bg-gradient-to-br from-rose-950/40 via-slate-900/90 to-amber-950/40 border border-rose-500/30 p-8 sm:p-10 shadow-2xl text-center sm:text-left sm:flex sm:items-center sm:justify-between gap-8 mt-12">
                <div class="space-y-2 max-w-xl">
                    <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        <Sparkles class="w-3.5 h-3.5 text-amber-400" />
                        <span>Sorteador Gratuito e Sem Senhas</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold font-display text-white">
                        Pronto para organizar o seu Amigo Secreto?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Crie seu grupo em 1 minuto: defina regras para casais não se tirarem, monte listas de desejos e envie as revelações direto no WhatsApp. 100% gratuito.
                    </p>
                </div>
                <div class="shrink-0 pt-4 sm:pt-0">
                    <Link
                        :href="route('register')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 shadow-xl shadow-rose-900/40 transition-all hover:scale-[1.02] active:scale-95"
                    >
                        <Gift class="w-4 h-4" />
                        <span>Criar Evento Grátis</span>
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-900 bg-slate-950 py-8 px-4 text-center text-xs text-slate-500 space-y-2">
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <Link :href="route('home')" class="hover:text-rose-400 transition-colors">Início</Link>
                <span>&bull;</span>
                <Link :href="route('guides.index')" class="text-rose-400 font-semibold">Guias & Dicas</Link>
                <span>&bull;</span>
                <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
                <span>&bull;</span>
                <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
            </div>
            <p>&copy; 2026 GiftMatch. Todos os direitos reservados.</p>
        </footer>
    </div>
</template>

