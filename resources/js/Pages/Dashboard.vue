<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate, formatCurrency } from '@/Utils/formatters';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    PlusCircle, 
    Calendar, 
    Users, 
    Gift, 
    Copy, 
    Check, 
    ArrowRight, 
    Share2, 
    Sparkles, 
    Eye, 
    DollarSign,
    MessageCircle
} from 'lucide-vue-next';

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
});

const copiedCode = ref(null);

function copyInviteLink(event) {
    navigator.clipboard.writeText(event.invite_url);
    copiedCode.value = event.id;
    setTimeout(() => {
        copiedCode.value = null;
    }, 2500);
}

function getInviteText(event) {
    return `🎁 *Convite para o Amigo Secreto: ${event.title}*!\n\n` +
        `O sorteio está sendo organizado por mim no Amigo Secreto.\n` +
        (event.budget_limit ? `💰 *Valor sugerido:* ${formatCurrency(event.budget_limit)}\n` : '') +
        (event.event_date ? `📅 *Data da festa:* ${formatDate(event.event_date)}\n` : '') +
        `\n👇 *Entre no grupo e coloque sua lista de presentes aqui:*\n${event.invite_url}`;
}

function shareOnWhatsApp(event) {
    const text = encodeURIComponent(getInviteText(event));
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
}

async function shareGeneric(event) {
    const text = getInviteText(event);
    if (navigator.share) {
        try {
            await navigator.share({
                title: `Amigo Secreto: ${event.title}`,
                text: text,
                url: event.invite_url,
            });
            return;
        } catch (err) {
            if (err.name === 'AbortError') return;
        }
    }
    copyInviteLink(event);
}

const totalParticipants = computed(() => {
    return props.events.reduce((sum, e) => sum + (e.participants_count || 0), 0);
});

const totalDrawn = computed(() => {
    return props.events.filter(e => e.status === 'drawn').length;
});
</script>

<template>
    <Head title="Meus Eventos - Amigo Secreto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Painel do Organizador</span>
                        <span class="text-amber-500">✨</span>
                    </h1>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 font-medium">
                        Gerencie seus grupos de amigo secreto, acompanhe confirmações e realize sorteios.
                    </p>
                </div>

                <Link
                    :href="route('events.create')"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/20 hover:shadow-lg hover:shadow-rose-500/30 transition-all hover:scale-[1.02]"
                >
                    <PlusCircle class="w-4 h-4" />
                    Criar Novo Amigo Secreto
                </Link>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Stats Counters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                        <Gift class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-display">
                            {{ events.length }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Eventos Criados</div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <Users class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-display">
                            {{ totalParticipants }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Participantes Confirmados</div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <Sparkles class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-display">
                            {{ totalDrawn }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Sorteios Realizados</div>
                    </div>
                </div>
            </div>

            <!-- Events List Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                    <Calendar class="w-5 h-5 text-rose-500" />
                    Seus Grupos de Amigo Secreto
                </h2>
            </div>

            <!-- Events Grid -->
            <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="event in events"
                    :key="event.id"
                    class="flex flex-col justify-between rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-rose-500/40 transition-all duration-300 overflow-hidden group"
                >
                    <!-- Top Event Card -->
                    <div class="p-6 space-y-4">
                        <!-- Status Badge & Code -->
                        <div class="flex items-center justify-between gap-2">
                            <span
                                v-if="event.status === 'drawn'"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30"
                            >
                                <Sparkles class="w-3.5 h-3.5" />
                                Sorteado!
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-500/30"
                            >
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Inscrições Abertas
                            </span>

                            <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300 tracking-wider">
                                #{{ event.code }}
                            </span>
                        </div>

                        <!-- Title & Description -->
                        <div>
                            <Link
                                :href="route('events.show', event.id)"
                                class="text-xl font-black font-display text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors line-clamp-1"
                            >
                                {{ event.title }}
                            </Link>
                            <p v-if="event.description" class="text-xs text-slate-700 dark:text-slate-300 mt-1 line-clamp-2 leading-relaxed font-medium">
                                {{ event.description }}
                            </p>
                        </div>

                        <!-- Metadata Badges -->
                        <div class="pt-2 flex flex-wrap gap-2 text-xs">
                            <div v-if="event.budget_limit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <DollarSign class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                {{ formatCurrency(event.budget_limit) }}
                            </div>

                            <div v-if="event.event_date" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <Calendar class="w-3.5 h-3.5 text-rose-500" />
                                {{ formatDate(event.event_date) }}
                            </div>

                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <Users class="w-3.5 h-3.5 text-indigo-500" />
                                {{ event.participants_count }} amigo{{ event.participants_count === 1 ? '' : 's' }}
                            </div>

                            <div v-if="event.status === 'drawn'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">
                                <Eye class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                {{ event.revealed_count }}/{{ event.participants_count }} viram
                            </div>
                        </div>
                    </div>

                    <!-- Actions Bottom Bar -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button
                                @click="copyInviteLink(event)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 transition-colors shadow-sm"
                                :title="copiedCode === event.id ? 'Copiado!' : 'Copiar link de convite'"
                            >
                                <Check v-if="copiedCode === event.id" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ copiedCode === event.id ? 'Copiado!' : 'Convite' }}</span>
                            </button>

                            <button
                                @click="shareOnWhatsApp(event)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm shadow-emerald-600/20"
                                title="Enviar convite direto no WhatsApp"
                            >
                                <MessageCircle class="w-3.5 h-3.5" />
                                <span>WhatsApp</span>
                            </button>

                            <button
                                @click="shareGeneric(event)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 transition-colors"
                                title="Compartilhar por outros aplicativos"
                            >
                                <Share2 class="w-3.5 h-3.5 text-rose-500" />
                                <span>Compartilhar</span>
                            </button>
                        </div>

                        <Link
                            :href="route('events.show', event.id)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-black text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                        >
                            <span>Gerenciar</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="py-16 px-6 text-center rounded-3xl bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 max-w-xl mx-auto space-y-5"
            >
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-rose-500/20 to-amber-500/20 border border-rose-500/30 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-inner">
                    <Gift class="w-10 h-10 animate-bounce-slow" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-xl font-black font-display text-slate-900 dark:text-white">
                        Você ainda não tem nenhum Amigo Secreto
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 max-w-sm mx-auto leading-relaxed font-medium">
                        Crie seu primeiro evento em menos de 1 minuto e convide seus amigos pelo WhatsApp.
                    </p>
                </div>

                <div class="pt-2">
                    <Link
                        :href="route('events.create')"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-lg shadow-rose-500/25 transition-all hover:scale-[1.02]"
                    >
                        <PlusCircle class="w-4 h-4" />
                        Criar Meu Primeiro Amigo Secreto
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
