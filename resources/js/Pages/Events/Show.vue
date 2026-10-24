<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import WishlistDisplay from '@/Components/WishlistDisplay.vue';
import { formatDate, formatPhone, maskPhone, formatCurrency } from '@/Utils/formatters';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Users, 
    Gift, 
    Copy, 
    Check, 
    Share2, 
    Calendar, 
    DollarSign, 
    MapPin, 
    PlusCircle, 
    Sliders, 
    RefreshCw, 
    Trash2, 
    Eye, 
    EyeOff, 
    AlertTriangle, 
    X, 
    Edit, 
    Sparkles, 
    ExternalLink, 
    Search,
    HelpCircle,
    MessageCircle,
    Send,
    Mail
} from 'lucide-vue-next';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    participants: {
        type: Array,
        default: () => [],
    },
    exclusions: {
        type: Array,
        default: () => [],
    },
});

// Clipboard state
const copiedInvite = ref(false);
const copiedMagic = ref(null);
const searchQuery = ref('');

// Modals state
const showAddParticipantModal = ref(false);
const showExclusionsModal = ref(false);
const showEditEventModal = ref(false);
const showWishlistModal = ref(false);
const showDrawConfirmModal = ref(false);
const showResetConfirmModal = ref(false);
const showDeleteParticipantModal = ref(false);
const showDeleteExclusionModal = ref(false);
const showShareModal = ref(false);
const currentSharePayload = ref(null);
const copiedPayloadLink = ref(false);
const copiedFullMessage = ref(false);

const selectedParticipantWishlist = ref(null);
const participantToDelete = ref(null);
const exclusionToDelete = ref(null);

// Forms
const addParticipantForm = useForm({
    name: '',
    phone: '',
    email: '',
    wishlist: [],
    clothing_size: '',
    shoe_size: '',
    likes: '',
    dislikes: '',
});

const addExclusionForm = useForm({
    participant_id: '',
    excluded_participant_id: '',
    bidirectional: true,
});

const editEventForm = useForm({
    title: props.event.title,
    description: props.event.description || '',
    budget_limit: props.event.budget_limit || '',
    draw_date: props.event.draw_date || '',
    event_date: props.event.event_date || '',
    location: props.event.location || '',
});

// Copy Invite Link
function copyInviteLink() {
    navigator.clipboard.writeText(props.event.invite_url);
    copiedInvite.value = true;
    setTimeout(() => {
        copiedInvite.value = false;
    }, 2500);
}

// Copy Participant Magic Link
function copyMagicLink(p) {
    navigator.clipboard.writeText(p.magic_url);
    copiedMagic.value = p.id;
    setTimeout(() => {
        copiedMagic.value = null;
    }, 2500);
}

// Shared invite payload
const invitePayload = computed(() => {
    const text = `🎁 *Convite para o Amigo Secreto: ${props.event.title}*!\n\n` +
        `Olá pessoal! Estou organizando nosso amigo secreto no site Amigo Secreto.\n` +
        (props.event.budget_limit ? `💰 *Valor sugerido:* ${formatCurrency(props.event.budget_limit)}\n` : '') +
        (props.event.event_date ? `📅 *Data da confraternização:* ${formatDate(props.event.event_date)}\n` : '') +
        (props.event.location ? `📍 *Local:* ${props.event.location}\n` : '') +
        `\n👉 *Clique no link abaixo para entrar e cadastrar seus presentes desejados:*\n${props.event.invite_url}`;

    return {
        title: `Convite: ${props.event.title}`,
        text: text,
        url: props.event.invite_url,
    };
});

function getParticipantSharePayload(p) {
    const text = `🎉 Olá *${p.name}*!\n\n` +
        `O sorteio do Amigo Secreto *${props.event.title}* foi realizado com sucesso!\n\n` +
        `🎁 Descubra quem você tirou clicando no seu link exclusivo e secreto abaixo:\n` +
        `${p.magic_url}\n\n` +
        `🤫 Lembre-se: é segredo! Não conte para ninguém e confira a lista de desejos do seu amigo.`;

    return {
        title: `Amigo Secreto de ${p.name} (${props.event.title})`,
        text: text,
        url: p.magic_url,
        phone: p.clean_phone || '',
    };
}

