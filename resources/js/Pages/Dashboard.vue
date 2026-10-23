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
    Eye, 
    DollarSign,
    MessageCircle,
    CheckCircle2
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
    return `*Convite para o Amigo Secreto: ${event.title}*!\n\n` +
        `O sorteio está sendo organizado no Amigo Secreto.\n` +
        (event.budget_limit ? `💰 *Valor sugerido:* ${formatCurrency(event.budget_limit)}\n` : '') +
        (event.event_date ? `📅 *Data da confraternização:* ${formatDate(event.event_date)}\n` : '') +
        `\n👇 *Entre no grupo e cadastre sua lista de presentes aqui:*\n${event.invite_url}`;
}

function shareOnWhatsApp(event) {
    const text = encodeURIComponent(getInviteText(event));
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
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
                    <h1 class="text-xl sm:text-2xl font-black font-display text-white">
                        Meus Amigos Secretos
                    </h1>
                    <p class="text-xs text-slate-400 mt-1 font-medium">
                        Gerencie seus grupos, acompanhe participantes confirmados e realize sorteios.
                    </p>
                </div>

                <Link
                    :href="route('events.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-900/30 transition-all active:scale-95 self-start sm:self-auto"
                >
                    <PlusCircle class="w-4 h-4" />
                    <span>Criar Novo Evento</span>
                </Link>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Stats Counters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                        <Gift class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-white font-display">
                            {{ events.length }}
                        </div>
                        <div class="text-xs text-slate-400 font-medium">Grupos Criados</div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                        <Users class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-white font-display">
                            {{ totalParticipants }}
                        </div>
                        <div class="text-xs text-slate-400 font-medium">Participantes Cadastrados</div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <CheckCircle2 class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-white font-display">
                            {{ totalDrawn }}
                        </div>
                        <div class="text-xs text-slate-400 font-medium">Sorteios Realizados</div>
                    </div>
                </div>
            </div>

            <!-- Events List Header -->
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <h2 class="text-base font-bold font-display text-white flex items-center gap-2">
                    <Calendar class="w-4 h-4 text-rose-400" />
                    <span>Seus Grupos Ativos</span>
                </h2>
                <span class="text-xs text-slate-500 font-medium">
                    {{ events.length }} {{ events.length === 1 ? 'grupo' : 'grupos' }}
                </span>
            </div>

            <!-- Events Grid -->
            <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="event in events"
                    :key="event.id"
                    class="flex flex-col justify-between rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all duration-200 overflow-hidden group shadow-sm"
                >
                    <!-- Top Event Card -->
                    <div class="p-5 space-y-4">
                        <!-- Status Badge & Code -->
                        <div class="flex items-center justify-between gap-2">
                            <span
                                v-if="event.status === 'drawn'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30"
                            >
                                <CheckCircle2 class="w-3.5 h-3.5" />
                                <span>Sorteado</span>
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30"
                            >
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>Inscrições Abertas</span>
                            </span>

                            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-800/60 px-2 py-0.5 rounded border border-slate-700/60">
                                #{{ event.code }}
                            </span>
                        </div>

                        <!-- Title & Description -->
                        <div>
                            <Link
                                :href="route('events.show', event.id)"
                                class="text-lg font-bold font-display text-white group-hover:text-rose-400 transition-colors line-clamp-1"
                            >
                                {{ event.title }}
                            </Link>
                            <p v-if="event.description" class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ event.description }}
                            </p>
                        </div>

                        <!-- Metadata Badges -->
                        <div class="pt-1 flex flex-wrap gap-2 text-xs">
                            <div v-if="event.budget_limit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/70 text-slate-200 border border-slate-700/60 font-medium">
                                <DollarSign class="w-3.5 h-3.5 text-emerald-400" />
                                <span>{{ formatCurrency(event.budget_limit) }}</span>
                            </div>

                            <div v-if="event.event_date" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/70 text-slate-200 border border-slate-700/60 font-medium">
                                <Calendar class="w-3.5 h-3.5 text-rose-400" />
                                <span>{{ formatDate(event.event_date) }}</span>
                            </div>

                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/70 text-slate-200 border border-slate-700/60 font-medium">
                                <Users class="w-3.5 h-3.5 text-indigo-400" />
                                <span>{{ event.participants_count }} amigo{{ event.participants_count === 1 ? '' : 's' }}</span>
                            </div>

                            <div v-if="event.status === 'drawn'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-950/40 text-emerald-300 border border-emerald-800/60 font-medium">
                                <Eye class="w-3.5 h-3.5 text-emerald-400" />
                                <span>{{ event.revealed_count }}/{{ event.participants_count }} viram</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Bottom Bar -->
                    <div class="p-3.5 bg-slate-950/60 border-t border-slate-800/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                @click="copyInviteLink(event)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors"
                                :title="copiedCode === event.id ? 'Copiado!' : 'Copiar link de convite'"
                            >
                                <Check v-if="copiedCode === event.id" class="w-3.5 h-3.5 text-emerald-400" />
                                <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ copiedCode === event.id ? 'Copiado!' : 'Copiar Link' }}</span>
                            </button>

                            <button
                                type="button"
                                @click="shareOnWhatsApp(event)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 hover:text-emerald-200 bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 transition-colors"
                                title="Enviar convite no WhatsApp"
                            >
                                <MessageCircle class="w-3.5 h-3.5" />
                                <span>WhatsApp</span>
                            </button>
                        </div>

                        <Link
                            :href="route('events.show', event.id)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-colors"
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
                class="py-16 px-6 text-center rounded-2xl bg-slate-900 border border-dashed border-slate-800 max-w-lg mx-auto space-y-4"
            >
                <div class="w-14 h-14 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mx-auto">
                    <Gift class="w-7 h-7" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold font-display text-white">
                        Você ainda não criou nenhum Amigo Secreto
                    </h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                        Crie seu primeiro grupo agora mesmo e compartilhe o link com seus amigos.
                    </p>
                </div>

                <div class="pt-2">
                    <Link
                        :href="route('events.create')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition-all"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>Criar Primeiro Amigo Secreto</span>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
