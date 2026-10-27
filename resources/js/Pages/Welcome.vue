<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const CookieConsent = defineAsyncComponent(() => import('@/Components/CookieConsent.vue'));
const GoogleAd = defineAsyncComponent(() => import('@/Components/GoogleAd.vue'));
const AffiliateRecommendations = defineAsyncComponent(() => import('@/Components/AffiliateRecommendations.vue'));
import { 
    Gift, 
    ArrowRight, 
    Share2, 
    Sliders, 
    PartyPopper, 
    CheckCircle2, 
    ShieldCheck, 
    Smartphone,
    Users,
    Calendar,
    MessageCircle,
    Check,
    LogOut,
    Copy,
    AlertTriangle,
    X,
    Lock,
    Bell,
    ShoppingBag,
    HelpCircle,
    ChevronDown,
    ChevronUp
} from 'lucide-vue-next';

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    activeParticipations: {
        type: Array,
        default: () => [],
    },
});

const inviteCodeInput = ref('');
const showForgetModal = ref(false);
const selectedParticipation = ref(null);
const copiedModalLink = ref(false);
const isDisconnecting = ref(false);

const openFaqIndex = ref(null);

function toggleFaq(index) {
    openFaqIndex.value = openFaqIndex.value === index ? null : index;
}

const faqs = [
    {
        q: 'O GiftMatch envia mensagens automáticas pelo WhatsApp?',
        a: 'Não. O GiftMatch não envia mensagens em massa e não pede acesso ao seu WhatsApp pessoal. Em vez disso, fornecemos botões práticos que abrem o seu próprio WhatsApp com a mensagem personalizada e o link exclusivo prontos para você apenas tocar em "Enviar" para cada amigo ou no grupo.',
    },
    {
        q: 'Os participantes precisam baixar aplicativo ou criar conta com senha?',
        a: 'Zero aplicativos e zero senhas para convidados! Apenas quem organiza cria uma conta simples para gerenciar o evento. Os convidados só precisam clicar no link de convite, preencher o nome e cadastrar seus presentes desejados. Tudo funciona direto no navegador do celular ou computador.',
    },
    {
        q: 'O organizador consegue ver quem tirou quem?',
        a: 'Para preservar a surpresa da brincadeira, cada participante recebe um link individual e exclusivo com sua revelação. No painel de administração, o organizador tem acesso aos links de cada amigo (caso alguém perca o acesso) e aos dados de suporte, mantendo a experiência segura e preservando o segredo até a revelação!',
    },
    {
        q: 'Como funciona o Bilhete Secreto anônimo?',
        a: 'Depois do sorteio, quem tirou um amigo pode enviar perguntas ou bilhetes sem se identificar (por exemplo: "qual cor você prefere?" ou "tem preferência de marca?"). O sorteado recebe a pergunta em sua tela secreta e responde ali mesmo, mantendo o anonimato garantido e ajudando a acertar no presente!',
    },
    {
        q: 'Como funcionam os Temas de Amigo Secreto?',
        a: 'Você pode criar um evento Tradicional ou temático: Amigo Chocolate, Amigo Caneca, Amigo Livro, Amigo Chinelo ou Amigo da Onça (troll/divertido). O tema é exibido no convite e oferece sugestões inteligentes de presentes sob medida para o orçamento do grupo.',
    },
    {
        q: 'O GiftMatch é realmente 100% gratuito?',
        a: 'Sim, totalmente gratuito! Você pode criar quantos grupos quiser, adicionar participantes ilimitados, definir restrições para casais, realizar sorteios e usar as listas de desejos sem pagar absolutamente nada.',
    },
];

function goToInvite() {
    if (inviteCodeInput.value.trim()) {
        router.visit(route('invite.show', { code: inviteCodeInput.value.trim().toUpperCase() }));
    }
}

function openForgetModal(p) {
    selectedParticipation.value = p;
    copiedModalLink.value = false;
    showForgetModal.value = true;
}

async function copyToClipboard(text) {
    if (typeof window !== 'undefined' && navigator.clipboard && navigator.clipboard.writeText) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {}
    }
    try {
        const input = document.createElement('textarea');
        input.value = text;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        return true;
    } catch (e) {
        return false;
    }
}

function handleCopyModalLink() {
    if (!selectedParticipation.value) return;
    copyToClipboard(selectedParticipation.value.magic_url);
    copiedModalLink.value = true;
    setTimeout(() => {
        copiedModalLink.value = false;
    }, 2500);
}

