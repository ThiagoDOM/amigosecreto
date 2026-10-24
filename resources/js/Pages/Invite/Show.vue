<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import { formatDate, formatCurrency, maskPhone } from '@/Utils/formatters';
import { 
    Gift, 
    Calendar, 
    DollarSign, 
    MapPin, 
    User, 
    CheckCircle2, 
    AlertCircle 
} from 'lucide-vue-next';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: '',
    phone: '',
    email: '',
    wishlist: [
        { title: '', url: '', notes: '' }
    ],
    clothing_size: '',
    shoe_size: '',
    likes: '',
    dislikes: '',
});

function submit() {
    form.post(route('invite.join', props.event.code));
}
</script>

<template>
    <Head>
        <title>{{ 'Convite: ' + event.title + ' 🎁 - GiftMatch' }}</title>
        <meta name="description" :content="'Você foi convidado(a) por ' + (event.organizer_name || 'um amigo') + ' para o Amigo Secreto &quot;' + event.title + '&quot; no GiftMatch! Confirme sua participação, escolha seus presentes e participe do sorteio.'" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="GiftMatch" />
        <meta property="og:title" :content="'Convite para o Amigo Secreto: ' + event.title + ' 🎁 - GiftMatch'" />
        <meta property="og:description" :content="'Você foi convidado(a) por ' + (event.organizer_name || 'um amigo') + ' para o Amigo Secreto &quot;' + event.title + '&quot; no GiftMatch! Clique aqui para confirmar sua presença, indicar seus presentes e participar do sorteio.'" />
        <meta property="og:image" content="/og-invite.jpg" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="'Convite para o Amigo Secreto: ' + event.title + ' 🎁 - GiftMatch'" />
        <meta name="twitter:description" :content="'Você foi convidado(a) por ' + (event.organizer_name || 'um amigo') + ' para o Amigo Secreto &quot;' + event.title + '&quot; no GiftMatch! Confirme sua presença e adicione seus presentes.'" />
        <meta name="twitter:image" content="/og-invite.jpg" />
    </Head>

    <div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden font-sans py-12 px-4 sm:px-6">
        <ToastNotification />

        <!-- Festive background glows -->
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-rose-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-[550px] h-[550px] bg-amber-500/15 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto space-y-8">
            <!-- Brand Logo -->
            <div class="text-center">
                <Link :href="route('home')" class="inline-block transition-transform hover:scale-105">
                    <ApplicationLogo />
                </Link>
            </div>

            <!-- Event Card Banner -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-4">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                    Você foi convidado(a)!
                </div>

                <h1 class="text-3xl sm:text-4xl font-black font-display text-white">
                    {{ event.title }}
                </h1>

                <p v-if="event.description" class="text-sm text-slate-200 leading-relaxed font-medium">
                    {{ event.description }}
                </p>

                <!-- Metadata details -->
                <div class="pt-2 flex flex-wrap gap-2.5 text-xs">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <User class="w-3.5 h-3.5 text-rose-400" />
                        Organizado por: {{ event.organizer_name }}
                    </div>

                    <div v-if="event.budget_limit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <DollarSign class="w-3.5 h-3.5 text-emerald-400" />
                        Valor sugerido: {{ formatCurrency(event.budget_limit) }}
                    </div>

                    <div v-if="event.event_date" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <Calendar class="w-3.5 h-3.5 text-amber-400" />
                        Confraternização: {{ formatDate(event.event_date) }}
                    </div>

                    <div v-if="event.location" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <MapPin class="w-3.5 h-3.5 text-indigo-400" />
                        {{ event.location }}
                    </div>
                </div>
            </div>

            <!-- Registration Form or Closed Alert -->
            <div v-if="event.is_drawn" class="rounded-3xl bg-slate-900/90 border border-slate-800 p-8 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto">
                    <AlertCircle class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold font-display text-white">Inscrições Encerradas</h3>
                <p class="text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
                    O sorteio deste amigo secreto já foi realizado pelo organizador. Caso você já participe, solicite ao organizador o seu link exclusivo.
                </p>
            </div>

            <!-- Active Registration Form -->
            <form
                v-else
                @submit.prevent="submit"
                class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6"
            >
                <div class="space-y-1">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Gift class="w-5 h-5 text-rose-500" />
                        Confirme sua participação
                    </h2>
                    <p class="text-xs text-slate-300 font-medium">
                        Preencha seus dados para entrar no sorteio. Você receberá seu amigo em segredo após o sorteio!
                    </p>
                </div>

                <!-- Name -->
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                        Seu Nome Completo <span class="text-rose-500">*</span>
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Ex: Carlos Oliveira"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        required
                    />
                    <div v-if="form.errors.name" class="text-xs text-rose-400 font-semibold">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- WhatsApp / Phone with Auto-Mask -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            WhatsApp / Telefone
                        </label>
                        <input
                            id="phone"
                            :value="form.phone"
                            @input="form.phone = maskPhone($event.target.value)"
                            type="text"
                            placeholder="(11) 98765-4321"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                        <div v-if="form.errors.phone" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.phone }}
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            E-mail (opcional)
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="seu@email.com"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                        <div v-if="form.errors.email" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.email }}
                        </div>
                    </div>
                </div>

                <!-- Structured Wishlist Editor with Link Support -->
                <div class="pt-2">
                    <WishlistEditor v-model="form.wishlist" />
                    <div v-if="form.errors.wishlist" class="text-xs text-rose-400 font-semibold mt-1">
                        {{ form.errors.wishlist }}
                    </div>
                </div>

                <!-- Sizes Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="clothing_size" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Tamanho de Roupa
                        </label>
                        <input
                            id="clothing_size"
                            v-model="form.clothing_size"
                            type="text"
                            placeholder="Ex: M, G, 42"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                    </div>

                    <div class="space-y-1.5">
                        <label for="shoe_size" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Tamanho de Calçado
                        </label>
                        <input
                            id="shoe_size"
                            v-model="form.shoe_size"
                            type="text"
                            placeholder="Ex: 38, 40, 42"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                    </div>
                </div>

                <!-- Likes & Dislikes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="likes" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            O que você adora?
                        </label>
                        <input
                            id="likes"
                            v-model="form.likes"
                            type="text"
                            placeholder="Ex: Café, gatos, plantas, séries"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                    </div>

                    <div class="space-y-1.5">
                        <label for="dislikes" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Não gosta / Alergias
                        </label>
                        <input
                            id="dislikes"
                            v-model="form.dislikes"
                            type="text"
                            placeholder="Ex: Lactose, cheiros fortes"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-xl shadow-rose-600/30 transition-all hover:scale-[1.01] disabled:opacity-50"
                    >
                        <Gift class="w-5 h-5" />
                        <span>{{ form.processing ? 'Entrando no Amigo Secreto...' : 'Confirmar Minha Participação' }}</span>
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-2 font-medium">
                        Ao entrar, você receberá seu link secreto exclusivo.
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
