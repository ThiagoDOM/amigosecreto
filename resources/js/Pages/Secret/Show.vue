<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import WishlistDisplay from '@/Components/WishlistDisplay.vue';
import { formatDate, formatCurrency, parseWishlist } from '@/Utils/formatters';
import confetti from 'canvas-confetti';
import { 
    Gift, 
    DollarSign, 
    Calendar, 
    MapPin, 
    Edit, 
    Check, 
    X, 
    PartyPopper, 
    Lock, 
    Heart, 
    ThumbsDown, 
    Copy,
    Info,
    Bookmark,
    ChevronDown,
    Download,
    Smartphone,
    Share2,
    MessageCircle
} from 'lucide-vue-next';

const props = defineProps({
    participant: {
        type: Object,
        required: true,
    },
    event: {
        type: Object,
        required: true,
    },
    secret_friend: {
        type: Object,
        default: null,
    },
});

const isRevealed = ref(props.participant.is_revealed);
const isOpening = ref(false);
const showEditWishlistModal = ref(false);
const copiedPageLink = ref(false);
const showSaveDropdown = ref(false);
const showHomeScreenModal = ref(false);
const clientToast = ref('');
let toastTimeout = null;

const wishlistForm = useForm({
    wishlist: parseWishlist(props.participant.wishlist),
    clothing_size: props.participant.clothing_size || '',
    shoe_size: props.participant.shoe_size || '',
    likes: props.participant.likes || '',
    dislikes: props.participant.dislikes || '',
});

function fireCelebrationConfetti() {
    const count = 200;
    const defaults = {
        origin: { y: 0.7 },
        zIndex: 9999,
    };

    function fire(particleRatio, opts) {
        confetti({
            ...defaults,
            ...opts,
            particleCount: Math.floor(count * particleRatio)
        });
    }

    fire(0.25, {
        spread: 26,
        startVelocity: 55,
    });
    fire(0.2, {
        spread: 60,
    });
    fire(0.35, {
        spread: 100,
        decay: 0.91,
        scalar: 0.8
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 25,
        decay: 0.92,
        scalar: 1.2
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 45,
    });
}

function revealFriend() {
    if (isOpening.value || isRevealed.value) return;

    isOpening.value = true;

    // Trigger visual confetti burst
    setTimeout(() => {
        fireCelebrationConfetti();
        isRevealed.value = true;
        isOpening.value = false;

        // Post reveal to backend to record timestamp
        router.post(route('secret.reveal', props.participant.token), {}, {
            preserveScroll: true,
        });
    }, 900);
}

function submitWishlist() {
    wishlistForm.put(route('secret.wishlist.update', props.participant.token), {
        onSuccess: () => {
            showEditWishlistModal.value = false;
        }
    });
}

function showToast(msg) {
    clientToast.value = msg;
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
        clientToast.value = '';
    }, 4000);
}

function handleCopyLink() {
    navigator.clipboard.writeText(window.location.href);
    copiedPageLink.value = true;
    showToast('Link copiado com sucesso! Guarde para acessar depois.');
    setTimeout(() => {
        copiedPageLink.value = false;
        showSaveDropdown.value = false;
    }, 1500);
}

function handleAddToHomeScreen() {
    showSaveDropdown.value = false;
    showHomeScreenModal.value = true;
}

