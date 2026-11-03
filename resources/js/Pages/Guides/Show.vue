<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { 
    BookOpen, 
    Clock, 
    Calendar, 
    ArrowLeft, 
    ArrowRight, 
    Gift, 
    Sparkles, 
    Tag, 
    Share2, 
    Check, 
    Copy, 
    MessageCircle,
    ListTree,
    ChevronRight,
    UserCheck
} from 'lucide-vue-next';

const props = defineProps({
    guide: {
        type: Object,
        required: true,
    },
    related: {
        type: Array,
        default: () => [],
    },
});

const copiedLink = ref(false);

function copyArticleUrl() {
    if (typeof window !== 'undefined' && navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href);
        copiedLink.value = true;
        setTimeout(() => {
            copiedLink.value = false;
        }, 2000);
    }
}

function shareWhatsApp() {
    if (typeof window === 'undefined') return;
    const text = encodeURIComponent(`Veja esse guia: "${props.guide.title}"\n${window.location.href}`);
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
}

const currentUrl = typeof window !== 'undefined' ? window.location.href : `https://giftmatch.online/guias/${props.guide.slug}`;

const articleSchema = {
    '@context': 'https://schema.org',
    '@type': 'Article',
    'headline': props.guide.title,
    'description': props.guide.meta_description,
    'inLanguage': 'pt-BR',
    'mainEntityOfPage': {
        '@type': 'WebPage',
        '@id': `https://giftmatch.online/guias/${props.guide.slug}`,
    },
    'datePublished': props.guide.published_at,
    'dateModified': props.guide.updated_at,
    'author': {
        '@type': 'Organization',
        'name': 'Equipe Editorial GiftMatch',
        'url': 'https://giftmatch.online',
    },
    'publisher': {
        '@type': 'Organization',
        'name': 'GiftMatch',
        'logo': {
            '@type': 'ImageObject',
            'url': 'https://giftmatch.online/favicon.png',
        },
    },
};

const breadcrumbSchema = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    'itemListElement': [
        {
            '@type': 'ListItem',
            'position': 1,
            'name': 'Início',
            'item': 'https://giftmatch.online',
        },
        {
            '@type': 'ListItem',
            'position': 2,
            'name': 'Guias & Dicas',
            'item': 'https://giftmatch.online/guias',
        },
        {
            '@type': 'ListItem',
            'position': 3,
            'name': props.guide.title,
            'item': `https://giftmatch.online/guias/${props.guide.slug}`,
        },
    ],
};
</script>