// WhatsApp Share for the Invite Link
function shareInviteWhatsApp() {
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(invitePayload.value.text)}`, '_blank');
}

// WhatsApp Send Individual Secret Friend Magic Link
function sendMagicLinkWhatsApp(p) {
    const phoneParam = p.clean_phone ? `phone=${p.clean_phone}&` : '';
    const payload = getParticipantSharePayload(p);
    window.open(`https://api.whatsapp.com/send?${phoneParam}text=${encodeURIComponent(payload.text)}`, '_blank');
}

// Open Multi-Channel Share Modal
function openShareModal(payload) {
    currentSharePayload.value = payload;
    copiedPayloadLink.value = false;
    copiedFullMessage.value = false;
    showShareModal.value = true;
}

// Generic Share for Invite (Native Web Share with Fallback to Modal)
async function shareInviteGeneric() {
    const payload = invitePayload.value;
    if (navigator.share) {
        try {
            await navigator.share({
                title: payload.title,
                text: payload.text,
                url: payload.url,
            });
            return;
        } catch (err) {
            if (err.name === 'AbortError') return;
        }
    }
    openShareModal(payload);
}

// Generic Share for Participant Magic Link (Native Web Share with Fallback to Modal)
async function shareParticipantGeneric(p) {
    const payload = getParticipantSharePayload(p);
    if (navigator.share) {
        try {
            await navigator.share({
                title: payload.title,
                text: payload.text,
                url: payload.url,
            });
            return;
        } catch (err) {
            if (err.name === 'AbortError') return;
        }
    }
    openShareModal(payload);
}

function shareViaWhatsApp(payload) {
    const phoneParam = payload.phone ? `phone=${payload.phone}&` : '';
    window.open(`https://api.whatsapp.com/send?${phoneParam}text=${encodeURIComponent(payload.text)}`, '_blank');
}

function shareViaTelegram(payload) {
    window.open(`https://t.me/share/url?url=${encodeURIComponent(payload.url)}&text=${encodeURIComponent(payload.text)}`, '_blank');
}

function shareViaEmail(payload) {
    window.open(`mailto:?subject=${encodeURIComponent(payload.title)}&body=${encodeURIComponent(payload.text)}`, '_blank');
}

function copyPayloadUrl(payload) {
    navigator.clipboard.writeText(payload.url);
    copiedPayloadLink.value = true;
    setTimeout(() => {
        copiedPayloadLink.value = false;
    }, 2500);
}

function copyPayloadFullText(payload) {
    navigator.clipboard.writeText(payload.text);
    copiedFullMessage.value = true;
    setTimeout(() => {
        copiedFullMessage.value = false;
    }, 2500);
}

// Draw Action via Modal
function requestDraw() {
    showDrawConfirmModal.value = true;
}

function executeDraw() {
    router.post(route('events.draw', props.event.id), {}, {
        onFinish: () => {
            showDrawConfirmModal.value = false;
        }
    });
}

// Reset Draw Action
function executeResetDraw() {
    router.post(route('events.reset-draw', props.event.id), {}, {
        onSuccess: () => {
            showResetConfirmModal.value = false;
        }
    });
}

// Participant Delete Confirmation via Modal
function requestDeleteParticipant(p) {
    participantToDelete.value = p;
    showDeleteParticipantModal.value = true;
}

function executeDeleteParticipant() {
    if (!participantToDelete.value) return;
    router.delete(route('events.participants.destroy', { 
        event: props.event.id, 
        participant: participantToDelete.value.id 
    }), {
        onSuccess: () => {
            showDeleteParticipantModal.value = false;
            participantToDelete.value = null;
        }
    });
}

// Exclusion Delete Confirmation via Modal
function requestDeleteExclusion(ex) {
    exclusionToDelete.value = ex;
    showDeleteExclusionModal.value = true;
}

function executeDeleteExclusion() {
    if (!exclusionToDelete.value) return;
    router.delete(route('events.exclusions.destroy', { 
        event: props.event.id, 
        exclusion: exclusionToDelete.value.id 
    }), {
        onSuccess: () => {
            showDeleteExclusionModal.value = false;
            exclusionToDelete.value = null;
        }
    });
}

// Submit Add Participant
function submitAddParticipant() {
    addParticipantForm.post(route('events.participants.store', props.event.id), {
        onSuccess: () => {
            showAddParticipantModal.value = false;
            addParticipantForm.reset();
            addParticipantForm.wishlist = [];
        }
    });
}

