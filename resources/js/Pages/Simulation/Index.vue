<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import WishlistDisplay from '@/Components/WishlistDisplay.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { formatDate, formatCurrency, maskPhone } from '@/Utils/formatters';
import confetti from 'canvas-confetti';
import { 
    Gift, 
    PartyPopper, 
    ArrowRight, 
    ArrowLeft, 
    CheckCircle2, 
    User, 
    Calendar, 
    DollarSign, 
    MapPin, 
    RotateCcw, 
    Heart, 
    ThumbsDown, 
    Eye, 
    Info, 
    Lock, 
    ChevronDown,
    Home,
    Smartphone,
    ShoppingBag
} from 'lucide-vue-next';

// Simulation Navigation Step: 'invite' | 'secret'
const currentStep = ref('invite');
const isSubmitting = ref(false);
const isOpening = ref(false);
const isRevealed = ref(false);
const showPreferences = ref(true);
const toastMessage = ref('');
let toastTimeout = null;

function showToast(msg) {
    if (toastTimeout) clearTimeout(toastTimeout);
    toastMessage.value = msg;
    toastTimeout = setTimeout(() => {
        toastMessage.value = '';
    }, 4000);
}

// 1. Mock Event Data
const mockEvent = reactive({
    title: 'Amigo Secreto da Família e Amigos 2026 🎄',
    organizer_name: 'Mariana Silva',
    event_date: '2026-12-24',
    budget_limit: 60.00,
    location: 'Casa da Vovó Zilda e Salão de Festas',
    description: 'Venham todos comemorar nosso final de ano com muitas risadas, comida gostosa e troca de presentes!',
    code: 'DEMO2026',
});

// 2. Mock Participant Form Data (Pre-filled as requested)
const form = reactive({
    name: 'João Exemplo',
    phone: '(11) 90000-0000',
    email: 'joao@exemplo.com',
    clothing_size: 'M',
    shoe_size: '41',
    likes: 'Café especial, tecnologia, livros de ficção e chocolates amargos',
    dislikes: 'Bebidas alcoólicas e objetos pontiagudos',
    wishlist: [
        {
            title: 'Fone de Ouvido Bluetooth Sem Fio',
            url: 'https://shopee.com.br',
            notes: 'Cor preta ou cinza, de preferência com boa bateria',
        },
        {
            title: 'Livro de Ficção Científica ou Desenvolvimento',
            url: '',
            notes: 'Gosto de ficção científica (Isaac Asimov) e desenvolvimento pessoal',
        },
        {
            title: 'Garrafa Térmica Inox 500ml',
            url: '',
            notes: 'Para manter o café quentinho no trabalho',
        },
    ],
});

// 3. Mock Secret Friend (Who Lucas drew in the simulation)
const mockSecretFriend = reactive({
    name: 'Maria Exemplo',
    avatar: 'CS',
    clothing_size: 'P',
    shoe_size: '36',
    likes: 'Aromas suaves, papelaria fofa, chás aromáticos, plantas e livros de romance',
    dislikes: 'Bebidas alcoólicas e cores muito fluorescentes',
    wishlist: [
        {
            title: 'Kit de Pincéis de Maquiagem Macios',
            url: '',
            notes: 'Cerdas macias para o dia a dia',
        },
        {
            title: 'Caneca Cerâmica Artesanal + Café Gourmet',
            url: '',
            notes: 'Amo canecas bonitas e café passado quentinho!',
        },
        {
            title: 'Vela Aromática de Lavanda ou Baunilha',
            url: '',
            notes: 'Para relaxar à noite no quarto',
        },
    ],
});

function onPhoneInput(e) {
    form.phone = maskPhone(e.target.value);
}

// Transition from Step 1 (Invite Form) to Step 2 (Participant Home / Secret Reveal)
function submitSimulation() {
    isSubmitting.value = true;
    setTimeout(() => {
        isSubmitting.value = false;
        currentStep.value = 'secret';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        showToast('🎉 Inscrição simulada com sucesso! Entrando na sua página de participante...');
    }, 450);
}