function handleConfirmForget() {
    if (!selectedParticipation.value) return;
    isDisconnecting.value = true;
    router.post(route('participant.session.forget'), {
        token: selectedParticipation.value.token
    }, {
        onFinish: () => {
            isDisconnecting.value = false;
            showForgetModal.value = false;
            selectedParticipation.value = null;
        }
    });
}
</script>

<template>
    <Head>
        <title>GiftMatch - Amigo Secreto Online Inteligente</title>
        <meta name="description" content="Organize seu Amigo Secreto no GiftMatch de forma simples, rápida e 100% gratuita! Sorteio automático sem ninguém tirar a si mesmo, regras para casais, lista de desejos e envio direto no WhatsApp." />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="GiftMatch" />
        <meta property="og:title" content="GiftMatch - Amigo Secreto Online Inteligente" />
        <meta property="og:description" content="Organize seu Amigo Secreto no GiftMatch de forma simples, rápida e 100% gratuita! Sorteio automático com regras para casais, lista de desejos e envio direto no WhatsApp." />
        <meta property="og:image" content="/og-home.jpg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="GiftMatch - Amigo Secreto Online Inteligente" />
        <meta name="twitter:description" content="Organize seu Amigo Secreto no GiftMatch de forma simples, rápida e 100% gratuita! Sorteio automático com regras para casais, lista de desejos e envio direto no WhatsApp." />
        <meta name="twitter:image" content="/og-home.jpg" />
    </Head>

    <CookieConsent />

    <div class="min-h-screen bg-slate-950 text-slate-100 selection:bg-rose-500 selection:text-white font-sans antialiased relative overflow-hidden flex flex-col justify-between">
        <!-- Ambient lighting (subtle & elegant) -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-rose-600/15 via-rose-500/5 to-transparent rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute bottom-10 -left-40 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Header / Navigation -->
        <header class="relative z-20 border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md sticky top-0">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-2 transition-transform hover:scale-[1.02]">
                    <ApplicationLogo />
                </Link>

                <nav class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-rose-600 hover:bg-rose-500 text-white transition-all shadow-md shadow-rose-900/30"
                        >
                            <span>Meu Painel</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-300 hover:text-white transition-colors"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-900/30 transition-all hover:scale-[1.02]"
                        >
                            <span>Criar Conta</span>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-grow">
            <!-- Hero Section -->
            <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-20 sm:pt-16 sm:pb-24 text-center">
                <!-- Active Participant Sessions Detected -->
                <div v-if="activeParticipations && activeParticipations.length > 0" class="mb-10 max-w-2xl mx-auto text-left space-y-3">
                    <div
                        v-for="p in activeParticipations"
                        :key="p.token"
                        class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-rose-950/70 via-slate-900/90 to-amber-950/50 border border-rose-500/35 p-5 shadow-xl shadow-rose-950/40 backdrop-blur-md"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    <CheckCircle2 class="w-3 h-3 text-rose-400" />
                                    <span>Sua Inscrição Ativa</span>
                                </div>
                                <h3 class="text-base sm:text-lg font-bold text-white flex flex-wrap items-center gap-1.5">
                                    <span>Você está participando do evento:</span>
                                    <span class="text-rose-400 font-extrabold underline decoration-rose-500/40 underline-offset-4">{{ p.event_title }}</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-300">
                                    Inscrito como <strong class="text-white font-semibold">{{ p.participant_name }}</strong>
                                    <span v-if="p.event_date" class="text-slate-400"> &bull; Festa: {{ p.event_date }}</span>
                                    <span v-else-if="p.draw_date" class="text-slate-400"> &bull; Sorteio: {{ p.draw_date }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <Link
                                    :href="p.magic_url"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-rose-600 hover:bg-rose-500 text-white transition-all shadow-md shadow-rose-900/30 hover:scale-[1.02]"
                                >
                                    <Gift class="w-4 h-4" />
                                    <span>Acessar Meu Amigo</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>

                                <button
                                    @click="openForgetModal(p)"
                                    type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-rose-300 bg-slate-900/80 hover:bg-rose-950/40 border border-slate-800 hover:border-rose-800/50 transition-all cursor-pointer"
                                    title="Sair deste dispositivo"
                                >
                                    <LogOut class="w-3.5 h-3.5" />
                                    <span>Sair</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-slate-300 text-xs font-semibold mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Sorteio de Amigo Secreto Online 100% Gratuito</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-display tracking-tight text-white leading-tight max-w-4xl mx-auto">
                    Organize o seu sorteio de final de ano <span class="inline-block pb-1 bg-gradient-to-r from-rose-400 via-rose-300 to-amber-300 bg-clip-text text-transparent">sem complicações</span>
                </h1>

                <!-- Subtitle -->
                <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto font-normal">
                    Crie o evento, envie o link de convite no WhatsApp e deixe cada amigo cadastrar sua lista de presentes. Simples, rápido e sem exigir cadastro dos participantes.
                </p>

                <!-- Primary CTAs -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5 max-w-md mx-auto sm:max-w-none">
                    <Link
                        :href="$page.props.auth.user ? route('events.create') : route('register')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-xl text-sm sm:text-base font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-lg shadow-rose-600/25 transition-all hover:scale-[1.01]"
                    >
                        <Gift class="w-5 h-5" />
                        <span>Criar Amigo Secreto Grátis</span>
                    </Link>

                    <Link
                        :href="route('simulation')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm sm:text-base font-bold text-amber-300 hover:text-white bg-slate-900/90 hover:bg-slate-800 border border-amber-500/30 hover:border-amber-400/50 shadow-md transition-all hover:scale-[1.01]"
                    >
                        <Gift class="w-4 h-4 text-amber-400" />
                        <span>Simulação Interativa</span>
                    </Link>

                    <a
                        href="#como-funciona"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm sm:text-base font-semibold text-slate-300 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors"
                    >
                        <span>Como funciona</span>
                    </a>
                </div>

                <!-- Fast Join by Invite Code -->
                <div class="mt-10 max-w-md mx-auto">
                    <form @submit.prevent="goToInvite" class="relative flex items-center">
                        <input
                            v-model="inviteCodeInput"
                            type="text"
                            placeholder="Tem um código de convite? Ex: AS8K92"
                            class="w-full pl-4 pr-24 py-3 rounded-xl bg-slate-900/90 border border-slate-800 text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 uppercase tracking-wider font-mono transition-all"
                        />
                        <button
                            type="submit"
                            :disabled="!inviteCodeInput.trim()"
                            class="absolute right-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 disabled:opacity-40 transition-all flex items-center gap-1"
                        >
                            <span>Entrar</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </button>
                    </form>
                </div>

                <!-- Value Highlights -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-xs font-semibold text-slate-300">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                        <span>100% Gratuito</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800">
                        <Smartphone class="w-4 h-4 text-sky-400 shrink-0" />
                        <span>Zero Aplicativos para Baixar</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800">
                        <MessageCircle class="w-4 h-4 text-emerald-400 shrink-0" />
                        <span>Encaminhamento Fácil no WhatsApp</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800">
                        <ShoppingBag class="w-4 h-4 text-amber-400 shrink-0" />
                        <span>Lista com Links e Tamanhos</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 border border-slate-800">
                        <Sliders class="w-4 h-4 text-rose-400 shrink-0" />
                        <span>Regras para Casais</span>
                    </div>
                </div>
            </section>

            <!-- Feature Cards Section -->
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 mb-3">
                        Diferenciais do GiftMatch
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold font-display text-white">
                        Tudo pensado para o sorteio perfeito
                    </h2>
                    <p class="mt-2 text-slate-400 text-xs sm:text-sm">
                        Simplicidade para quem organiza e uma experiência incrível para cada participante.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Feature 1: WhatsApp -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <MessageCircle class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Encaminhamento Fácil no WhatsApp</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Nada de sistemas que cobram ou pedem acesso ao seu WhatsApp. O GiftMatch gera botões que abrem mensagens já formatadas para você encaminhar em 1 clique para o grupo ou em particular.
                        </p>
                    </div>

                    <!-- Feature 2: Wishlist with Links & Sizes -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                            <ShoppingBag class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Lista com Links e Tamanhos</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Chega de errar no presente! Cada amigo cadastra links reais de lojas (Shopee, Mercado Livre, Amazon), tamanhos de calçado e roupa, o que mais adora e preferências para evitar.
                        </p>
                    </div>

                    <!-- Feature 3: Anonymous Secret Notes -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                            <Lock class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Bilhete Secreto 100% Anônimo</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Ficou com dúvida sobre uma cor, tamanho ou gosto? Mande uma pergunta anônima para quem você tirou. Ele(a) responde na tela dele(a) sem jamais descobrir quem tirou ele(a).
                        </p>
                    </div>

                    <!-- Feature 4: Themed Secret Santa -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <Gift class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Amigo Secreto Temático</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Tradicional, Amigo Chocolate, Caneca, Livro, Chinelo ou Amigo da Onça! Escolha o tema do evento e conte com um botão exclusivo que abre as sugestões de presentes ideais.
                        </p>
                    </div>

                    <!-- Feature 5: Zero Apps & Zero Passwords -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                            <Smartphone class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Zero Apps & Zero Burocracia</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Os participantes não precisam baixar nada na Play Store nem criar senhas chatas que todo mundo esquece. Tudo funciona 100% no navegador de qualquer celular ou computador.
                        </p>
                    </div>

                    <!-- Feature 6: Couple Restrictions & Safety -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-slate-900/80 border border-slate-800 transition-all hover:border-slate-700 space-y-3.5">
                        <div class="w-11 h-11 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                            <Sliders class="w-5 h-5" />
                        </div>
                        <h3 class="text-base sm:text-lg font-bold font-display text-white">Regras para Casais & Famílias</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Defina com apenas 1 clique quem não pode tirar quem (namorados, maridos e esposas, irmãos). Nosso algoritmo garante um sorteio matematicamente válido sem falhas.
                        </p>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="como-funciona" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
                <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                    <h2 class="text-2xl sm:text-3xl font-extrabold font-display text-white">
                        Como funciona em 3 passos
                    </h2>
                    <p class="mt-2 text-slate-400 text-xs sm:text-sm">
                        O organizador cria o grupo e todo o processo flui naturalmente.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Step 1 -->
                    <div class="p-6 rounded-2xl bg-slate-900/50 border border-slate-800 text-center flex flex-col items-center space-y-3">
                        <div class="w-10 h-10 rounded-full bg-rose-600 text-white font-bold text-sm flex items-center justify-center shadow-md shadow-rose-900/40">
                            1
                        </div>
                        <h3 class="text-base font-bold text-white">Cadastre o Evento</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Defina o nome do grupo, tema (ex: chocolate, tradicional), valor sugerido, data e local da festa.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-6 rounded-2xl bg-slate-900/50 border border-slate-800 text-center flex flex-col items-center space-y-3">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-950 font-bold text-sm flex items-center justify-center shadow-md shadow-amber-900/40">
                            2
                        </div>
                        <h3 class="text-base font-bold text-white">Encaminhe o Link no WhatsApp</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Envie o convite pelo botão pronto. Cada amigo entra direto pelo navegador e cadastra seus presentes favoritos.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-6 rounded-2xl bg-slate-900/50 border border-slate-800 text-center flex flex-col items-center space-y-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold text-sm flex items-center justify-center shadow-md shadow-emerald-900/40">
                            3
                        </div>
                        <h3 class="text-base font-bold text-white">Sorteie & Compartilhe</h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            O sistema sorteia respeitando as restrições e disponibiliza os links de revelação protegidos para cada participante.
                        </p>
                    </div>
                </div>

                <!-- Call to action card -->
                <div class="mt-14 p-8 sm:p-10 rounded-2xl bg-slate-900 border border-slate-800 text-center space-y-5">
                    <h3 class="text-xl sm:text-2xl font-bold font-display text-white">
                        Pronto para organizar seu amigo secreto?
                    </h3>
                    <p class="text-slate-400 text-xs sm:text-sm max-w-xl mx-auto">
                        Crie seu grupo gratuitamente em menos de 2 minutos e comece a convidar os amigos.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-1">
                        <Link
                            :href="$page.props.auth.user ? route('events.create') : route('register')"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-900/30 transition-all hover:scale-[1.01]"
                        >
                            <Gift class="w-4 h-4" />
                            <span>Começar Agora Gratuitamente</span>
                        </Link>
                        <Link
                            :href="route('simulation')"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-amber-300 hover:text-white bg-slate-800/90 hover:bg-slate-800 border border-amber-500/30 hover:border-amber-400/60 shadow-md shadow-amber-950/20 transition-all hover:scale-[1.01]"
                        >
                            <Gift class="w-4 h-4 text-amber-400" />
                            <span>Simular como Convidado</span>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- FAQ (Perguntas Frequentes) Section -->
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">
                        <HelpCircle class="w-3.5 h-3.5" />
                        <span>Dúvidas Frequentes</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold font-display text-white">
                        Perguntas e Respostas
                    </h2>
                    <p class="mt-2 text-slate-400 text-xs sm:text-sm">
                        Tudo o que você precisa saber sobre como funciona o GiftMatch.
                    </p>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(faq, idx) in faqs"
                        :key="idx"
                        class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden transition-colors"
                        :class="{ 'border-slate-700': openFaqIndex === idx }"
                    >
                        <button
                            @click="toggleFaq(idx)"
                            type="button"
                            class="w-full p-4 sm:p-5 flex items-center justify-between text-left gap-4 cursor-pointer hover:bg-slate-800/30 transition-colors"
                        >
                            <span class="text-sm sm:text-base font-bold text-white">
                                {{ faq.q }}
                            </span>
                            <ChevronDown
                                class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                :class="{ 'rotate-180 text-rose-400': openFaqIndex === idx }"
                            />
                        </button>

                        <div
                            v-show="openFaqIndex === idx"
                            class="px-4 pb-5 sm:px-5 sm:pb-6 text-xs sm:text-sm text-slate-300 leading-relaxed font-normal border-t border-slate-800/60 pt-3 animate-in fade-in"
                        >
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </section>

            <!-- Affiliate Partner Deals (Shopee & Mercado Livre) -->
            <div class="max-w-4xl mx-auto px-4 w-full mt-8">
                <AffiliateRecommendations
                    title="Ideias e Ofertas de Presentes para Amigo Secreto"
                    subtitle="Garanta seu presente com descontos exclusivos, cupons e frete grátis nas maiores lojas do Brasil:"
                />
            </div>

            <!-- Google AdSense Native Banner -->
            <div class="max-w-4xl mx-auto px-4 w-full">
                <GoogleAd slot-id="home-bottom" />
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-800/80 py-6 text-xs text-slate-500 bg-slate-950">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <ApplicationLogo />
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-slate-400">
                    <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
                    <span>&bull;</span>
                    <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
                    <span>&bull;</span>
                    <span>GiftMatch &bull; Amigo Secreto Online sem complicações</span>
                </div>
            </div>
        </footer>

        <!-- Modal de Confirmação para Sair da Sessão -->
        <div
            v-if="showForgetModal && selectedParticipation"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in"
        >
            <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-700/80 p-6 shadow-2xl text-left space-y-5">
                <!-- Close button -->
                <button
                    @click="showForgetModal = false"
                    class="absolute top-4 right-4 p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                >
                    <X class="w-5 h-5" />
                </button>

                <!-- Header -->
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                        <AlertTriangle class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">
                            Atenção: Guarde seu link antes de sair!
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Evento: <strong class="text-slate-200">{{ selectedParticipation.event_title }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Body / Warning -->
                <div class="space-y-3 text-xs sm:text-sm text-slate-300 leading-relaxed bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                    <p>
                        Você não possui senha cadastrada. Para acessar novamente o seu amigo secreto e visualizar os presentes neste ou em outro aparelho, <strong>você precisará deste link exclusivo</strong>.
                    </p>
                    <p class="text-rose-300/90 font-medium">
                        Se você sair sem copiar o link, não conseguirá abrir seu amigo secreto a não ser que peça o link novamente para o organizador.
                    </p>
                </div>

                <!-- Link Copy Box -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Seu Link Exclusivo de Acesso:
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            :value="selectedParticipation.magic_url"
                            class="flex-1 px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none select-all"
                        />
                        <button
                            @click="handleCopyModalLink"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white transition-all shrink-0 cursor-pointer"
                            :class="copiedModalLink ? 'bg-emerald-600' : 'bg-rose-600 hover:bg-rose-500'"
                        >
                            <Check v-if="copiedModalLink" class="w-3.5 h-3.5" />
                            <Copy v-else class="w-3.5 h-3.5" />
                            <span>{{ copiedModalLink ? 'Copiado!' : 'Copiar Link' }}</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        💡 Cole no seu WhatsApp ou guarde nos favoritos antes de confirmar a saída.
                    </p>
                </div>

                <!-- Modal Footer Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
                    <button
                        @click="showForgetModal = false"
                        type="button"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors"
                    >
                        Cancelar e Continuar Conectado
                    </button>
                    <button
                        @click="handleConfirmForget"
                        :disabled="isDisconnecting"
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-red-600 hover:bg-red-500 disabled:opacity-50 transition-all cursor-pointer shadow-md shadow-red-950/40"
                    >
                        <LogOut class="w-4 h-4" />
                        <span>{{ isDisconnecting ? 'Saindo...' : 'Confirmar e Sair' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