// Submit Exclusion
function submitExclusion() {
    addExclusionForm.post(route('events.exclusions.store', props.event.id), {
        onSuccess: () => {
            addExclusionForm.reset('excluded_participant_id');
        }
    });
}

// Submit Edit Event
function submitEditEvent() {
    editEventForm.put(route('events.update', props.event.id), {
        onSuccess: () => {
            showEditEventModal.value = false;
        }
    });
}

function openWishlist(p) {
    selectedParticipantWishlist.value = p;
    showWishlistModal.value = true;
}

// Filtered Participants
const filteredParticipants = computed(() => {
    if (!searchQuery.value.trim()) return props.participants;
    const q = searchQuery.value.toLowerCase();
    return props.participants.filter(p => p.name.toLowerCase().includes(q) || (p.phone && p.phone.includes(q)));
});

const revealedCount = computed(() => {
    return props.participants.filter(p => p.is_revealed).length;
});
</script>

<template>
    <Head :title="event.title + ' - Painel do Evento'" />

    <AuthenticatedLayout>
        <div class="space-y-8">
            <!-- Event Header Card -->
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Left: Title & Details -->
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span
                                v-if="event.is_drawn"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30"
                            >
                                <Sparkles class="w-3.5 h-3.5" />
                                Sorteio Realizado em {{ formatDate(event.drawn_at) }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-500/30"
                            >
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Inscrições Abertas (Aguardando Sorteio)
                            </span>

                            <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700">
                                Código: #{{ event.code }}
                            </span>
                        </div>

                        <h1 class="text-3xl font-black font-display text-slate-900 dark:text-white">
                            {{ event.title }}
                        </h1>

                        <p v-if="event.description" class="text-sm text-slate-700 dark:text-slate-200 max-w-2xl leading-relaxed">
                            {{ event.description }}
                        </p>

                        <!-- Meta Badges -->
                        <div class="pt-2 flex flex-wrap gap-3 text-xs">
                            <div v-if="event.budget_limit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <DollarSign class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                Valor sugerido: {{ formatCurrency(event.budget_limit) }}
                            </div>

                            <div v-if="event.event_date" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <Calendar class="w-4 h-4 text-rose-500" />
                                Confraternização: {{ formatDate(event.event_date) }}
                            </div>

                            <div v-if="event.draw_date" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <Calendar class="w-4 h-4 text-amber-500" />
                                Sorteio previsto: {{ formatDate(event.draw_date) }}
                            </div>

                            <div v-if="event.location" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-bold border border-slate-200 dark:border-slate-700">
                                <MapPin class="w-4 h-4 text-indigo-500" />
                                {{ event.location }}
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Edit button -->
                    <div class="flex items-center gap-2 self-start lg:self-center">
                        <button
                            @click="showEditEventModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors border border-slate-200 dark:border-slate-700"
                        >
                            <Edit class="w-3.5 h-3.5" />
                            Editar Evento
                        </button>
                    </div>
                </div>

                <!-- Invite Link Box -->
                <div class="mt-8 p-4 rounded-2xl bg-gradient-to-r from-rose-500/10 via-amber-500/5 to-emerald-500/10 border border-rose-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                            <Share2 class="w-3.5 h-3.5" />
                            Link de Convite para Participantes
                        </div>
                        <div class="text-xs text-slate-700 dark:text-slate-200 font-medium">
                            Envie este link para as pessoas entrarem no grupo e adicionarem suas listas de presentes:
                        </div>
                        <div class="text-xs font-mono font-bold text-slate-900 dark:text-slate-100 select-all truncate max-w-lg">
                            {{ event.invite_url }}
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                        <button
                            @click="copyInviteLink"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm"
                            title="Copiar link do convite"
                        >
                            <Check v-if="copiedInvite" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            <Copy v-else class="w-4 h-4 text-slate-500" />
                            <span>{{ copiedInvite ? 'Copiado!' : 'Copiar Link' }}</span>
                        </button>

                        <button
                            @click="shareInviteWhatsApp"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm shadow-emerald-600/30"
                            title="Enviar convite diretamente no WhatsApp"
                        >
                            <MessageCircle class="w-4 h-4" />
                            <span>WhatsApp</span>
                        </button>

                        <button
                            @click="shareInviteGeneric"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 transition-colors shadow-sm"
                            title="Compartilhar por outros aplicativos (Telegram, E-mail, etc.)"
                        >
                            <Share2 class="w-4 h-4 text-rose-500" />
                            <span>Compartilhar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action & Controls Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Add Manual Participant -->
                    <button
                        v-if="!event.is_drawn"
                        @click="showAddParticipantModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm transition-colors"
                    >
                        <PlusCircle class="w-4 h-4 text-rose-500" />
                        Adicionar Participante Manual
                    </button>

                    <!-- Exclusions / Restrictions -->
                    <button
                        v-if="!event.is_drawn"
                        @click="showExclusionsModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm transition-colors"
                    >
                        <Sliders class="w-4 h-4 text-amber-500" />
                        Restrições de Casais ({{ exclusions.length }})
                    </button>

                    <!-- Reset Draw -->
                    <button
                        v-if="event.is_drawn"
                        @click="showResetConfirmModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-900 hover:bg-rose-100 transition-colors"
                    >
                        <RefreshCw class="w-4 h-4" />
                        Reiniciar / Sortear Novamente
                    </button>
                </div>

                <!-- Primary Draw Button -->
                <div>
                    <button
                        v-if="!event.is_drawn"
                        @click="requestDraw"
                        :disabled="!event.can_draw"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-2xl text-sm font-black uppercase tracking-wider text-white shadow-lg transition-all"
                        :class="event.can_draw 
                            ? 'bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-rose-600/30 hover:shadow-rose-600/50 hover:scale-[1.02]' 
                            : 'bg-slate-300 dark:bg-slate-800 text-slate-500 cursor-not-allowed shadow-none'"
                    >
                        <Sparkles class="w-5 h-5 text-amber-300" />
                        <span>Realizar Sorteio Agora! 🎲</span>
                    </button>

                    <div v-if="!event.can_draw && !event.is_drawn" class="text-right text-xs text-amber-700 dark:text-amber-400 mt-1 font-semibold">
                        * Mínimo de 3 participantes para sortear (atualmente: {{ participants.length }})
                    </div>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
                <!-- Participants List Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                            <Users class="w-5 h-5 text-rose-500" />
                            <span>Participantes Confirmados ({{ participants.length }})</span>
                        </h2>
                        <p v-if="event.is_drawn" class="text-xs text-slate-700 dark:text-slate-300 mt-0.5">
                            Status de visualização: <strong class="text-emerald-700 dark:text-emerald-400 font-bold">{{ revealedCount }} de {{ participants.length }}</strong> já descobriram quem tiraram.
                        </p>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar participante..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-rose-500"
                        />
                    </div>
                </div>

                <!-- Empty Participants State -->
                <div v-if="participants.length === 0" class="py-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                        <Users class="w-8 h-8" />
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white">Nenhum amigo entrou ainda</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 max-w-sm mx-auto mt-1">
                            Compartilhe o link de convite no grupo do WhatsApp ou adicione participantes manualmente.
                        </p>
                    </div>
                    <button
                        @click="shareInviteWhatsApp"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm"
                    >
                        <Share2 class="w-4 h-4" />
                        Convidar Pelo WhatsApp
                    </button>
                </div>

                <!-- Participants Table / List -->
                <div v-else class="divide-y divide-slate-100 dark:divide-slate-800">
                    <div
                        v-for="p in filteredParticipants"
                        :key="p.id"
                        class="py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 rounded-2xl px-3 transition-colors"
                    >
                        <!-- Participant Info -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white font-extrabold flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                                {{ p.name.charAt(0).toUpperCase() }}
                            </div>

                            <div class="space-y-0.5">
                                <div class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>{{ p.name }}</span>
                                    <span v-if="p.is_revealed" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 px-2 py-0.5 rounded-md">
                                        <Eye class="w-3 h-3" />
                                        Já viu o amigo
                                    </span>
                                    <span v-else-if="event.is_drawn" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                        <EyeOff class="w-3 h-3" />
                                        Ainda não abriu
                                    </span>
                                </div>

                                <div class="text-xs text-slate-600 dark:text-slate-300 flex flex-wrap items-center gap-3 font-medium">
                                    <span v-if="p.phone">{{ formatPhone(p.phone) }}</span>
                                    <span v-if="p.email">{{ p.email }}</span>
                                    <button
                                        v-if="p.wishlist || p.clothing_size || p.shoe_size || p.likes"
                                        @click="openWishlist(p)"
                                        class="text-rose-600 dark:text-rose-400 hover:underline font-bold flex items-center gap-1"
                                    >
                                        <Gift class="w-3 h-3" />
                                        Ver Desejos
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Actions per Participant -->
                        <div class="flex flex-wrap items-center gap-2 self-end md:self-center">
                            <!-- When Drawn: Magic Link, WhatsApp & Generic Share buttons -->
                            <template v-if="event.is_drawn">
                                <button
                                    @click="copyMagicLink(p)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm"
                                    :title="copiedMagic === p.id ? 'Copiado!' : 'Copiar link mágico individual'"
                                >
                                    <Check v-if="copiedMagic === p.id" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                    <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ copiedMagic === p.id ? 'Copiado!' : 'Copiar' }}</span>
                                </button>

                                <button
                                    @click="sendMagicLinkWhatsApp(p)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm shadow-emerald-600/20"
                                    title="Enviar link secreto individual pelo WhatsApp"
                                >
                                    <MessageCircle class="w-3.5 h-3.5" />
                                    <span>WhatsApp</span>
                                </button>

                                <button
                                    @click="shareParticipantGeneric(p)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 transition-colors"
                                    title="Compartilhar link por outros aplicativos ou canais"
                                >
                                    <Share2 class="w-3.5 h-3.5 text-indigo-500" />
                                    <span>Compartilhar</span>
                                </button>

                                <a
                                    :href="p.magic_url"
                                    target="_blank"
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
                                    title="Abrir página do participante em nova aba"
                                >
                                    <ExternalLink class="w-4 h-4" />
                                </a>
                            </template>

                            <!-- When Not Drawn: Delete button -->
                            <template v-else>
                                <button
                                    @click="requestDeleteParticipant(p)"
                                    class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                                    title="Remover participante"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Sorteio (Sem alert/confirm nativo) -->
        <div v-if="showDrawConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto">
                    <Sparkles class="w-8 h-8" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white">
                        Realizar o Sorteio Agora?
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        O algoritmo sorteará aleatoriamente os amigos secretos, respeitando as regras de restrições. Cada participante receberá seu link secreto exclusivo.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDrawConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDraw"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 shadow-md shadow-rose-600/30"
                    >
                        Confirmar e Sortear!
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Exclusão de Participante (Sem confirm nativo) -->
        <div v-if="showDeleteParticipantModal && participantToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                    <Trash2 class="w-8 h-8" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                        Remover {{ participantToDelete.name }}?
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Tem certeza de que deseja remover este participante do amigo secreto?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDeleteParticipantModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDeleteParticipant"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/30"
                    >
                        Sim, Remover
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Exclusão de Regra de Restrição (Sem confirm nativo) -->
        <div v-if="showDeleteExclusionModal && exclusionToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                    <AlertTriangle class="w-8 h-8" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                        Remover Restrição?
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Deseja remover a regra de que <strong>{{ exclusionToDelete.participant_name }}</strong> não pode tirar <strong>{{ exclusionToDelete.excluded_participant_name }}</strong>?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDeleteExclusionModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDeleteExclusion"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/30"
                    >
                        Sim, Remover Regra
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Adicionar Participante Manual com Lista de Desejos Estruturada -->
        <div v-if="showAddParticipantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-xl rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <PlusCircle class="w-5 h-5 text-rose-500" />
                        Adicionar Participante Manual
                    </h3>
                    <button @click="showAddParticipantModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitAddParticipant" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-1">
                            Nome Completo <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="addParticipantForm.name"
                            type="text"
                            placeholder="Ex: Tio Roberto"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-1">WhatsApp / Telefone</label>
                            <input
                                :value="addParticipantForm.phone"
                                @input="addParticipantForm.phone = maskPhone($event.target.value)"
                                type="text"
                                placeholder="(11) 98765-4321"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-1">E-mail (opcional)</label>
                            <input
                                v-model="addParticipantForm.email"
                                type="email"
                                placeholder="roberto@email.com"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <!-- Structured Wishlist Editor with Links -->
                    <div class="pt-2">
                        <WishlistEditor v-model="addParticipantForm.wishlist" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-1">Tamanho Roupa</label>
                            <input
                                v-model="addParticipantForm.clothing_size"
                                type="text"
                                placeholder="Ex: M ou 42"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-1">Calçado</label>
                            <input
                                v-model="addParticipantForm.shoe_size"
                                type="text"
                                placeholder="Ex: 40"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showAddParticipantModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="addParticipantForm.processing"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 shadow-md"
                        >
                            {{ addParticipantForm.processing ? 'Salvando...' : 'Adicionar Participante' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Restrições de Casais -->
        <div v-if="showExclusionsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <Sliders class="w-5 h-5 text-amber-500" />
                        Regras de Restrição (Casais)
                    </h3>
                    <button @click="showExclusionsModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
                    Evite que certas pessoas tirem umas às outras no sorteio (ex: casais, pais e filhos ou amigos que já se presenteiam todo ano).
                </p>

                <!-- Existing Exclusions List -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Restrições Atuais</h4>
                    <div v-if="exclusions.length === 0" class="text-xs text-slate-600 dark:text-slate-300 py-3 text-center bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700">
                        Nenhuma restrição configurada. Qualquer um pode tirar qualquer um.
                    </div>
                    <div v-else class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        <div
                            v-for="ex in exclusions"
                            :key="ex.id"
                            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 flex items-center justify-between text-xs"
                        >
                            <span class="font-medium text-slate-800 dark:text-slate-200">
                                <strong>{{ ex.participant_name }}</strong> não pode tirar <strong>{{ ex.excluded_participant_name }}</strong>
                            </span>
                            <button
                                @click="requestDeleteExclusion(ex)"
                                class="text-slate-400 hover:text-rose-500 p-1 rounded"
                                title="Remover regra"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Restriction Form -->
                <form @submit.prevent="submitExclusion" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Adicionar Nova Regra</h4>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Participante A</label>
                            <select
                                v-model="addExclusionForm.participant_id"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                                required
                            >
                                <option value="" disabled>Selecione...</option>
                                <option v-for="p in participants" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Não pode tirar B</label>
                            <select
                                v-model="addExclusionForm.excluded_participant_id"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                                required
                            >
                                <option value="" disabled>Selecione...</option>
                                <option
                                    v-for="p in participants"
                                    :key="p.id"
                                    :value="p.id"
                                    :disabled="p.id === addExclusionForm.participant_id"
                                >
                                    {{ p.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input
                            id="bidirectional"
                            v-model="addExclusionForm.bidirectional"
                            type="checkbox"
                            class="rounded text-rose-600 focus:ring-rose-500"
                        />
                        <label for="bidirectional" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Regra Mútua (Casal: B também não pode tirar A)
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            :disabled="addExclusionForm.processing || !addExclusionForm.participant_id || !addExclusionForm.excluded_participant_id"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 transition-colors shadow-sm disabled:opacity-50"
                        >
                            {{ addExclusionForm.processing ? 'Adicionando...' : 'Adicionar Restrição' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Editar Evento -->
        <div v-if="showEditEventModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <Edit class="w-5 h-5 text-rose-500" />
                        Editar Evento
                    </h3>
                    <button @click="showEditEventModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEditEvent" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Nome do Evento</label>
                        <input
                            v-model="editEventForm.title"
                            type="text"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Valor Sugerido (R$)</label>
                        <input
                            v-model="editEventForm.budget_limit"
                            type="number"
                            step="0.50"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Data Sorteio</label>
                            <input
                                v-model="editEventForm.draw_date"
                                type="date"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Data Festa</label>
                            <input
                                v-model="editEventForm.event_date"
                                type="date"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Local</label>
                        <input
                            v-model="editEventForm.location"
                            type="text"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1">Descrição e Regras</label>
                        <textarea
                            v-model="editEventForm.description"
                            rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white"
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="showEditEventModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="editEventForm.processing"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md"
                        >
                            {{ editEventForm.processing ? 'Salvando...' : 'Salvar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Ver Desejos do Participante com WishlistDisplay -->
        <div v-if="showWishlistModal && selectedParticipantWishlist" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white font-bold flex items-center justify-center">
                            {{ selectedParticipantWishlist.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                {{ selectedParticipantWishlist.name }}
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-300">Preferências e Lista de Presentes</p>
                        </div>
                    </div>
                    <button @click="showWishlistModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Wishlist Display with Links -->
                    <div>
                        <div class="font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-1.5">
                            <Gift class="w-4 h-4 text-rose-500" />
                            Presentes Desejados:
                        </div>
                        <WishlistDisplay :wishlist="selectedParticipantWishlist.wishlist" />
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                            <div class="font-bold text-slate-700 dark:text-slate-300 mb-0.5">Tamanho de Roupa</div>
                            <div class="text-slate-900 dark:text-white font-bold text-sm">{{ selectedParticipantWishlist.clothing_size || '-' }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                            <div class="font-bold text-slate-700 dark:text-slate-300 mb-0.5">Tamanho de Calçado</div>
                            <div class="text-slate-900 dark:text-white font-bold text-sm">{{ selectedParticipantWishlist.shoe_size || '-' }}</div>
                        </div>
                    </div>

                    <div v-if="selectedParticipantWishlist.likes" class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50">
                        <div class="font-bold text-emerald-900 dark:text-emerald-200 mb-0.5">O que adora:</div>
                        <div class="text-emerald-800 dark:text-emerald-100 font-medium">{{ selectedParticipantWishlist.likes }}</div>
                    </div>

                    <div v-if="selectedParticipantWishlist.dislikes" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50">
                        <div class="font-bold text-rose-900 dark:text-rose-200 mb-0.5">Não gosta / Alergia:</div>
                        <div class="text-rose-800 dark:text-rose-100 font-medium">{{ selectedParticipantWishlist.dislikes }}</div>
                    </div>
                </div>

                <div class="pt-2 text-right border-t border-slate-100 dark:border-slate-800">
                    <button
                        @click="showWishlistModal = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Reinício de Sorteio -->
        <div v-if="showResetConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
                    <AlertTriangle class="w-8 h-8" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                        Tem certeza que deseja reiniciar o sorteio?
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Ao reiniciar, os pares sorteados serão apagados e os participantes não conseguirão ver quem tiraram até que um novo sorteio seja feito.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showResetConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeResetDraw"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/30"
                    >
                        Sim, Reiniciar Sorteio
                    </button>
                </div>
            </div>
        </div>
        <!-- MODAL: Compartilhar Genérico / Outras Opções -->
        <div v-if="showShareModal && currentSharePayload" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="w-full max-w-lg rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 sm:p-7 shadow-2xl space-y-5 animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                            <Share2 class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                                {{ currentSharePayload.title }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                Escolha como deseja compartilhar ou copie a mensagem:
                            </p>
                        </div>
                    </div>
                    <button @click="showShareModal = false" class="text-slate-400 hover:text-slate-900 dark:hover:text-white p-1 rounded-lg">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Channels Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <!-- WhatsApp -->
                    <button
                        @click="shareViaWhatsApp(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800/80 transition-all group"
                    >
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <MessageCircle class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">WhatsApp</span>
                    </button>

                    <!-- Telegram -->
                    <button
                        @click="shareViaTelegram(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-2xl bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-100 dark:hover:bg-sky-900/40 border border-sky-200 dark:border-sky-800/80 transition-all group"
                    >
                        <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <Send class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-bold text-sky-800 dark:text-sky-300">Telegram</span>
                    </button>

                    <!-- Email -->
                    <button
                        @click="shareViaEmail(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800/80 transition-all group"
                    >
                        <div class="w-8 h-8 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <Mail class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-bold text-indigo-800 dark:text-indigo-300">E-mail</span>
                    </button>

                    <!-- Copiar Link -->
                    <button
                        @click="copyPayloadUrl(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-all group"
                    >
                        <div class="w-8 h-8 rounded-xl bg-slate-700 text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <Check v-if="copiedPayloadLink" class="w-4 h-4 text-emerald-400" />
                            <Copy v-else class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            {{ copiedPayloadLink ? 'Copiado!' : 'Copiar Link' }}
                        </span>
                    </button>
                </div>

                <!-- Preview Box with Copy Full Message Button -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            Mensagem Pronta para Envio
                        </label>
                        <button
                            @click="copyPayloadFullText(currentSharePayload)"
                            class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline"
                        >
                            <Check v-if="copiedFullMessage" class="w-3.5 h-3.5 text-emerald-500" />
                            <Copy v-else class="w-3.5 h-3.5" />
                            <span>{{ copiedFullMessage ? 'Mensagem Copiada!' : 'Copiar Texto Completo' }}</span>
                        </button>
                    </div>

                    <textarea
                        readonly
                        rows="5"
                        :value="currentSharePayload.text"
                        class="w-full text-xs font-sans rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 p-3.5 focus:outline-none select-all resize-none leading-relaxed"
                    ></textarea>
                </div>

                <div class="flex justify-end pt-1 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="button"
                        @click="showShareModal = false"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