// Reveal secret friend with confetti and suspense
function revealSecretFriend() {
    if (isRevealed.value || isOpening.value) return;
    
    isOpening.value = true;
    setTimeout(() => {
        isOpening.value = false;
        isRevealed.value = true;
        
        // Fire confetti celebration
        try {
            confetti({
                particleCount: 120,
                spread: 80,
                origin: { y: 0.5 },
                colors: ['#f43f5e', '#fbbf24', '#10b981', '#38bdf8', '#a855f7'],
            });
            setTimeout(() => {
                confetti({
                    particleCount: 60,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                });
                confetti({
                    particleCount: 60,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                });
            }, 300);
        } catch (e) {}

        showToast(`Você tirou ${mockSecretFriend.name}! Veja a lista de desejos dela abaixo.`);
    }, 400);
}

// Reset simulation to start fresh
function resetSimulation() {
    isRevealed.value = false;
    isOpening.value = false;
    currentStep.value = 'invite';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    showToast('Simulação reiniciada!');
}
</script>

<template>
    <Head>
        <title>Simulação Interativa de Amigo Secreto - GiftMatch</title>
        <meta
            name="description"
            content="Experimente como seus convidados se inscrevem e revelam o amigo secreto no GiftMatch. Demonstração 100% interativa sem necessidade de cadastro."
        />
    </Head>

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased selection:bg-rose-500 selection:text-white flex flex-col justify-between relative overflow-hidden">
        <!-- Ambient decorative lighting -->
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-gradient-to-b from-amber-500/15 via-rose-500/10 to-transparent rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-1/2 -right-40 w-[450px] h-[450px] bg-rose-500/10 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Sticky Simulation Notification Bar -->
        <aside class="sticky top-0 z-50 bg-gradient-to-r from-amber-950/90 via-slate-900/95 to-rose-950/90 border-b border-amber-500/40 backdrop-blur-md px-4 py-2.5 shadow-lg shadow-black/40">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5 text-xs">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold uppercase tracking-wider text-[10px]">
                        <Gift class="w-3 h-3 text-amber-400" />
                        <span>Modo Simulação</span>
                    </span>
                    <span class="text-slate-300 font-medium hidden md:inline">
                        Nenhum dado é salvo no banco de dados. Esta é uma experiência 100% interativa para você testar.
                    </span>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <button
                        @click="resetSimulation"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold transition-colors"
                        title="Reiniciar Simulação"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        <span>Reiniciar</span>
                    </button>
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold transition-colors"
                    >
                        <Home class="w-3.5 h-3.5" />
                        <span>Voltar à Home</span>
                    </Link>
                    <Link
                        :href="$page.props.auth.user ? route('events.create') : route('register')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-md shadow-rose-950/40 transition-all hover:scale-[1.02]"
                    >
                        <Gift class="w-3.5 h-3.5" />
                        <span>Criar Meu Grupo Real</span>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Header -->
        <header class="relative z-20 border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md">
            <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-2">
                    <ApplicationLogo />
                </Link>

                <!-- Step Tracker Pills -->
                <div class="flex items-center gap-2 text-xs font-bold">
                    <div
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full transition-all"
                        :class="currentStep === 'invite' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                    >
                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="currentStep === 'invite' ? 'bg-rose-500 text-white' : 'bg-slate-800 text-slate-400'">
                            1
                        </span>
                        <span class="hidden sm:inline">Convite do Convidado</span>
                        <span class="sm:hidden">Convite</span>
                    </div>

                    <ArrowRight class="w-3.5 h-3.5 text-slate-600" />

                    <div
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full transition-all"
                        :class="currentStep === 'secret' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                    >
                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]" :class="currentStep === 'secret' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400'">
                            2
                        </span>
                        <span class="hidden sm:inline">Página e Revelação</span>
                        <span class="sm:hidden">Revelação</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="relative z-10 flex-grow max-w-4xl mx-auto px-4 sm:px-6 py-8 w-full">
            <!-- Global Toast Notification -->
            <ToastNotification :message="toastMessage" />

            <!-- ============================================================= -->
            <!-- PHASE 1: SIMULATED INVITATION FORM ('invite')                 -->
            <!-- ============================================================= -->
            <div v-if="currentStep === 'invite'" class="space-y-6 animate-in fade-in duration-300">
                <!-- Helper card explaining this step -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-950/40 via-slate-900/80 to-slate-900/80 border border-amber-500/30 flex items-start gap-3.5 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 flex items-center justify-center shrink-0">
                        <Gift class="w-5 h-5" />
                    </div>
                    <div class="space-y-1 text-xs sm:text-sm">
                        <h2 class="font-bold text-white flex items-center gap-2">
                            <span>Etapa 1 de 2: Como seu convidado recebe o convite</span>
                        </h2>
                        <p class="text-slate-300 leading-relaxed">
                            Esta é a tela que seus amigos e familiares abrem quando você compartilha o link no WhatsApp.
                            <strong class="text-amber-300">O formulário abaixo já foi pré-preenchido</strong> com dados de teste para você avançar com 1 clique!
                        </p>
                    </div>
                </div>

                <!-- Event Details Card -->
                <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2.5">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                            <Gift class="w-3.5 h-3.5" />
                            <span>Você foi convidado(a)</span>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">
                            Código: <strong class="text-slate-200">{{ mockEvent.code }}</strong>
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black font-display text-white">
                            {{ mockEvent.title }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1">
                            Organizado com carinho por <strong class="text-slate-200">{{ mockEvent.organizer_name }}</strong>
                        </p>
                    </div>

                    <p v-if="mockEvent.description" class="text-xs sm:text-sm text-slate-300 bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80 leading-relaxed">
                        {{ mockEvent.description }}
                    </p>

                    <!-- Event Quick Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                            <div class="w-9 h-9 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center shrink-0">
                                <Calendar class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Data</span>
                                <span class="text-xs sm:text-sm font-semibold text-white truncate block">
                                    {{ formatDate(mockEvent.event_date) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center shrink-0">
                                <DollarSign class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Valor Sugerido</span>
                                <span class="text-xs sm:text-sm font-semibold text-white truncate block">
                                    {{ formatCurrency(mockEvent.budget_limit) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center shrink-0">
                                <MapPin class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Local</span>
                                <span class="text-xs sm:text-sm font-semibold text-white truncate block">
                                    {{ mockEvent.location }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form (Simulated & Pre-filled) -->
                <form @submit.prevent="submitSimulation" class="p-6 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <div>
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <User class="w-4 h-4 text-rose-400" />
                                <span>Formulário de Inscrição</span>
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Não exige criação de conta nem senha.
                            </p>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/15 border border-emerald-500/30 px-2.5 py-1 rounded-full flex items-center gap-1">
                            <CheckCircle2 class="w-3 h-3" />
                            <span>Preenchido</span>
                        </span>
                    </div>

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                Nome Completo *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-3 text-sm bg-slate-950 border border-slate-700/80 rounded-xl text-white focus:outline-none focus:border-rose-500"
                                placeholder="Seu nome"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                WhatsApp / Celular *
                            </label>
                            <input
                                v-model="form.phone"
                                @input="onPhoneInput"
                                type="tel"
                                required
                                class="w-full px-4 py-3 text-sm bg-slate-950 border border-slate-700/80 rounded-xl text-white focus:outline-none focus:border-rose-500"
                                placeholder="(11) 99999-9999"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                E-mail (Opcional)
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-3 text-sm bg-slate-950 border border-slate-700/80 rounded-xl text-white focus:outline-none focus:border-rose-500"
                                placeholder="seu@email.com"
                            />
                        </div>
                    </div>

                    <!-- Wishlist Editor Section -->
                    <div class="pt-2 border-t border-slate-800/80">
                        <div class="mb-3">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <Gift class="w-4 h-4 text-amber-400" />
                                <span>Lista de Presentes Desejados</span>
                            </h3>
                            <p class="text-xs text-slate-400">
                                Dê ideias para quem tirar você saber exatamente o que você gostaria de ganhar:
                            </p>
                        </div>
                        <WishlistEditor v-model="form.wishlist" />
                    </div>

                    <!-- Additional Sizes & Preferences Accordion -->
                    <div class="pt-2 border-t border-slate-800/80 space-y-3">
                        <button
                            type="button"
                            @click="showPreferences = !showPreferences"
                            class="w-full flex items-center justify-between text-left py-2 text-xs font-bold uppercase tracking-wider text-slate-300 hover:text-white transition-colors"
                        >
                            <span class="flex items-center gap-2">
                                <Heart class="w-3.5 h-3.5 text-rose-400" />
                                <span>Tamanhos de Roupa, Gostos e Desgostos</span>
                            </span>
                            <ChevronDown
                                class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                :class="showPreferences ? 'rotate-180' : ''"
                            />
                        </button>

                        <div v-show="showPreferences" class="space-y-4 pt-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-medium text-slate-300">
                                        Tamanho de Roupa
                                    </label>
                                    <input
                                        v-model="form.clothing_size"
                                        type="text"
                                        placeholder="Ex: M, 42"
                                        class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-rose-500"
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-medium text-slate-300">
                                        Calçado
                                    </label>
                                    <input
                                        v-model="form.shoe_size"
                                        type="text"
                                        placeholder="Ex: 41"
                                        class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-rose-500"
                                    />
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-medium text-slate-300">
                                    O que eu gosto / Hobbies
                                </label>
                                <textarea
                                    v-model="form.likes"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-rose-500 resize-none"
                                    placeholder="Ex: café, tecnologia, livros..."
                                ></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-medium text-slate-300">
                                    O que NÃO me dar / Restrições
                                </label>
                                <textarea
                                    v-model="form.dislikes"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-rose-500 resize-none"
                                    placeholder="Ex: bebidas, objetos frágeis..."
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Simulation Button -->
                    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-slate-400 text-center sm:text-left">
                            Clique abaixo para simular a inscrição e ir para a área secreta:
                        </p>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-lg shadow-rose-900/40 transition-all hover:scale-[1.01] cursor-pointer disabled:opacity-50"
                        >
                            <span v-if="isSubmitting">Inscrevendo...</span>
                            <span v-else>Confirmar Participação (Simular)</span>
                            <ArrowRight class="w-4 h-4" />
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================================= -->
            <!-- PHASE 2: PARTICIPANT HOME & SECRET REVEAL ('secret')          -->
            <!-- ============================================================= -->
            <div v-else class="space-y-6 animate-in fade-in duration-300">
                <!-- Helper card explaining this step -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-950/40 via-slate-900/80 to-slate-900/80 border border-emerald-500/30 flex items-start gap-3.5 shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 flex items-center justify-center shrink-0">
                        <PartyPopper class="w-5 h-5" />
                    </div>
                    <div class="space-y-1 text-xs sm:text-sm">
                        <h2 class="font-bold text-white flex items-center gap-2">
                            <span>Etapa 2 de 2: Home do Participante e Revelação</span>
                        </h2>
                        <p class="text-slate-300 leading-relaxed">
                            Aqui é onde o participante <strong>{{ form.name }}</strong> entra pelo link exclusivo do WhatsApp. O sorteio já aconteceu nesta simulação.
                            <strong class="text-emerald-300">Clique na caixa misteriosa abaixo para revelar quem ele tirou!</strong>
                        </p>
                    </div>
                </div>

                <!-- Event Header Card -->
                <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                            <span>Sorteio Realizado</span>
                        </div>
                        <span class="text-xs text-slate-400">
                            Inscrito como: <strong class="text-white">{{ form.name }}</strong>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black font-display text-white">
                        {{ mockEvent.title }}
                    </h1>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Confraternização</span>
                            <span class="font-semibold text-white mt-0.5 block">{{ formatDate(mockEvent.event_date) }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Valor Sugerido</span>
                            <span class="font-semibold text-white mt-0.5 block">{{ formatCurrency(mockEvent.budget_limit) }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 col-span-2 sm:col-span-1">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Organizador</span>
                            <span class="font-semibold text-white mt-0.5 block">{{ mockEvent.organizer_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- The Magic Reveal Box Card -->
                <div class="p-6 sm:p-10 rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-800 shadow-2xl relative overflow-hidden">
                    <!-- Glow behind card -->
                    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-72 h-72 bg-rose-500/20 rounded-full blur-[90px] pointer-events-none"></div>

                    <!-- STATE A: BEFORE REVEAL -->
                    <div v-if="!isRevealed" class="text-center space-y-6 py-4">
                        <div class="relative inline-flex items-center justify-center">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-gradient-to-br from-rose-500 to-amber-500 p-0.5 shadow-xl shadow-rose-900/40">
                                <div class="w-full h-full rounded-[22px] bg-slate-950 flex items-center justify-center">
                                    <Gift class="w-12 h-12 text-rose-400 animate-bounce" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 max-w-md mx-auto">
                            <h2 class="text-2xl sm:text-3xl font-black font-display text-white">
                                Quem é o seu Amigo Secreto?
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300">
                                O sorteio foi concluído com sucesso. Toque no botão abaixo para abrir o seu pacote e descobrir o seu par!
                            </p>
                        </div>

                        <div>
                            <button
                                @click="revealSecretFriend"
                                :disabled="isOpening"
                                class="inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl text-base font-black text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-xl shadow-rose-900/50 transition-all hover:scale-105 active:scale-95 cursor-pointer disabled:opacity-75"
                            >
                                <Gift class="w-5 h-5 text-amber-200" />
                                <span>{{ isOpening ? 'Abrindo o pacote...' : 'Revelar Meu Amigo Secreto' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- STATE B: AFTER REVEAL -->
                    <div v-else class="space-y-6 animate-in zoom-in-95 duration-500">
                        <div class="text-center space-y-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 mb-1">
                                <PartyPopper class="w-3.5 h-3.5 text-amber-400" />
                                <span>Você tirou no sorteio:</span>
                            </div>
                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display text-white tracking-tight">
                                {{ mockSecretFriend.name }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-400">
                                Guarde esse segredo a sete chaves até o dia da festa!
                            </p>
                        </div>

                        <!-- Secret Friend's Wishlist Section -->
                        <div class="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                    <ShoppingBag class="w-4 h-4 text-rose-400" />
                                    <span>Lista de Desejos de {{ mockSecretFriend.name }}</span>
                                </h3>
                                <span class="text-[11px] text-slate-400">
                                    Valor sugerido: {{ formatCurrency(mockEvent.budget_limit) }}
                                </span>
                            </div>

                            <WishlistDisplay :wishlist="mockSecretFriend.wishlist" />
                        </div>

                        <!-- Secret Friend's Sizes and Preferences -->
                        <div class="p-6 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 text-xs sm:text-sm">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <Heart class="w-4 h-4 text-rose-400" />
                                <span>Preferências e Medidas de {{ mockSecretFriend.name }}</span>
                            </h3>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tamanho de Roupa</span>
                                    <span class="font-bold text-white mt-0.5 block text-sm">{{ mockSecretFriend.clothing_size }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Calçado</span>
                                    <span class="font-bold text-white mt-0.5 block text-sm">{{ mockSecretFriend.shoe_size }}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80 space-y-0.5">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">O que a Camila gosta:</span>
                                    <p class="text-slate-200">{{ mockSecretFriend.likes }}</p>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80 space-y-0.5">
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">O que NÃO dar:</span>
                                    <p class="text-slate-200">{{ mockSecretFriend.dislikes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participant's Own Registered Wishlist -->
                <div class="p-5 sm:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                                <Gift class="w-4 h-4 text-amber-400 shrink-0" />
                                <span>Seus Presentes Cadastrados ({{ form.name }})</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5 leading-relaxed">
                                A pessoa que tirou você verá essas informações quando revelar o amigo secreto dela:
                            </p>
                        </div>
                        <button
                            @click="currentStep = 'invite'"
                            type="button"
                            class="self-start sm:self-auto text-xs font-semibold text-rose-400 hover:text-rose-300 transition-colors whitespace-nowrap"
                        >
                            Editar na Etapa 1
                        </button>
                    </div>

                    <WishlistDisplay :wishlist="form.wishlist" />
                </div>

                <!-- CTA Next Steps Card -->
                <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-rose-950/60 via-slate-900 to-amber-950/40 border border-rose-500/30 text-center space-y-5 shadow-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        <PartyPopper class="w-3.5 h-3.5" />
                        <span>Fim da Demonstração</span>
                    </div>

                    <div class="max-w-xl mx-auto space-y-2">
                        <h3 class="text-xl sm:text-2xl font-bold font-display text-white">
                            Gostou da facilidade da experiência?
                        </h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                            Crie o seu grupo agora mesmo em menos de 2 minutos. É 100% gratuito e seus convidados terão exatamente essa experiência incrível!
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                        <Link
                            :href="$page.props.auth.user ? route('events.create') : route('register')"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-lg shadow-rose-900/40 transition-all hover:scale-[1.01]"
                        >
                            <Gift class="w-4 h-4" />
                            <span>Criar Meu Amigo Secreto Real</span>
                        </Link>
                        <button
                            @click="resetSimulation"
                            type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors cursor-pointer"
                        >
                            <RotateCcw class="w-4 h-4" />
                            <span>Testar Novamente</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-800/80 py-6 text-xs text-slate-500 bg-slate-950">
            <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <ApplicationLogo />
                </div>
                <div class="flex items-center gap-3 text-slate-400">
                    <Link :href="route('home')" class="hover:text-white transition-colors">Página Inicial</Link>
                    <span>&bull;</span>
                    <Link :href="route('terms')" class="hover:text-white transition-colors">Termos</Link>
                    <span>&bull;</span>
                    <Link :href="route('privacy')" class="hover:text-white transition-colors">Privacidade</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
