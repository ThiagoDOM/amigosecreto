<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import WishlistDisplay from '@/Components/WishlistDisplay.vue';
import { formatDate, formatPhone, maskPhone, formatCurrency } from '@/Utils/formatters';
import { THEMES, getTheme } from '@/Utils/themes';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
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
    ExternalLink, 
    Search,
    MessageCircle,
    Send,
    Mail,
    CheckCircle2,
    Loader2,
    UserCheck
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

const page = usePage();

// Clipboard state
const copiedInvite = ref(false);
const copiedMagic = ref(null);
const searchQuery = ref('');

// Modals state
const showAddParticipantModal = ref(false);
const showExclusionsModal = ref(false);
const showEditEventModal = ref(false);
const showWishlistModal = ref(false);
const showParticipantDetailsModal = ref(false);
const showDrawConfirmModal = ref(false);
const showResetConfirmModal = ref(false);
const showDeleteParticipantModal = ref(false);
const showDeleteExclusionModal = ref(false);
const showShareModal = ref(false);
const currentSharePayload = ref(null);
const copiedPayloadLink = ref(false);
const copiedFullMessage = ref(false);
const copiedParticipantModalLink = ref(false);

const isDrawing = ref(false);
const showDrawnFriend = ref(false);

const selectedParticipant = ref(null);
const selectedParticipantWishlist = ref(null);
const participantToDelete = ref(null);
const exclusionToDelete = ref(null);

const isOrganizerParticipating = computed(() => {
    const userEmail = page.props.auth?.user?.email?.toLowerCase().trim();
    const userName = page.props.auth?.user?.name?.toLowerCase().trim();
    return props.participants.some(p => {
        const pEmail = p.email?.toLowerCase().trim();
        const pName = p.name?.toLowerCase().trim();
        return (userEmail && pEmail === userEmail) || (userName && pName === userName);
    });
});

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

function onAddParticipantPhoneInput(e) {
    const masked = maskPhone(e.target.value);
    addParticipantForm.phone = masked;
    e.target.value = masked;
}

const addExclusionForm = useForm({
    participant_id: '',
    excluded_participant_id: '',
    bidirectional: true,
});

const currentTheme = computed(() => getTheme(props.event?.theme));

