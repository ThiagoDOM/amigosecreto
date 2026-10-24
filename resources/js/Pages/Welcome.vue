<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { 
    Gift, 
    Sparkles, 
    Share2, 
    ShieldCheck, 
    Sliders, 
    ArrowRight, 
    CheckCircle2, 
    HeartHandshake,
    PartyPopper,
    Send
} from 'lucide-vue-next';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const inviteCodeInput = ref('');

function goToInvite() {
    if (inviteCodeInput.value.trim()) {
        router.visit(route('invite.show', { code: inviteCodeInput.value.trim().toUpperCase() }));
    }
}
</script>

<template>
    <Head title="Amigo Secreto Online - Crie seu sorteio gratuitamente" />

    <div class="min-h-screen bg-slate-950 text-slate-100 selection:bg-rose-500 selection:text-white relative overflow-hidden font-sans">
        <!-- Ambient Glowing Background Blobs -->
        <div class="absolute -top-32 -left-32 w-[550px] h-[550px] bg-rose-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/3 -right-32 w-[600px] h-[600px] bg-amber-500/15 rounded-full blur-[160px] pointer-events-none"></div>
        <div class="absolute -bottom-40 left-1/4 w-[500px] h-[500px] bg-emerald-500/15 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Header / Navigation -->
        <header class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center gap-2">
                    <ApplicationLogo />
                </Link>

                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-white/10 hover:bg-white/20 border border-white/10 text-white backdrop-blur-md transition-all shadow-sm"
                        >
                            Acessar Meu Painel
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition-colors"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-lg shadow-rose-600/30 transition-all hover:scale-[1.02]"
                        >
                            Criar Conta Grátis
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-20 lg:pt-16">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm font-semibold tracking-wide">
                    <Sparkles class="w-4 h-4 text-amber-400 animate-spin-slow" />
                    <span>O Amigo Secreto mais prático e moderno do Brasil</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-6xl font-black font-display tracking-tight text-white leading-tight sm:leading-tight">
                    Sorteie seu <span class="bg-gradient-to-r from-rose-500 via-pink-400 to-amber-400 bg-clip-text text-transparent">Amigo Secreto</span> sem complicação
                </h1>

                <!-- Subtitle -->
                <p class="text-lg sm:text-xl text-slate-300 font-normal leading-relaxed max-w-2xl mx-auto">
                    Crie o evento grátis, envie o link de convite pelo WhatsApp e realize o sorteio com regras de casais e revelação interativa com confetes!
                </p>

                <!-- Primary CTAs -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="$page.props.auth.user ? route('events.create') : route('register')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-xl shadow-rose-600/30 hover:shadow-rose-600/50 hover:scale-[1.02] transition-all"
                    >
                        <Gift class="w-5 h-5" />
                        Criar Meu Amigo Secreto Grátis
                    </Link>

                    <a
                        href="#como-funciona"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl text-base font-semibold text-slate-300 hover:text-white bg-slate-900/80 hover:bg-slate-800/80 border border-slate-800 transition-all"
                    >
                        Ver como funciona
                    </a>
                </div>

                <!-- Fast Join by Invite Code -->
                <div class="pt-6 max-w-md mx-auto">
                    <form @submit.prevent="goToInvite" class="relative flex items-center">
                        <input
                            v-model="inviteCodeInput"
                            type="text"
                            placeholder="Tem um código de convite? Ex: AS8K92LM"
                            class="w-full pl-5 pr-28 py-3.5 rounded-2xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 uppercase tracking-wider font-mono shadow-inner"
                        />
                        <button
                            type="submit"
                            class="absolute right-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 hover:from-rose-500 hover:to-amber-400 transition-all flex items-center gap-1 shadow-sm"
                        >
                            <span>Entrar</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </button>
                    </form>
                </div>

                <!-- Trust Badges -->
                <div class="pt-4 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                        100% Gratuito
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                        Sem login para convidados
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                        Envio no WhatsApp
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                        Regras de restrições
                    </span>
                </div>
            </div>

            <!-- Features Cards Grid -->
            <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-slate-900/60 border border-slate-800/80 hover:border-rose-500/30 transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 mb-6 group-hover:scale-110 transition-transform">
                        <Share2 class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold font-display text-white mb-2">Convite Fácil por WhatsApp</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Gere um link exclusivo do seu evento. Seus amigos entram e preenchem o nome e lista de desejos direto pelo celular, sem precisar de senha!
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-slate-900/60 border border-slate-800/80 hover:border-amber-500/30 transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 mb-6 group-hover:scale-110 transition-transform">
                        <Sliders class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold font-display text-white mb-2">Restrições & Casais</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Configure quem não pode tirar quem com apenas um clique! Perfeito para casais, irmãos ou amigos que já se presenteiam todo ano.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-slate-900/60 border border-slate-800/80 hover:border-emerald-500/30 transition-all hover:-translate-y-1 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
                        <PartyPopper class="w-6 h-6" />
                    </div>
                    <h3 class="text-xl font-bold font-display text-white mb-2">Revelação Mágica Interativa</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Cada participante recebe seu link mágico secreto. Ao abrir, uma animação interativa com confetes revela o amigo e sua lista de presentes!
                    </p>
                </div>
            </div>

            <!-- How It Works Section -->
            <section id="como-funciona" class="mt-28 pt-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl sm:text-4xl font-extrabold font-display text-white mb-4">
                        Como funciona em 3 passos simples
                    </h2>
                    <p class="text-slate-400 text-base">
                        Organize tudo pelo computador ou smartphone em menos de 2 minutos.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    <!-- Step 1 -->
                    <div class="relative p-6 rounded-3xl bg-slate-900/40 border border-slate-800 text-center flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-rose-600 text-white font-bold text-lg flex items-center justify-center shadow-lg shadow-rose-600/40 mb-4">
                            1
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Crie o Evento</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Defina o nome do grupo, o valor sugerido de presente, data da festa e instruções adicionais.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative p-6 rounded-3xl bg-slate-900/40 border border-slate-800 text-center flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-500 text-slate-950 font-bold text-lg flex items-center justify-center shadow-lg shadow-amber-500/40 mb-4">
                            2
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Compartilhe no WhatsApp</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Envie o link de convite no grupo da família ou da empresa. Acompanhe em tempo real quem confirmou presença.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative p-6 rounded-3xl bg-slate-900/40 border border-slate-800 text-center flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-500 text-white font-bold text-lg flex items-center justify-center shadow-lg shadow-emerald-500/40 mb-4">
                            3
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Sorteie e Comemore!</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Clique em sortear! Envie os links mágicos individuais e veja a mágica da confraternização acontecer.
                        </p>
                    </div>
                </div>

                <!-- Call to action card -->
                <div class="mt-16 p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-rose-900/60 via-slate-900 to-amber-950/60 border border-rose-500/30 text-center space-y-6">
                    <h3 class="text-2xl sm:text-3xl font-extrabold font-display text-white">
                        Pronto para organizar o seu Amigo Secreto?
                    </h3>
                    <p class="text-slate-300 text-base max-w-xl mx-auto">
                        Junte a família, amigos ou colegas de trabalho agora mesmo. É totalmente gratuito!
                    </p>
                    <div>
                        <Link
                            :href="$page.props.auth.user ? route('events.create') : route('register')"
                            class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-xl shadow-rose-600/30 transition-all hover:scale-[1.02]"
                        >
                            <Gift class="w-5 h-5" />
                            Começar Gratuitamente
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-800 py-8 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <ApplicationLogo />
                </div>
                <div>
                    Feito com carinho para as suas melhores confraternizações &bull; 2026
                </div>
            </div>
        </footer>
    </div>
</template>