function handleShareWhatsApp() {
    showSaveDropdown.value = false;
    const msg = `🎁 Meu link de acesso ao Amigo Secreto (${props.event.title}):\n${window.location.href}\n\nGuarde esta mensagem para consultar seu amigo secreto e a lista de presentes!`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`, '_blank');
}

function handleDownloadImage() {
    showSaveDropdown.value = false;
    downloadCardImage();
    showToast('Cartão gerado e salvo com sucesso!');
}

function downloadCardImage() {
    const canvas = document.createElement('canvas');
    canvas.width = 900;
    canvas.height = 1100;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    function roundRect(x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    }

    // 1. Background gradient
    const bgGrad = ctx.createLinearGradient(0, 0, 900, 1100);
    bgGrad.addColorStop(0, '#0a0f1d');
    bgGrad.addColorStop(0.5, '#1e112a');
    bgGrad.addColorStop(1, '#090d16');
    ctx.fillStyle = bgGrad;
    ctx.fillRect(0, 0, 900, 1100);

    // Decorative ambient circles
    ctx.save();
    ctx.fillStyle = 'rgba(225, 29, 72, 0.18)';
    ctx.beginPath();
    ctx.arc(100, 100, 300, 0, Math.PI * 2);
    ctx.fill();

    ctx.fillStyle = 'rgba(245, 158, 11, 0.15)';
    ctx.beginPath();
    ctx.arc(800, 950, 320, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();

    // Festive golden stars
    ctx.save();
    const goldenStars = [
        [80, 220, 3], [140, 380, 2], [220, 140, 4], [720, 180, 3], [780, 340, 2],
        [830, 220, 4], [100, 750, 3], [160, 900, 2], [760, 780, 4], [820, 650, 3],
        [280, 960, 3], [650, 920, 2], [70, 520, 2], [840, 480, 3]
    ];
    goldenStars.forEach(([x, y, r]) => {
        ctx.fillStyle = 'rgba(253, 224, 71, 0.8)';
        ctx.beginPath();
        ctx.arc(x, y, r, 0, Math.PI * 2);
        ctx.fill();
    });
    ctx.restore();

    // Outer card border
    ctx.save();
    roundRect(30, 30, 840, 1040, 36);
    ctx.strokeStyle = 'rgba(244, 63, 94, 0.35)';
    ctx.lineWidth = 3;
    ctx.stroke();
    ctx.restore();

    // Header badge
    ctx.save();
    roundRect(310, 60, 280, 44, 22);
    ctx.fillStyle = 'rgba(244, 63, 94, 0.2)';
    ctx.fill();
    ctx.strokeStyle = 'rgba(244, 63, 94, 0.5)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.font = 'bold 16px sans-serif';
    ctx.fillStyle = '#fecdd3';
    ctx.textAlign = 'center';
    ctx.fillText('✨ AMIGO SECRETO ✨', 450, 88);
    ctx.restore();

    // Event Title
    ctx.save();
    ctx.font = 'bold 34px sans-serif';
    ctx.fillStyle = '#ffffff';
    ctx.textAlign = 'center';
    ctx.fillText(props.event.title, 450, 150);

    // Participant Name
    ctx.font = '20px sans-serif';
    ctx.fillStyle = '#94a3b8';
    ctx.fillText(`Cartão de: ${props.participant.name}`, 450, 190);
    ctx.restore();

    // Inner Revealed Friend Card or Pending Card
    if (isRevealed.value && props.secret_friend) {
        ctx.save();
        roundRect(70, 230, 760, 480, 32);
        const innerGrad = ctx.createLinearGradient(70, 230, 830, 710);
        innerGrad.addColorStop(0, 'rgba(30, 41, 59, 0.95)');
        innerGrad.addColorStop(1, 'rgba(15, 23, 42, 0.95)');
        ctx.fillStyle = innerGrad;
        ctx.fill();
        ctx.strokeStyle = 'rgba(245, 158, 11, 0.5)';
        ctx.lineWidth = 2.5;
        ctx.stroke();

        ctx.font = 'bold 20px sans-serif';
        ctx.fillStyle = '#fb7185';
        ctx.textAlign = 'center';
        ctx.fillText('VOCÊ TIROU:', 450, 290);

        ctx.font = 'bold 50px sans-serif';
        const nameGrad = ctx.createLinearGradient(200, 360, 700, 360);
        nameGrad.addColorStop(0, '#fde68a');
        nameGrad.addColorStop(0.5, '#fda4af');
        nameGrad.addColorStop(1, '#f472b6');
        ctx.fillStyle = nameGrad;
        ctx.fillText(props.secret_friend.name, 450, 365);

        ctx.font = 'italic 17px sans-serif';
        ctx.fillStyle = '#cbd5e1';
        ctx.fillText('🤫 Guarde este segredo a sete chaves até a festa!', 450, 415);

        let infoY = 475;
        if (props.event.budget_limit) {
            ctx.font = 'bold 18px sans-serif';
            ctx.fillStyle = '#34d399';
            ctx.fillText(`💰 Valor sugerido: ${formatCurrency(props.event.budget_limit)}`, 450, infoY);
            infoY += 36;
        }
        if (props.event.event_date) {
            ctx.font = 'bold 18px sans-serif';
            ctx.fillStyle = '#f43f5e';
            ctx.fillText(`📅 Confraternização: ${formatDate(props.event.event_date)}`, 450, infoY);
            infoY += 36;
        }
        if (props.event.location) {
            ctx.font = '16px sans-serif';
            ctx.fillStyle = '#a5b4fc';
            ctx.fillText(`📍 ${props.event.location}`, 450, infoY);
            infoY += 36;
        }

        const friendWishes = parseWishlist(props.secret_friend.wishlist);
        if (friendWishes && friendWishes.length > 0) {
            ctx.font = 'bold 16px sans-serif';
            ctx.fillStyle = '#f8fafc';
            ctx.fillText(`🎁 Sugestão de presente: ${friendWishes[0].title}`, 450, 660);
        }
        ctx.restore();
    } else {
        ctx.save();
        roundRect(70, 230, 760, 480, 32);
        ctx.fillStyle = 'rgba(30, 41, 59, 0.95)';
        ctx.fill();
        ctx.strokeStyle = 'rgba(244, 63, 94, 0.4)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.font = 'bold 28px sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.fillText('🔒 Sorteio Pendente', 450, 360);

        ctx.font = '18px sans-serif';
        ctx.fillStyle = '#94a3b8';
        ctx.fillText('O organizador ainda não realizou o sorteio.', 450, 420);
        ctx.fillText('Guarde este cartão para acessar sua página quando sortear!', 450, 460);
        ctx.restore();
    }

    // Link Access Box at Bottom
    ctx.save();
    roundRect(70, 740, 760, 230, 24);
    ctx.fillStyle = 'rgba(15, 23, 42, 0.85)';
    ctx.fill();
    ctx.strokeStyle = 'rgba(51, 65, 85, 0.8)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.font = 'bold 16px sans-serif';
    ctx.fillStyle = '#38bdf8';
    ctx.textAlign = 'center';
    ctx.fillText('SEU LINK EXCLUSIVO DE ACESSO:', 450, 785);

    ctx.font = '15px monospace';
    ctx.fillStyle = '#f1f5f9';
    const currentUrl = window.location.href;
    const displayUrl = currentUrl.length > 58 ? currentUrl.substring(0, 55) + '...' : currentUrl;
    ctx.fillText(displayUrl, 450, 830);

    ctx.font = '14px sans-serif';
    ctx.fillStyle = '#94a3b8';
    ctx.fillText('Você pode voltar a esta página a qualquer momento para ver detalhes.', 450, 885);
    ctx.fillText('Organizado com carinho pelo Amigo Secreto Online 🎄', 450, 925);
    ctx.restore();

    // Footer copyright
    ctx.save();
    ctx.font = '13px sans-serif';
    ctx.fillStyle = '#64748b';
    ctx.textAlign = 'center';
    ctx.fillText('Gerado automaticamente • Amigo Secreto', 450, 1030);
    ctx.restore();

    // Download trigger
    const dataUrl = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = dataUrl;
    const safeName = (props.participant.name || 'amigo').toLowerCase().replace(/[^a-z0-9]/g, '-');
    a.download = `amigo-secreto-${safeName}.png`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

onMounted(() => {
    // If already revealed previously, fire a soft greeting confetti
    if (isRevealed.value) {
        setTimeout(() => {
            confetti({
                particleCount: 50,
                spread: 70,
                origin: { y: 0.6 }
            });
        }, 500);
    }
});
</script>

<template>
    <Head :title="'Amigo Secreto de ' + participant.name" />

    <div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden font-sans py-12 px-4 sm:px-6">
        <ToastNotification />

        <!-- Festive Ambient Blobs -->
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-rose-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-[550px] h-[550px] bg-amber-500/15 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-emerald-500/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto space-y-8">
            <!-- Brand & Page Link Bookmark Dropdown -->
            <div class="flex items-center justify-between">
                <Link :href="route('home')" class="transition-transform hover:scale-105">
                    <ApplicationLogo />
                </Link>

                <!-- Dropdown Salvar Acesso -->
                <div class="relative">
                    <button
                        @click="showSaveDropdown = !showSaveDropdown"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 border border-slate-700 hover:border-slate-600 text-slate-100 hover:text-white transition-all shadow-md active:scale-95"
                    >
                        <Bookmark class="w-4 h-4 text-rose-400" />
                        <span>Salvar Acesso</span>
                        <ChevronDown class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': showSaveDropdown }" />
                    </button>

                    <!-- Backdrop overlay to close dropdown -->
                    <div
                        v-if="showSaveDropdown"
                        @click="showSaveDropdown = false"
                        class="fixed inset-0 z-40"
                    ></div>

                    <!-- Dropdown Content -->
                    <div
                        v-if="showSaveDropdown"
                        class="absolute right-0 mt-2 w-72 rounded-2xl bg-slate-900/95 border border-slate-700/80 p-2 shadow-2xl backdrop-blur-xl z-50 space-y-1 animate-in fade-in zoom-in-95"
                    >
                        <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                            Opções para não perder
                        </div>

                        <!-- 1. Copiar Link -->
                        <button
                            @click="handleCopyLink"
                            class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-slate-800/80 transition-colors group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <Check v-if="copiedPageLink" class="w-4 h-4 text-emerald-400" />
                                <Copy v-else class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    {{ copiedPageLink ? 'Link Copiado!' : 'Copiar link' }}
                                    <span v-if="copiedPageLink" class="text-[10px] text-emerald-400 font-semibold">(Copiado)</span>
                                </div>
                                <div class="text-[11px] text-slate-400 font-medium leading-tight">Copie a URL para abrir quando quiser</div>
                            </div>
                        </button>

                        <!-- 2. Salvar Imagem -->
                        <button
                            @click="handleDownloadImage"
                            class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-slate-800/80 transition-colors group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <Download class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">Salvar imagem do cartão</div>
                                <div class="text-[11px] text-slate-400 font-medium leading-tight">Baixe um lindo cartão para a galeria</div>
                            </div>
                        </button>

                        <!-- 3. Adicionar à tela inicial -->
                        <button
                            @click="handleAddToHomeScreen"
                            class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-slate-800/80 transition-colors group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <Smartphone class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">Adicionar à tela inicial</div>
                                <div class="text-[11px] text-slate-400 font-medium leading-tight">Crie um ícone de app no celular</div>
                            </div>
                        </button>

                        <!-- 4. Guardar no WhatsApp -->
                        <button
                            @click="handleShareWhatsApp"
                            class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-slate-800/80 transition-colors group"
                        >
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <MessageCircle class="w-4 h-4" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">Guardar no WhatsApp</div>
                                <div class="text-[11px] text-slate-400 font-medium leading-tight">Envie o link para você no WhatsApp</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Greeting Card -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                        Espaço Exclusivo
                    </div>

                    <span class="text-xs text-slate-300 font-medium">
                        Evento: <strong class="text-white">{{ event.title }}</strong>
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-black font-display text-white">
                    Olá, <span class="bg-gradient-to-r from-rose-400 to-amber-300 bg-clip-text text-transparent">{{ participant.name }}</span>!
                </h1>

                <!-- Event Details Pills -->
                <div class="pt-2 flex flex-wrap gap-2 text-xs">
                    <div v-if="event.budget_limit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <DollarSign class="w-3.5 h-3.5 text-emerald-400" />
                        Valor sugerido: {{ formatCurrency(event.budget_limit) }}
                    </div>

                    <div v-if="event.event_date" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <Calendar class="w-3.5 h-3.5 text-rose-400" />
                        Confraternização: {{ formatDate(event.event_date) }}
                    </div>

                    <div v-if="event.location" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-100 border border-slate-700 font-bold">
                        <MapPin class="w-3.5 h-3.5 text-indigo-400" />
                        {{ event.location }}
                    </div>
                </div>
            </div>

            <!-- DRAW STATUS 1: Event NOT yet drawn -->
            <div
                v-if="!event.is_drawn"
                class="rounded-3xl bg-slate-900/90 border border-slate-800 p-8 sm:p-12 text-center shadow-2xl backdrop-blur-xl space-y-5"
            >
                <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto shadow-inner">
                    <Lock class="w-10 h-10 animate-pulse-slow" />
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-bold font-display text-white">
                        O sorteio ainda não foi realizado
                    </h2>
                    <p class="text-sm text-slate-300 max-w-md mx-auto leading-relaxed font-medium">
                        O organizador (<strong class="text-white">{{ event.organizer_name }}</strong>) ainda está recebendo as confirmações dos participantes. Assim que o sorteio acontecer, acesse este mesmo link para descobrir quem você tirou!
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-center gap-4 text-xs text-slate-400 font-semibold">
                    <span>Participantes confirmados até agora: <strong class="text-rose-400">{{ event.participants_count }}</strong></span>
                </div>
            </div>

            <!-- DRAW STATUS 2: Event IS drawn -->
            <div v-else class="space-y-8">
                <!-- NOT REVEALED YET: Interactive 3D Gift Box Animation -->
                <div
                    v-if="!isRevealed"
                    class="rounded-3xl bg-gradient-to-br from-rose-950/40 via-slate-900 to-amber-950/40 border border-rose-500/30 p-8 sm:p-12 text-center shadow-2xl backdrop-blur-xl space-y-6"
                >
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                            Sorteio Finalizado!
                        </div>
                        <h2 class="text-3xl font-extrabold font-display text-white">
                            Seu Amigo Secreto já foi sorteado!
                        </h2>
                        <p class="text-sm text-slate-200 max-w-sm mx-auto font-medium">
                            Toque no presente misterioso abaixo para desembrulhar e descobrir quem é.
                        </p>
                    </div>

                    <!-- Interactive Gift Box Button -->
                    <div class="py-6 flex justify-center">
                        <button
                            @click="revealFriend"
                            :disabled="isOpening"
                            class="group relative focus:outline-none transition-transform duration-300 active:scale-95"
                        >
                            <!-- Glow effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-amber-500 rounded-3xl blur-2xl opacity-40 group-hover:opacity-75 transition-opacity"></div>

                            <!-- Gift Box Container -->
                            <div 
                                class="relative w-44 h-44 rounded-3xl bg-gradient-to-tr from-rose-600 via-rose-500 to-amber-500 p-1 shadow-2xl flex items-center justify-center"
                                :class="{ 'animate-bounce': isOpening, 'animate-float': !isOpening }"
                            >
                                <div class="w-full h-full bg-slate-900 rounded-[22px] flex flex-col items-center justify-center p-4 relative overflow-hidden">
                                    <!-- Ribbon Vertical -->
                                    <div class="absolute top-0 bottom-0 w-8 bg-amber-400 shadow-md"></div>
                                    <!-- Ribbon Horizontal -->
                                    <div class="absolute left-0 right-0 h-8 bg-amber-400 shadow-md"></div>

                                    <!-- Bow Ribbon on top -->
                                    <div class="relative z-10 w-16 h-16 rounded-full bg-amber-400 border-4 border-amber-300 shadow-xl flex items-center justify-center">
                                        <Gift class="w-8 h-8 text-rose-700" />
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <div class="text-xs text-slate-300 flex items-center justify-center gap-1.5 font-bold">
                        <PartyPopper class="w-4 h-4 text-amber-400" />
                        <span>Clique no presente para revelar com confetes</span>
                    </div>
                </div>

                <!-- ALREADY REVEALED: Friend details card with celebration -->
                <div
                    v-else
                    class="rounded-3xl bg-gradient-to-br from-rose-900/40 via-slate-900 to-emerald-950/40 border border-emerald-500/30 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            Revelado com Sucesso!
                        </div>

                        <button
                            @click="fireCelebrationConfetti"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-slate-100 transition-colors"
                        >
                            <PartyPopper class="w-3.5 h-3.5 text-amber-400" />
                            Soltar Confetes
                        </button>
                    </div>

                    <!-- Big Secret Friend Reveal Name -->
                    <div class="text-center py-6 space-y-2">
                        <div class="text-sm font-bold uppercase tracking-wider text-rose-300">
                            Você tirou:
                        </div>
                        <div class="text-4xl sm:text-5xl font-black font-display text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-rose-300 to-pink-400 tracking-tight text-glow">
                            {{ secret_friend?.name }}
                        </div>
                        <p class="text-xs text-slate-300 pt-1 font-medium">
                            Shh! 🤫 Guarde este segredo a sete chaves até o dia da festa!
                        </p>
                    </div>

                    <!-- Secret Friend Wishlist & Info with Links -->
                    <div class="space-y-4 pt-2 border-t border-slate-800">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center gap-2">
                            <Gift class="w-4 h-4 text-rose-400" />
                            Lista de Presentes de {{ secret_friend?.name }}
                        </h3>

                        <!-- Structured Wishlist Display with Links -->
                        <WishlistDisplay :wishlist="secret_friend?.wishlist" />

                        <!-- Sizes -->
                        <div class="grid grid-cols-2 gap-3 text-xs pt-1">
                            <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700">
                                <div class="font-bold text-slate-300 mb-0.5">Tamanho de Roupa</div>
                                <div class="text-base font-bold text-white">{{ secret_friend?.clothing_size || '-' }}</div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700">
                                <div class="font-bold text-slate-300 mb-0.5">Tamanho de Calçado</div>
                                <div class="text-base font-bold text-white">{{ secret_friend?.shoe_size || '-' }}</div>
                            </div>
                        </div>

                        <!-- Likes & Dislikes -->
                        <div v-if="secret_friend?.likes" class="p-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 text-xs">
                            <div class="font-bold text-emerald-300 mb-1 flex items-center gap-1.5">
                                <Heart class="w-3.5 h-3.5 text-emerald-400" />
                                O que adora:
                            </div>
                            <div class="text-emerald-100 font-medium leading-relaxed">{{ secret_friend?.likes }}</div>
                        </div>

                        <div v-if="secret_friend?.dislikes" class="p-3.5 rounded-2xl bg-rose-950/40 border border-rose-800/60 text-xs">
                            <div class="font-bold text-rose-300 mb-1 flex items-center gap-1.5">
                                <ThumbsDown class="w-3.5 h-3.5 text-rose-400" />
                                Não gosta / Alergias:
                            </div>
                            <div class="text-rose-100 font-medium leading-relaxed">{{ secret_friend?.dislikes }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participant's OWN Wishlist Box (Can be edited anytime) -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold font-display text-white flex items-center gap-2">
                            <Heart class="w-4 h-4 text-rose-500" />
                            Sua Própria Lista de Presentes
                        </h3>
                        <p class="text-xs text-slate-300 font-medium">
                            Isso é o que quem tirou você está vendo. Você pode atualizar a qualquer momento!
                        </p>
                    </div>

                    <button
                        @click="showEditWishlistModal = true"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-100 bg-slate-800 hover:bg-slate-700 transition-colors border border-slate-700 shadow-sm"
                    >
                        <Edit class="w-3.5 h-3.5 text-rose-400" />
                        Editar Minha Lista
                    </button>
                </div>

                <!-- Structured display of participant's own wishlist -->
                <WishlistDisplay :wishlist="participant.wishlist" />

                <div class="pt-2 flex flex-wrap gap-4 text-slate-300 text-xs border-t border-slate-700/60 font-medium">
                    <span>Roupa: <strong class="text-white">{{ participant.clothing_size || '-' }}</strong></span>
                    <span>Calçado: <strong class="text-white">{{ participant.shoe_size || '-' }}</strong></span>
                    <span v-if="participant.likes">Adora: <strong class="text-white">{{ participant.likes }}</strong></span>
                </div>
            </div>
        </div>

        <!-- MODAL: Editar Minha Própria Lista de Desejos com WishlistEditor -->
        <div v-if="showEditWishlistModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Edit class="w-5 h-5 text-rose-500" />
                        Editar Minha Lista de Presentes
                    </h3>
                    <button @click="showEditWishlistModal = false" class="text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitWishlist" class="space-y-4">
                    <!-- Wishlist Editor with Link Support -->
                    <div>
                        <WishlistEditor v-model="wishlistForm.wishlist" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">Tamanho de Roupa</label>
                            <input
                                v-model="wishlistForm.clothing_size"
                                type="text"
                                placeholder="Ex: M, 42"
                                class="w-full px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">Tamanho de Calçado</label>
                            <input
                                v-model="wishlistForm.shoe_size"
                                type="text"
                                placeholder="Ex: 40"
                                class="w-full px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">O que você adora?</label>
                        <input
                            v-model="wishlistForm.likes"
                            type="text"
                            placeholder="Ex: Séries, café, chocolate amargo"
                            class="w-full px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200 mb-1">O que você NÃO gosta / Alergia</label>
                        <input
                            v-model="wishlistForm.dislikes"
                            type="text"
                            placeholder="Ex: Lactose, cheiros fortes"
                            class="w-full px-4 py-2 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        />
                    </div>

                    <div class="pt-3 flex justify-end gap-3 border-t border-slate-800">
                        <button
                            type="button"
                            @click="showEditWishlistModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="wishlistForm.processing"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 shadow-md"
                        >
                            {{ wishlistForm.processing ? 'Salvando...' : 'Salvar Minha Lista' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Adicionar à Tela Inicial -->
        <div v-if="showHomeScreenModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-700/80 p-6 sm:p-7 shadow-2xl space-y-5 animate-in zoom-in-95 duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center shadow-inner">
                            <Smartphone class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold font-display text-white">Adicionar à Tela Inicial</h3>
                            <p class="text-xs text-slate-400">Acesse com 1 toque como um aplicativo</p>
                        </div>
                    </div>
                    <button @click="showHomeScreenModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- iOS Safari -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-2">
                        <div class="font-bold text-white flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-700 text-indigo-300 inline-flex items-center justify-center text-[12px]">🍎</span>
                            iPhone / iPad (Safari)
                        </div>
                        <ol class="space-y-1.5 text-slate-300 list-decimal list-inside pl-1 leading-relaxed">
                            <li>Toque no botão <strong class="text-white">Compartilhar</strong> (ícone ⎋ na barra inferior do Safari).</li>
                            <li>Role para baixo e toque em <strong class="text-white">"Adicionar à Tela de Início"</strong>.</li>
                            <li>Toque em <strong class="text-white">"Adicionar"</strong> no canto superior direito.</li>
                        </ol>
                    </div>

                    <!-- Android Chrome -->
                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-2">
                        <div class="font-bold text-white flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-700 text-emerald-300 inline-flex items-center justify-center text-[12px]">🤖</span>
                            Android (Google Chrome)
                        </div>
                        <ol class="space-y-1.5 text-slate-300 list-decimal list-inside pl-1 leading-relaxed">
                            <li>Toque no menu de <strong class="text-white">três pontinhos (⋮)</strong> no canto superior direito.</li>
                            <li>Toque na opção <strong class="text-white">"Adicionar à tela inicial"</strong> ou <strong class="text-white">"Instalar aplicativo"</strong>.</li>
                            <li>Confirme tocando em <strong class="text-white">"Adicionar"</strong>.</li>
                        </ol>
                    </div>
                </div>

                <button
                    @click="showHomeScreenModal = false"
                    class="w-full py-3 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-indigo-600 to-rose-600 hover:from-indigo-500 hover:to-rose-500 shadow-lg shadow-indigo-900/30 transition-all active:scale-95"
                >
                    Entendido, fechar
                </button>
            </div>
        </div>

        <!-- Floating Client Toast Feedback -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="clientToast"
                class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-emerald-950/95 border border-emerald-500/40 text-emerald-100 shadow-2xl text-xs font-bold backdrop-blur-xl"
            >
                <Check class="w-4 h-4 text-emerald-400 shrink-0" />
                <span>{{ clientToast }}</span>
            </div>
        </transition>
    </div>
</template>