const editEventForm = useForm({
    title: props.event.title,
    theme: props.event.theme || 'traditional',
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
    const text = `*Convite para o Amigo Secreto: ${props.event.title}*!\n\n` +
        `Olá! Estou organizando nosso amigo secreto no GiftMatch.\n` +
        (props.event.theme && props.event.theme !== 'traditional' ? `${currentTheme.value.emoji} *Tema:* ${currentTheme.value.label}\n` : '') +
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
    let text = '';
    if (props.event.is_drawn) {
        text = `🎉 Olá *${p.name}*!\n\n` +
            `O sorteio do Amigo Secreto *${props.event.title}* foi realizado com sucesso!\n\n` +
            `Descubra quem você tirou clicando no seu link exclusivo e secreto abaixo:\n` +
            `${p.magic_url}\n\n` +
            `🤫 Lembre-se: é segredo! Não conte para ninguém e confira a lista de desejos do seu amigo.`;
    } else {
        text = `Olá *${p.name}*!\n\n` +
            `Aqui está o seu link de acesso exclusivo ao Amigo Secreto *${props.event.title}*:\n` +
            `${p.magic_url}\n\n` +
            `📝 Acesse para cadastrar seus presentes desejados, tamanhos e preferências antes do sorteio!`;
    }

    return {
        title: `Amigo Secreto de ${p.name}`,
        text: text,
        url: p.magic_url,
        phone: p.phone ? p.phone.replace(/\D/g, '') : null,
    };
}

function shareInviteWhatsApp() {
    const text = encodeURIComponent(invitePayload.value.text);
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
}

function sendMagicLinkWhatsApp(p) {
    const payload = getParticipantSharePayload(p);
    const text = encodeURIComponent(payload.text);
    const phoneParam = payload.phone ? `phone=${payload.phone}&` : '';
    window.open(`https://api.whatsapp.com/send?${phoneParam}text=${text}`, '_blank');
}

function openShareModal(payload) {
    currentSharePayload.value = payload;
    copiedPayloadLink.value = false;
    copiedFullMessage.value = false;
    showShareModal.value = true;
}

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
    if (isDrawing.value) return;
    isDrawing.value = true;
    router.post(route('events.draw', props.event.id), {}, {
        onFinish: () => {
            isDrawing.value = false;
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

// Open Participant Details Modal (Dados, Presentes e Link)
function openParticipantDetails(p) {
    selectedParticipant.value = p;
    selectedParticipantWishlist.value = p;
    copiedParticipantModalLink.value = false;
    showDrawnFriend.value = false;
    showParticipantDetailsModal.value = true;
}

function copyParticipantModalLink() {
    if (!selectedParticipant.value) return;
    navigator.clipboard.writeText(selectedParticipant.value.magic_url);
    copiedParticipantModalLink.value = true;
    setTimeout(() => {
        copiedParticipantModalLink.value = false;
    }, 2000);
}

// Open Wishlist Modal (alias)
function openWishlist(p) {
    openParticipantDetails(p);
}

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
        <div class="space-y-6">
            <!-- Event Header Card -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 sm:p-7 shadow-sm space-y-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Left: Title & Details -->
                    <div class="space-y-2.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span
                                v-if="event.is_drawn"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30"
                            >
                                <CheckCircle2 class="w-3.5 h-3.5" />
                                <span>Sorteio Realizado em {{ formatDate(event.drawn_at) }}</span>
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30"
                            >
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>Inscrições Abertas</span>
                            </span>

                            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700">
                                #{{ event.code }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-black font-display text-white">
                            {{ event.title }}
                        </h1>

                        <p v-if="event.description" class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            {{ event.description }}
                        </p>

                        <!-- Meta Badges -->
                        <div class="pt-1 flex flex-wrap gap-2 text-xs">
                            <div v-if="event.theme" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border font-medium" :class="currentTheme.badgeClass">
                                <span>{{ currentTheme.emoji }}</span>
                                <span>Tema: {{ currentTheme.label }}</span>
                            </div>

                            <div v-if="event.budget_limit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-200 border border-slate-700 font-medium">
                                <DollarSign class="w-3.5 h-3.5 text-emerald-400" />
                                <span>Valor sugerido: {{ formatCurrency(event.budget_limit) }}</span>
                            </div>

                            <div v-if="event.event_date" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-200 border border-slate-700 font-medium">
                                <Calendar class="w-3.5 h-3.5 text-rose-400" />
                                <span>Festa: {{ formatDate(event.event_date) }}</span>
                            </div>

                            <div v-if="event.draw_date" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-200 border border-slate-700 font-medium">
                                <Calendar class="w-3.5 h-3.5 text-amber-400" />
                                <span>Sorteio: {{ formatDate(event.draw_date) }}</span>
                            </div>

                            <div v-if="event.location" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-200 border border-slate-700 font-medium">
                                <MapPin class="w-3.5 h-3.5 text-indigo-400" />
                                <span>{{ event.location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Edit button -->
                    <div class="flex items-center gap-2 self-start lg:self-center">
                        <button
                            type="button"
                            @click="showEditEventModal = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors"
                        >
                            <Edit class="w-3.5 h-3.5" />
                            <span>Editar Detalhes</span>
                        </button>
                    </div>
                </div>

                <!-- Invite Link Box -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-rose-400 flex items-center gap-1.5">
                            <Share2 class="w-3.5 h-3.5" />
                            <span>Link de Convite para Participantes</span>
                        </div>
                        <div class="text-xs text-slate-300">
                            Compartilhe para seus amigos entrarem no grupo:
                        </div>
                        <div class="text-xs font-mono font-bold text-white select-all truncate max-w-lg">
                            {{ event.invite_url }}
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button
                            type="button"
                            @click="copyInviteLink"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 border border-slate-700 transition-colors"
                            title="Copiar link do convite"
                        >
                            <Check v-if="copiedInvite" class="w-3.5 h-3.5 text-emerald-400" />
                            <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ copiedInvite ? 'Copiado!' : 'Copiar' }}</span>
                        </button>

                        <button
                            type="button"
                            @click="shareInviteWhatsApp"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm"
                            title="Enviar convite no WhatsApp"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>WhatsApp</span>
                        </button>

                        <button
                            type="button"
                            @click="shareInviteGeneric"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors"
                            title="Mais opções de envio"
                        >
                            <Share2 class="w-3.5 h-3.5 text-slate-400" />
                            <span>Mais</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action & Controls Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Add Manual Participant -->
                    <button
                        v-if="!event.is_drawn"
                        type="button"
                        @click="showAddParticipantModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 border border-slate-800 text-slate-200 hover:text-white hover:bg-slate-800 transition-colors"
                    >
                        <PlusCircle class="w-4 h-4 text-rose-400" />
                        <span>Adicionar Manual</span>
                    </button>

                    <!-- Exclusions / Restrictions -->
                    <button
                        v-if="!event.is_drawn"
                        type="button"
                        @click="showExclusionsModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 border border-slate-800 text-slate-200 hover:text-white hover:bg-slate-800 transition-colors"
                    >
                        <Sliders class="w-4 h-4 text-amber-400" />
                        <span>Regras de Casais ({{ exclusions.length }})</span>
                    </button>

                    <!-- Reset Draw -->
                    <button
                        v-if="event.is_drawn"
                        type="button"
                        @click="showResetConfirmModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 border border-rose-900/40 text-rose-300 hover:bg-rose-950/40 transition-colors"
                    >
                        <RefreshCw class="w-3.5 h-3.5" />
                        <span>Sortear Novamente</span>
                    </button>

                    <!-- Organizer Self-Participation -->
                    <Link
                        v-if="!event.is_drawn && !isOrganizerParticipating"
                        :href="route('invite.show', { code: event.code, name: $page.props.auth?.user?.name, email: $page.props.auth?.user?.email })"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-900/50 hover:text-white transition-all shadow-sm"
                    >
                        <UserCheck class="w-4 h-4 text-emerald-400" />
                        <span>Vou participar</span>
                    </Link>
                </div>

                <!-- Primary Draw Button -->
                <div>
                    <button
                        v-if="!event.is_drawn"
                        type="button"
                        @click="requestDraw"
                        :disabled="!event.can_draw || isDrawing"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white transition-all shadow-sm"
                        :class="(event.can_draw && !isDrawing)
                            ? 'bg-rose-600 hover:bg-rose-500 shadow-rose-900/30 active:scale-95' 
                            : 'bg-slate-800 text-slate-500 cursor-not-allowed'"
                    >
                        <Loader2 v-if="isDrawing" class="w-4 h-4 animate-spin text-white" />
                        <Gift v-else class="w-4 h-4" />
                        <span>{{ isDrawing ? 'Sorteando...' : 'Realizar Sorteio Agora' }}</span>
                    </button>

                    <div v-if="!event.can_draw && !event.is_drawn" class="text-right text-[11px] text-amber-400 mt-1 font-medium">
                        * Mínimo de 3 participantes (atualmente: {{ participants.length }})
                    </div>
                </div>
            </div>

            <!-- Participants Section -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 sm:p-6 shadow-sm space-y-4">
                <!-- Participants List Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="text-base font-bold font-display text-white flex items-center gap-2">
                            <Users class="w-4 h-4 text-rose-400" />
                            <span>Participantes ({{ participants.length }})</span>
                        </h2>
                        <p v-if="event.is_drawn" class="text-xs text-slate-400 mt-0.5">
                            Status de visualização: <strong class="text-emerald-400">{{ revealedCount }} de {{ participants.length }}</strong> já descobriram quem tiraram.
                        </p>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-60">
                        <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar amigo..."
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl text-xs border border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:outline-none focus:border-rose-500"
                        />
                    </div>
                </div>

                <!-- Empty Participants State -->
                <div v-if="participants.length === 0" class="py-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 text-slate-400 flex items-center justify-center mx-auto">
                        <Users class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">Nenhum amigo entrou ainda</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto mt-0.5">
                            Compartilhe o link de convite no WhatsApp ou adicione participantes manualmente.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="shareInviteWhatsApp"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-sm"
                    >
                        <MessageCircle class="w-3.5 h-3.5" />
                        <span>Convidar Pelo WhatsApp</span>
                    </button>
                </div>

                <!-- Participants List -->
                <div v-else class="divide-y divide-slate-800/80">
                    <div
                        v-for="p in filteredParticipants"
                        :key="p.id"
                        class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/40 rounded-xl px-2.5 transition-colors"
                    >
                        <!-- Participant Info -->
                        <div class="flex items-center gap-3 min-w-0">
                            <button
                                type="button"
                                @click="openParticipantDetails(p)"
                                class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 text-rose-300 font-bold flex items-center justify-center text-xs shrink-0 hover:border-rose-500 hover:text-white transition-colors cursor-pointer"
                                :title="'Ver dados de ' + p.name"
                            >
                                {{ p.name.charAt(0).toUpperCase() }}
                            </button>

                            <div class="space-y-0.5 min-w-0">
                                <div class="font-semibold text-xs sm:text-sm text-white flex items-center gap-2 truncate">
                                    <button
                                        type="button"
                                        @click="openParticipantDetails(p)"
                                        class="truncate hover:text-rose-400 text-left font-semibold transition-colors cursor-pointer focus:outline-none"
                                        :title="'Ver detalhes, preferências e link de ' + p.name"
                                    >
                                        {{ p.name }}
                                    </button>
                                    <span v-if="p.is_revealed" class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-800/60 shrink-0">
                                        <Eye class="w-3 h-3" />
                                        <span>Já viu</span>
                                    </span>
                                    <span v-else-if="event.is_drawn" class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 bg-slate-800 px-2 py-0.5 rounded shrink-0">
                                        <EyeOff class="w-3 h-3" />
                                        <span>Pendente</span>
                                    </span>
                                </div>

                                <div class="text-[11px] text-slate-400 flex flex-wrap items-center gap-2">
                                    <span v-if="p.phone">{{ formatPhone(p.phone) }}</span>
                                    <span v-if="p.email" class="truncate">{{ p.email }}</span>
                                    <button
                                        type="button"
                                        @click="openParticipantDetails(p)"
                                        class="text-rose-400 hover:underline font-semibold flex items-center gap-1 cursor-pointer"
                                    >
                                        <Gift class="w-3 h-3" />
                                        <span>Ver Detalhes / Desejos</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Actions per Participant -->
                        <div class="flex items-center gap-1.5 self-end sm:self-center shrink-0">
                            <!-- BOTAO DE OLHO: Visivel tanto antes quanto depois do sorteio -->
                            <button
                                type="button"
                                @click="openParticipantDetails(p)"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 border border-slate-700 transition-colors cursor-pointer"
                                title="Visualizar dados, escolhas de presentes e link deste participante"
                            >
                                <Eye class="w-3.5 h-3.5 text-rose-400" />
                                <span class="hidden sm:inline">Ver dados</span>
                            </button>

                            <!-- When Drawn: Magic Link, WhatsApp & Generic Share buttons -->
                            <template v-if="event.is_drawn">
                                <button
                                    type="button"
                                    @click="copyMagicLink(p)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-200 hover:text-white hover:bg-slate-700 border border-slate-700 transition-colors cursor-pointer"
                                    :title="copiedMagic === p.id ? 'Copiado!' : 'Copiar link mágico'"
                                >
                                    <Check v-if="copiedMagic === p.id" class="w-3 h-3 text-emerald-400" />
                                    <Copy v-else class="w-3 h-3 text-slate-400" />
                                    <span>{{ copiedMagic === p.id ? 'Copiado!' : 'Copiar' }}</span>
                                </button>

                                <button
                                    type="button"
                                    @click="sendMagicLinkWhatsApp(p)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-300 hover:text-white bg-emerald-950/60 hover:bg-emerald-900/60 border border-emerald-800/60 transition-colors cursor-pointer"
                                    title="Enviar link secreto pelo WhatsApp"
                                >
                                    <MessageCircle class="w-3 h-3" />
                                    <span>WhatsApp</span>
                                </button>

                                <button
                                    type="button"
                                    @click="shareParticipantGeneric(p)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                                    title="Mais opções de envio"
                                >
                                    <Share2 class="w-3.5 h-3.5" />
                                </button>

                                <a
                                    :href="p.magic_url"
                                    target="_blank"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                                    title="Abrir página do amigo secreto"
                                >
                                    <ExternalLink class="w-3.5 h-3.5" />
                                </a>
                            </template>

                            <!-- When Not Drawn: Fast link copy, WhatsApp share & Delete button -->
                            <template v-else>
                                <button
                                    type="button"
                                    @click="copyMagicLink(p)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                                    :title="copiedMagic === p.id ? 'Copiado!' : 'Copiar link individual do participante'"
                                >
                                    <Check v-if="copiedMagic === p.id" class="w-4 h-4 text-emerald-400" />
                                    <Copy v-else class="w-4 h-4" />
                                </button>

                                <button
                                    type="button"
                                    @click="sendMagicLinkWhatsApp(p)"
                                    class="p-1.5 rounded-lg text-emerald-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                                    title="Enviar link de acesso pelo WhatsApp"
                                >
                                    <MessageCircle class="w-4 h-4" />
                                </button>

                                <button
                                    type="button"
                                    @click="requestDeleteParticipant(p)"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors cursor-pointer"
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

        <!-- MODAL: Confirmação de Sorteio -->
        <div v-if="showDrawConfirmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto">
                    <Gift class="w-6 h-6" />
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-lg font-bold font-display text-white">
                        Realizar o Sorteio Agora?
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        O algoritmo sorteará aleatoriamente os amigos secretos, respeitando as regras de restrições. Cada participante receberá seu link secreto exclusivo.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDrawConfirmModal = false"
                        :disabled="isDrawing"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDraw"
                        :disabled="isDrawing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 disabled:opacity-60 disabled:cursor-not-allowed shadow-sm transition-all"
                    >
                        <Loader2 v-if="isDrawing" class="w-4 h-4 animate-spin text-white" />
                        <span>{{ isDrawing ? 'Sorteando e enviando...' : 'Confirmar e Sortear' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Exclusão de Participante -->
        <div v-if="showDeleteParticipantModal && participantToDelete" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mx-auto">
                    <Trash2 class="w-6 h-6" />
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-base font-bold font-display text-white">
                        Remover {{ participantToDelete.name }}?
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Tem certeza de que deseja remover este participante do grupo?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDeleteParticipantModal = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDeleteParticipant"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm"
                    >
                        Sim, Remover
                    </button>
                </div>
            </div>
        </div>


        <!-- MODAL: Adicionar Participante Manual -->
        <div v-if="showAddParticipantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-xl rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
                        <PlusCircle class="w-4 h-4 text-rose-400" />
                        <span>Adicionar Participante Manual</span>
                    </h3>
                    <button type="button" @click="showAddParticipantModal = false" class="text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitAddParticipant" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">
                            Nome Completo <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="addParticipantForm.name"
                            type="text"
                            placeholder="Ex: Tio Roberto"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">WhatsApp / Telefone</label>
                            <input
                                :value="addParticipantForm.phone"
                                @input="onAddParticipantPhoneInput"
                                maxlength="15"
                                type="tel"
                                placeholder="(11) 98765-4321"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">E-mail (opcional)</label>
                            <input
                                v-model="addParticipantForm.email"
                                type="email"
                                placeholder="roberto@email.com"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <!-- Structured Wishlist Editor with Links -->
                    <div class="pt-1">
                        <WishlistEditor v-model="addParticipantForm.wishlist" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">Tamanho Roupa</label>
                            <input
                                v-model="addParticipantForm.clothing_size"
                                type="text"
                                placeholder="Ex: M ou 42"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">Calçado</label>
                            <input
                                v-model="addParticipantForm.shoe_size"
                                type="text"
                                placeholder="Ex: 40"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                        <button
                            type="button"
                            @click="showAddParticipantModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="addParticipantForm.processing"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm"
                        >
                            {{ addParticipantForm.processing ? 'Salvando...' : 'Adicionar Participante' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Restrições de Casais -->
        <div v-if="showExclusionsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
                        <Sliders class="w-4 h-4 text-amber-400" />
                        <span>Regras de Restrição (Casais)</span>
                    </h3>
                    <button type="button" @click="showExclusionsModal = false" class="text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <p class="text-xs text-slate-400 leading-relaxed font-normal">
                    Evite que certas pessoas tirem umas às outras no sorteio (ex: casais, familiares ou amigos que já se presenteiam todo ano).
                </p>

                <!-- Existing Exclusions List -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Restrições Atuais</h4>
                    <div v-if="exclusions.length === 0" class="text-xs text-slate-400 py-3 text-center bg-slate-950 rounded-xl border border-slate-800">
                        Nenhuma restrição configurada. Qualquer um pode tirar qualquer um.
                    </div>
                    <div v-else class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        <div
                            v-for="ex in exclusions"
                            :key="ex.id"
                            class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs"
                        >
                            <span class="text-slate-200">
                                <strong>{{ ex.participant_name }}</strong> não pode tirar <strong>{{ ex.excluded_participant_name }}</strong>
                            </span>
                            <button
                                type="button"
                                @click="requestDeleteExclusion(ex)"
                                class="text-slate-400 hover:text-rose-400 p-1 rounded"
                                title="Remover regra"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Exclusion Form -->
                <form @submit.prevent="submitExclusion" class="pt-3 border-t border-slate-800 space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Nova Restrição</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-300 mb-1">Participante:</label>
                            <select
                                v-model="addExclusionForm.participant_id"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-700 bg-slate-950 text-white focus:outline-none focus:border-amber-500"
                                required
                            >
                                <option value="" disabled>Selecione um amigo</option>
                                <option
                                    v-for="p in participants"
                                    :key="p.id"
                                    :value="p.id"
                                >
                                    {{ p.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-slate-300 mb-1">Não pode tirar:</label>
                            <select
                                v-model="addExclusionForm.excluded_participant_id"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-700 bg-slate-950 text-white focus:outline-none focus:border-amber-500"
                                required
                            >
                                <option value="" disabled>Selecione a restrição</option>
                                <option
                                    v-for="p in participants.filter(p => p.id !== addExclusionForm.participant_id)"
                                    :key="p.id"
                                    :value="p.id"
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
                            class="rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500/20"
                        />
                        <label for="bidirectional" class="text-xs text-slate-300 cursor-pointer">
                            Regra Mútua (Casal: ambos não podem tirar um ao outro)
                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            :disabled="addExclusionForm.processing || !addExclusionForm.participant_id || !addExclusionForm.excluded_participant_id"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 transition-colors shadow-sm disabled:opacity-40"
                        >
                            {{ addExclusionForm.processing ? 'Adicionando...' : 'Adicionar Restrição' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Confirmação de Exclusão de Regra (Aparece sobre o Modal de Restrições) -->
        <div v-if="showDeleteExclusionModal && exclusionToDelete" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
            <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-4 text-center animate-in zoom-in-95">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mx-auto">
                    <AlertTriangle class="w-6 h-6" />
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-base font-bold font-display text-white">
                        Remover Restrição?
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Deseja remover a regra de que <strong>{{ exclusionToDelete.participant_name }}</strong> não pode tirar <strong>{{ exclusionToDelete.excluded_participant_name }}</strong>?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showDeleteExclusionModal = false; exclusionToDelete = null"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeDeleteExclusion"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm cursor-pointer"
                    >
                        Sim, Remover Regra
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Editar Evento -->
        <div v-if="showEditEventModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
                        <Edit class="w-4 h-4 text-rose-400" />
                        <span>Editar Evento</span>
                    </h3>
                    <button type="button" @click="showEditEventModal = false" class="text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEditEvent" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Nome do Evento</label>
                        <input
                            v-model="editEventForm.title"
                            type="text"
                            class="w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-950 text-sm text-white focus:outline-none focus:border-rose-500"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Tema do Amigo Secreto</label>
                        <select
                            v-model="editEventForm.theme"
                            class="w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-950 text-sm text-white focus:outline-none focus:border-rose-500"
                        >
                            <option v-for="(tItem, tKey) in THEMES" :key="tKey" :value="tKey">
                                {{ tItem.emoji }} {{ tItem.label }} - {{ tItem.tagline }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Valor Sugerido (R$)</label>
                        <input
                            v-model="editEventForm.budget_limit"
                            type="number"
                            step="0.50"
                            class="w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-950 text-sm text-white focus:outline-none focus:border-rose-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1">Data Sorteio</label>
                            <input
                                v-model="editEventForm.draw_date"
                                type="date"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-700 bg-slate-950 text-white focus:outline-none focus:border-rose-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1">Data Festa</label>
                            <input
                                v-model="editEventForm.event_date"
                                type="date"
                                class="w-full px-3 py-2 rounded-xl text-xs border border-slate-700 bg-slate-950 text-white focus:outline-none focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Local</label>
                        <input
                            v-model="editEventForm.location"
                            type="text"
                            class="w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-950 text-sm text-white focus:outline-none focus:border-rose-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Descrição e Regras</label>
                        <textarea
                            v-model="editEventForm.description"
                            rows="3"
                            class="w-full px-4 py-2 rounded-xl border border-slate-700 bg-slate-950 text-sm text-white focus:outline-none focus:border-rose-500"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                        <button
                            type="button"
                            @click="showEditEventModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="editEventForm.processing"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm"
                        >
                            {{ editEventForm.processing ? 'Salvando...' : 'Salvar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Detalhes do Participante (Dados, Presentes e Link) -->
        <div v-if="showParticipantDetailsModal && selectedParticipant" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-xl rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 text-rose-400 font-bold flex items-center justify-center text-sm shadow-inner">
                            {{ selectedParticipant.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base sm:text-lg flex items-center gap-2">
                                <span>{{ selectedParticipant.name }}</span>
                            </h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span v-if="selectedParticipant.is_revealed" class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-800/60">
                                    <Eye class="w-3 h-3" />
                                    <span>Já visualizou o amigo tirado</span>
                                </span>
                                <span v-else-if="event.is_drawn" class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-400 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-800/60">
                                    <EyeOff class="w-3 h-3" />
                                    <span>Pendente de visualização</span>
                                </span>
                                <span v-else class="inline-flex items-center gap-1 text-[11px] font-medium text-indigo-300 bg-indigo-950/60 px-2 py-0.5 rounded border border-indigo-800/60">
                                    <span>Inscrito • Aguardando sorteio</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="showParticipantDetailsModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Section 1: Link Mágico Exclusivo & Ações Rápidas -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                            <Share2 class="w-3.5 h-3.5 text-rose-400" />
                            <span>Link Individual de Acesso</span>
                        </span>
                        <span class="text-[11px] text-slate-400">Exclusivo de {{ selectedParticipant.name.split(' ')[0] }}</span>
                    </div>

                    <!-- URL display input -->
                    <div class="flex items-center gap-2">
                        <input
                            :value="selectedParticipant.magic_url"
                            readonly
                            class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-slate-200 select-all focus:outline-none"
                        />
                    </div>

                    <!-- Action buttons grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                        <button
                            type="button"
                            @click="copyParticipantModalLink"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition-colors cursor-pointer"
                        >
                            <Check v-if="copiedParticipantModalLink" class="w-3.5 h-3.5 text-emerald-400" />
                            <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ copiedParticipantModalLink ? 'Copiado!' : 'Copiar Link' }}</span>
                        </button>

                        <button
                            type="button"
                            @click="sendMagicLinkWhatsApp(selectedParticipant)"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-950/60 hover:bg-emerald-900/60 text-emerald-300 border border-emerald-800/60 transition-colors cursor-pointer"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>WhatsApp</span>
                        </button>

                        <button
                            type="button"
                            @click="shareParticipantGeneric(selectedParticipant)"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors cursor-pointer"
                        >
                            <Share2 class="w-3.5 h-3.5 text-indigo-400" />
                            <span>Compartilhar</span>
                        </button>

                        <a
                            :href="selectedParticipant.magic_url"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors cursor-pointer"
                        >
                            <ExternalLink class="w-3.5 h-3.5 text-amber-400" />
                            <span>Abrir Link</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: Dados de Contato -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <div class="text-slate-400 mb-0.5 font-medium flex items-center gap-1.5">
                            <MessageCircle class="w-3.5 h-3.5 text-emerald-400" />
                            <span>WhatsApp / Telefone</span>
                        </div>
                        <div class="text-white font-bold">
                            {{ selectedParticipant.phone ? formatPhone(selectedParticipant.phone) : 'Não informado' }}
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <div class="text-slate-400 mb-0.5 font-medium flex items-center gap-1.5">
                            <Mail class="w-3.5 h-3.5 text-indigo-400" />
                            <span>E-mail</span>
                        </div>
                        <div class="text-white font-bold truncate">
                            {{ selectedParticipant.email || 'Não informado' }}
                        </div>
                    </div>
                </div>

                <!-- Section 3: Tamanhos & Preferências -->
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-slate-400 mb-0.5 font-medium">Tamanho de Roupa</div>
                            <div class="text-white font-bold text-sm">{{ selectedParticipant.clothing_size || 'Não informado' }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-slate-400 mb-0.5 font-medium">Tamanho de Calçado</div>
                            <div class="text-white font-bold text-sm">{{ selectedParticipant.shoe_size || 'Não informado' }}</div>
                        </div>
                    </div>

                    <div v-if="selectedParticipant.likes" class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <div class="font-bold text-emerald-400 mb-0.5">O que adora:</div>
                        <div class="text-slate-200">{{ selectedParticipant.likes }}</div>
                    </div>

                    <div v-if="selectedParticipant.dislikes" class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <div class="font-bold text-rose-400 mb-0.5">Não gosta / Alergia:</div>
                        <div class="text-slate-200">{{ selectedParticipant.dislikes }}</div>
                    </div>
                </div>

                <!-- Section 4: Lista de Presentes / Desejos -->
                <div class="space-y-2 text-xs">
                    <div class="font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <Gift class="w-4 h-4 text-rose-400" />
                        <span>Escolhas de Presentes:</span>
                    </div>
                    <WishlistDisplay :wishlist="selectedParticipant.wishlist" />
                </div>

                <!-- Section 5: (Se sorteado) Informação do Amigo Tirado (Organizador) -->
                <div v-if="event.is_drawn && selectedParticipant.drawn_friend" class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-amber-300 flex items-center gap-1.5">
                            <Gift class="w-3.5 h-3.5 text-amber-400" />
                            <span>Amigo Secreto Sorteado:</span>
                        </div>
                        <button
                            type="button"
                            @click="showDrawnFriend = !showDrawnFriend"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition-colors cursor-pointer"
                        >
                            <component :is="showDrawnFriend ? EyeOff : Eye" class="w-3.5 h-3.5" />
                            <span>{{ showDrawnFriend ? 'Ocultar' : 'Revelar' }}</span>
                        </button>
                    </div>
                    <p v-if="showDrawnFriend" class="text-slate-200 font-medium">
                        {{ selectedParticipant.name }} tirou <strong class="text-white text-sm bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/30">{{ selectedParticipant.drawn_friend.name }}</strong>
                    </p>
                    <p v-else class="text-slate-400 font-mono tracking-widest text-xs flex items-center gap-2">
                        <span>{{ selectedParticipant.name }} tirou:</span>
                        <span class="bg-slate-800/90 px-2.5 py-0.5 rounded text-slate-500 select-none">••••••••••••</span>
                    </p>
                    <p class="text-[11px] text-amber-400/80">
                        * Informação confidencial visível apenas para você (organizador). Fica oculta por padrão para evitar spoilers acidentais.
                    </p>
                </div>

                <!-- Footer -->
                <div class="pt-3 flex items-center justify-between border-t border-slate-800">
                    <span class="text-[11px] text-slate-500">Cadastrado em {{ selectedParticipant.created_at }}</span>
                    <button
                        type="button"
                        @click="showParticipantDetailsModal = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition-colors cursor-pointer"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Confirmação de Reinício de Sorteio -->
        <div v-if="showResetConfirmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mx-auto">
                    <AlertTriangle class="w-6 h-6" />
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-base font-bold font-display text-white">
                        Reiniciar o Sorteio?
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Ao reiniciar, os pares sorteados serão apagados e os participantes não conseguirão ver quem tiraram até que um novo sorteio seja feito.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        type="button"
                        @click="showResetConfirmModal = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="executeResetDraw"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm"
                    >
                        Sim, Reiniciar Sorteio
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Compartilhar Genérico -->
        <div v-if="showShareModal && currentSharePayload" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                            <Share2 class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold font-display text-white">
                                {{ currentSharePayload.title }}
                            </h3>
                            <p class="text-xs text-slate-400">
                                Escolha como deseja compartilhar:
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showShareModal = false" class="text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Channels Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <!-- WhatsApp -->
                    <button
                        type="button"
                        @click="shareViaWhatsApp(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all"
                    >
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <MessageCircle class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold text-emerald-300">WhatsApp</span>
                    </button>

                    <!-- Telegram -->
                    <button
                        type="button"
                        @click="shareViaTelegram(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all"
                    >
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center">
                            <Send class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold text-sky-300">Telegram</span>
                    </button>

                    <!-- Email -->
                    <button
                        type="button"
                        @click="shareViaEmail(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all"
                    >
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                            <Mail class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold text-indigo-300">E-mail</span>
                    </button>

                    <!-- Copiar Link -->
                    <button
                        type="button"
                        @click="copyPayloadUrl(currentSharePayload)"
                        class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all"
                    >
                        <div class="w-8 h-8 rounded-lg bg-slate-800 text-slate-300 flex items-center justify-center">
                            <Check v-if="copiedPayloadLink" class="w-4 h-4 text-emerald-400" />
                            <Copy v-else class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-semibold text-slate-300">
                            {{ copiedPayloadLink ? 'Copiado!' : 'Copiar Link' }}
                        </span>
                    </button>
                </div>

                <!-- Preview Box with Copy Full Message Button -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Mensagem Pronta
                        </label>
                        <button
                            type="button"
                            @click="copyPayloadFullText(currentSharePayload)"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-rose-400 hover:text-rose-300"
                        >
                            <Check v-if="copiedFullMessage" class="w-3.5 h-3.5 text-emerald-400" />
                            <Copy v-else class="w-3.5 h-3.5" />
                            <span>{{ copiedFullMessage ? 'Copiado!' : 'Copiar Texto' }}</span>
                        </button>
                    </div>

                    <textarea
                        readonly
                        rows="4"
                        :value="currentSharePayload.text"
                        class="w-full text-xs font-sans rounded-xl bg-slate-950 border border-slate-800 text-slate-300 p-3 focus:outline-none select-all resize-none leading-relaxed"
                    ></textarea>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-800">
                    <button
                        type="button"
                        @click="showShareModal = false"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-800"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