<template>
    <Head>
        <title>{{ guide.meta_title ? guide.meta_title + ' - GiftMatch' : guide.title + ' - GiftMatch' }}</title>
        <meta name="description" :content="guide.meta_description" />
        <meta v-if="guide.keywords && guide.keywords.length" name="keywords" :content="guide.keywords.join(', ')" />
        <link rel="canonical" :href="'https://giftmatch.online/guias/' + guide.slug" />
        
        <!-- OpenGraph -->
        <meta property="og:type" content="article" />
        <meta property="og:title" :content="guide.title + ' - GiftMatch'" />
        <meta property="og:description" :content="guide.meta_description" />
        <meta property="og:url" :content="'https://giftmatch.online/guias/' + guide.slug" />
        <meta property="og:site_name" content="GiftMatch" />
        <meta property="article:published_time" :content="guide.published_at" />
        <meta property="article:modified_time" :content="guide.updated_at" />
        <meta property="article:section" :content="guide.category" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="guide.title + ' - GiftMatch'" />
        <meta name="twitter:description" :content="guide.meta_description" />

        <!-- Schema.org JSON-LD -->
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(articleSchema) }}
        </component>
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(breadcrumbSchema) }}
        </component>
    </Head>

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-rose-500 selection:text-white flex flex-col justify-between">
        <!-- Ambient lighting -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[850px] h-[350px] bg-gradient-to-b from-rose-600/10 via-rose-500/5 to-transparent rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Top Navigation -->
        <header class="sticky top-0 z-40 bg-slate-950/85 backdrop-blur-md border-b border-slate-800/80">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <Link :href="route('home')" class="transition-transform hover:scale-[1.02]">
                    <ApplicationLogo />
                </Link>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('guides.index')"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        <span>Todos os Guias</span>
                    </Link>

                    <Link
                        :href="route('register')"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-900/30 transition-all hover:scale-[1.02]"
                    >
                        <Gift class="w-3.5 h-3.5" />
                        <span>Criar Sorteio Grátis</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-8 flex-1 w-full">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-400 overflow-x-auto pb-1" aria-label="Breadcrumb">
                <Link :href="route('home')" class="hover:text-rose-400 transition-colors shrink-0">Início</Link>
                <ChevronRight class="w-3.5 h-3.5 text-slate-600 shrink-0" />
                <Link :href="route('guides.index')" class="hover:text-rose-400 transition-colors shrink-0">Guias</Link>
                <ChevronRight class="w-3.5 h-3.5 text-slate-600 shrink-0" />
                <span class="text-rose-300 font-medium shrink-0">{{ guide.category }}</span>
            </nav>

            <!-- Article Header -->
            <header class="space-y-4 border-b border-slate-800/80 pb-6">
                <div class="flex flex-wrap items-center gap-2.5 text-xs">
                    <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full font-bold bg-rose-500/15 text-rose-300 border border-rose-500/25">
                        <Tag class="w-3 h-3" />
                        <span>{{ guide.category }}</span>
                    </span>
                    <span class="text-slate-400 flex items-center gap-1">
                        <Clock class="w-3.5 h-3.5 text-slate-500" />
                        <span>{{ guide.read_time }}</span>
                    </span>
                    <span class="text-slate-500">&bull;</span>
                    <span class="text-slate-400 flex items-center gap-1">
                        <Calendar class="w-3.5 h-3.5 text-slate-500" />
                        <span>Atualizado em {{ guide.updated_at }}</span>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black font-display text-white tracking-tight leading-tight">
                    {{ guide.title }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    {{ guide.excerpt }}
                </p>

                <!-- Author & Share Action Bar -->
                <div class="pt-2 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-400">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-500 to-amber-500 text-white flex items-center justify-center font-bold text-xs shadow-md">
                            GM
                        </div>
                        <div>
                            <span class="font-bold text-white block">Equipe Editorial GiftMatch</span>
                            <span class="text-[11px] text-slate-500">Revisado por especialistas em eventos</span>
                        </div>
                    </div>

                    <!-- Share buttons -->
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="shareWhatsApp"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-300 bg-emerald-950/60 hover:bg-emerald-900 border border-emerald-800/60 transition-colors cursor-pointer"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>WhatsApp</span>
                        </button>

                        <button
                            type="button"
                            @click="copyArticleUrl"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-colors cursor-pointer"
                        >
                            <Check v-if="copiedLink" class="w-3.5 h-3.5 text-emerald-400" />
                            <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ copiedLink ? 'Copiado!' : 'Copiar' }}</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Table of Contents -->
            <div v-if="guide.table_of_contents && guide.table_of_contents.length" class="p-5 sm:p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-rose-400">
                    <ListTree class="w-4 h-4" />
                    <span>Neste Artigo:</span>
                </div>
                <ul class="space-y-1.5 text-xs sm:text-sm text-slate-300 pl-1">
                    <li v-for="item in guide.table_of_contents" :key="item.id">
                        <a 
                            :href="'#' + item.id" 
                            class="hover:text-rose-400 transition-colors underline decoration-slate-700 underline-offset-4"
                        >
                            {{ item.title }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Article Prose Content -->
            <article class="prose prose-invert max-w-none text-slate-300 text-sm sm:text-base leading-relaxed space-y-4">
                <div v-html="guide.content"></div>
            </article>

            <!-- Mid/Bottom Call to Action Box -->
            <div class="rounded-3xl bg-gradient-to-br from-rose-950/40 via-slate-900 to-amber-950/40 border border-rose-500/30 p-6 sm:p-8 shadow-2xl space-y-4 text-center sm:text-left sm:flex sm:items-center sm:justify-between sm:space-y-0 gap-6 my-10">
                <div class="space-y-2 max-w-lg">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        <Sparkles class="w-3.5 h-3.5 text-amber-400" />
                        <span>Sorteador Gratuito</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold font-display text-white">
                        Chega de papelzinho: Crie seu Amigo Secreto no GiftMatch
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Defina restrições para casais não se tirarem, lista de presentes com sugestões de lojas e envie tudo com 1 clique no WhatsApp. 100% gratuito.
                    </p>
                </div>
                <div class="shrink-0">
                    <Link
                        :href="route('register')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 shadow-xl shadow-rose-900/40 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer"
                    >
                        <Gift class="w-4 h-4" />
                        <span>Criar Sorteio Agora</span>
                    </Link>
                </div>
            </div>

            <!-- Related Articles -->
            <section v-if="related && related.length" class="space-y-4 pt-6 border-t border-slate-800/80">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <BookOpen class="w-4 h-4 text-rose-400" />
                        <span>Artigos Relacionados</span>
                    </h3>
                    <Link :href="route('guides.index')" class="text-xs text-rose-400 hover:text-rose-300 font-semibold">
                        Ver todos &rarr;
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <article
                        v-for="rel in related"
                        :key="rel.slug"
                        class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-rose-500/40 transition-all space-y-2 flex flex-col justify-between"
                    >
                        <div class="space-y-1.5">
                            <span class="text-[10px] uppercase font-bold text-rose-400">
                                {{ rel.category }}
                            </span>
                            <h4 class="text-xs font-bold text-white line-clamp-2 hover:text-rose-300">
                                <Link :href="route('guides.show', rel.slug)">
                                    {{ rel.title }}
                                </Link>
                            </h4>
                        </div>
                        <Link
                            :href="route('guides.show', rel.slug)"
                            class="text-[11px] font-bold text-slate-400 hover:text-white inline-flex items-center gap-1 pt-1"
                        >
                            <span>Ler</span>
                            <ArrowRight class="w-3 h-3 text-rose-400" />
                        </Link>
                    </article>
                </div>
            </section>
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
