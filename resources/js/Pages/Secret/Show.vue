<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import GoogleAd from '@/Components/GoogleAd.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import WishlistEditor from '@/Components/WishlistEditor.vue';
import WishlistDisplay from '@/Components/WishlistDisplay.vue';
import AffiliateRecommendations from '@/Components/AffiliateRecommendations.vue';
import GiftSuggestionsModal from '@/Components/GiftSuggestionsModal.vue';
import SecretNotesChat from '@/Components/SecretNotesChat.vue';
import { formatDate, formatCurrency, parseWishlist } from '@/Utils/formatters';
import { getTheme } from '@/Utils/themes';
import { drawQrCodeToCanvas } from '@/Utils/qrcode';
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
    MessageCircle,
    Bell,
    BellRing,
    CheckCircle2,
    Clock,
    Eye,
    LogOut,
    AlertTriangle,
    ShieldCheck,
    ShoppingBag
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
    sent_questions: {
        type: Array,
        default: () => [],
    },
    received_questions: {
        type: Array,
        default: () => [],
    },
    vapid_public_key: {
        type: String,
        default: '',
    },
});

const page = usePage();
const isRevealed = ref(props.participant.is_revealed);
const isOpening = ref(false);
const showEditWishlistModal = ref(false);
const showGiftSuggestionsModal = ref(false);
const copiedPageLink = ref(false);
const showSaveDropdown = ref(false);
const showHomeScreenModal = ref(false);
const showOnboardingSaveModal = ref(false);
const showNotificationsModal = ref(false);
const showDisconnectModal = ref(false);
const showSoftPromptModal = ref(false);
const copiedDisconnectLink = ref(false);
const isDisconnecting = ref(false);
const notificationsEnabled = ref(false);
const notificationSupported = ref(false);
const clientToast = ref('');
let toastTimeout = null;

const currentTheme = computed(() => getTheme(props.event?.theme));

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

function handleCopyDisconnectLink() {
    copyToClipboard(window.location.href);
    copiedDisconnectLink.value = true;
    setTimeout(() => {
        copiedDisconnectLink.value = false;
    }, 2500);
}

function handleConfirmDisconnect() {
    isDisconnecting.value = true;
    router.post(route('participant.session.forget'), {
        token: props.participant.token
    }, {
        onFinish: () => {
            isDisconnecting.value = false;
            showDisconnectModal.value = false;
        }
    });
}

const currentUrl = computed(() => {
    return typeof window !== 'undefined' ? window.location.href : route('secret.show', props.participant.token);
});

const wishlistForm = useForm({
    wishlist: parseWishlist(props.participant.wishlist),
    clothing_size: props.participant.clothing_size || '',
    shoe_size: props.participant.shoe_size || '',
    likes: props.participant.likes || '',
    dislikes: props.participant.dislikes || '',
});

function fireCelebrationConfetti() {
    try {
        const count = 100;
        const defaults = {
            origin: { y: 0.7 },
            zIndex: 9999,
            disableForReducedMotion: true,
        };

        function fire(particleRatio, opts) {
            try {
                confetti({
                    ...defaults,
                    ...opts,
                    particleCount: Math.floor(count * particleRatio)
                });
            } catch (err) {
                // Ignore canvas context errors on restricted WebKit devices
            }
        }

        fire(0.25, { spread: 26, startVelocity: 45 });
        fire(0.2, { spread: 60 });
        fire(0.35, { spread: 90, decay: 0.91, scalar: 0.8 });
    } catch (e) {
        console.warn('Confetti burst notice:', e);
    }
}

function revealFriend() {
    if (isRevealed.value) return;
    if (isOpening.value) return;

    isOpening.value = true;

    // Failsafe timer: guarantees the UI is revealed quickly even if something stalls
    const safetyTimer = setTimeout(() => {
        isRevealed.value = true;
        isOpening.value = false;
    }, 500);

    // Fast, responsive 200ms animation burst
    setTimeout(() => {
        clearTimeout(safetyTimer);

        // Always reveal UI first
        isRevealed.value = true;
        isOpening.value = false;

        // Safely trigger celebration confetti
        try {
            fireCelebrationConfetti();
        } catch (e) {
            console.warn('Confetti error:', e);
        }

        // Post reveal to backend to record timestamp
        router.post(
            route('secret.reveal', props.participant.token),
            {},
            {
                preserveScroll: true,
                onError: (err) => {
                    console.warn('Reveal post sync notice:', err);
                },
                onFinish: () => {
                    isOpening.value = false;
                },
            }
        );
    }, 200);
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
    copyToClipboard(window.location.href);
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
    const msg = `Meu link de acesso ao Amigo Secreto (${props.event.title}):\n${window.location.href}\n\nGuarde esta mensagem para consultar seu amigo secreto e a lista de presentes!`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`, '_blank');
}

function handleDownloadImage() {
    showSaveDropdown.value = false;
    downloadCardImage();
    showToast('Cartão gerado e salvo com sucesso!');
}

function closeOnboardingSaveModal() {
    try {
        localStorage.setItem('onboarding_seen_' + props.participant.token, '1');
    } catch (e) {}
    showOnboardingSaveModal.value = false;
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function subscribeToPushNotifications() {
    if (typeof window === 'undefined') return false;
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return false;
    }

    try {
        let reg = await navigator.serviceWorker.getRegistration();
        if (!reg) {
            reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        }
        await navigator.serviceWorker.ready;

        const effectiveVapidKey = props.vapid_public_key || import.meta.env.VITE_VAPID_PUBLIC_KEY || 'BEo2wID4tug6zKpkta-vbngMpV43FdTUXkToBwTktUVrA3dUYVgWPiQU3OUBad6jXfPBkVi9I9o2Z2gcS4mISUw';
        if (!effectiveVapidKey) return false;

        let sub = await reg.pushManager.getSubscription();
        if (!sub) {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(effectiveVapidKey),
            });
        }

        if (sub) {
            const rawSub = sub.toJSON();
            await axios.post(route('secret.push-subscription.store', props.participant.token), {
                endpoint: rawSub.endpoint,
                keys: rawSub.keys,
            });
            return true;
        }
    } catch (e) {
        console.warn('Push subscription failed:', e);
    }
    return false;
}

onMounted(() => {
    if (typeof window !== 'undefined') {
        try {
            notificationSupported.value = ('Notification' in window);
            if ('Notification' in window && Notification.permission === 'granted') {
                notificationsEnabled.value = true;
                const subKey = 'push_sub_synced_' + props.participant.token;
                if (!localStorage.getItem(subKey)) {
                    subscribeToPushNotifications().then((ok) => {
                        if (ok) {
                            try { localStorage.setItem(subKey, '1'); } catch (e) {}
                        }
                    });
                }
            }
        } catch (e) {}
    }
});

function requestBrowserNotifications() {
    showSoftPromptModal.value = true;
}

async function confirmBrowserNotificationPermission() {
    showSoftPromptModal.value = false;
    if (typeof window === 'undefined') return;

    if (!('Notification' in window)) {
        showToast('Este dispositivo não tem suporte a notificações web.');
        return;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            notificationsEnabled.value = true;
            try {
                localStorage.setItem('notif_granted_' + props.participant.token, '1');
            } catch (e) {}

            // Subscribe to real Web Push on server
            const subscribed = await subscribeToPushNotifications();
            if (subscribed) {
                showToast('🔔 Notificações e avisos no celular ativados com sucesso!');
            } else {
                showToast('🔔 Notificações ativadas no navegador!');
            }

            // Local confirmation via Service Worker
            if ('serviceWorker' in navigator) {
                try {
                    const reg = await navigator.serviceWorker.ready;
                    if (reg && reg.showNotification) {
                        reg.showNotification('GiftMatch 🎁', {
                            body: `Notificações ativadas para "${props.event.title}". Avisaremos sobre o sorteio!`,
                            icon: '/apple-touch-icon.png',
                            badge: '/favicon-32x32.png',
                        });
                        return;
                    }
                } catch (swErr) {
                    console.debug('SW notification notice:', swErr);
                }
            }

            // Fallback for desktop browsers without active SW
            try {
                new Notification('GiftMatch 🎁', {
                    body: `Notificações ativadas para "${props.event.title}". Avisaremos sobre o sorteio!`,
                    icon: '/apple-touch-icon.png',
                });
            } catch (err) {}
        } else if (permission === 'denied') {
            showToast('As notificações foram recusadas no seu navegador.');
        }
    } catch (e) {
        console.debug('Notification request error:', e);
    }
}

async function sendTestNotification() {
    if (typeof window === 'undefined' || !('Notification' in window) || Notification.permission !== 'granted') {
        requestBrowserNotifications();
        return;
    }

    try {
        // Use Service Worker (Android Chrome & iOS PWA compatible)
        if ('serviceWorker' in navigator) {
            try {
                const reg = await navigator.serviceWorker.getRegistration();
                if (reg && reg.showNotification) {
                    reg.showNotification('GiftMatch 🎁 ' + props.event.title, {
                        body: 'Tudo pronto! As notificações estão ativas e funcionando perfeitamente.',
                        icon: '/apple-touch-icon.png',
                        badge: '/favicon-32x32.png',
                    });
                    showToast('🔔 Notificação de teste enviada com sucesso!');
                    return;
                }
            } catch (swErr) {
                console.debug('SW notification notice:', swErr);
            }
        }

        try {
            new Notification('GiftMatch 🎁 ' + props.event.title, {
                body: 'Tudo pronto! As notificações estão ativas e funcionando perfeitamente.',
                icon: '/apple-touch-icon.png',
            });
            showToast('🔔 Notificação de teste enviada com sucesso!');
        } catch (e) {
            showToast('🔔 Notificações ativas neste dispositivo!');
        }
    } catch (e) {
        console.debug('Test notification notice:', e);
        showToast('Não foi possível disparar o teste.');
    }
}

const eventNotifications = computed(() => {
    const list = [];

    // 1. Inscrição / Cadastro no Evento
    list.push({
        id: 'registration',
        title: 'Você entrou no Amigo Secreto',
        description: `Seu cadastro como participante de "${props.event.title}" foi confirmado com sucesso.`,
        date: props.participant.created_at || 'Confirmado',
        status: 'completed',
        badge: 'Confirmado',
        icon: 'check',
        colorClass: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
        badgeClass: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
    });

    // 2. Status das Notificações do Navegador
    if (notificationsEnabled.value) {
        list.push({
            id: 'notifications_status',
            title: 'Notificações do navegador ativadas',
            description: 'Você receberá avisos diretos neste dispositivo quando o sorteio acontecer ou novas atualizações forem publicadas.',
            date: 'Ativo',
            status: 'completed',
            badge: 'Ativado',
            icon: 'bell-ring',
            colorClass: 'text-amber-400 bg-amber-500/10 border-amber-500/30',
            badgeClass: 'bg-amber-500/15 text-amber-300 border-amber-500/30'
        });
    } else {
        list.push({
            id: 'notifications_status',
            title: 'Notificações pendentes de ativação',
            description: 'Ative os avisos no navegador para ser notificado imediatamente assim que o organizador realizar o sorteio.',
            date: 'Pendente',
            status: 'action_required',
            badge: 'Pendente',
            action: 'activate_notifications',
            icon: 'bell',
            colorClass: 'text-amber-400 bg-amber-500/10 border-amber-500/30',
            badgeClass: 'bg-amber-500/15 text-amber-300 border-amber-500/30'
        });
    }

    // 3. Sorteio do Amigo Secreto
    if (props.event.is_drawn) {
        list.push({
            id: 'draw_status',
            title: 'Sorteio realizado com sucesso!',
            description: 'O organizador realizou o sorteio das duplas secretas. O seu amigo secreto já foi definido!',
            date: props.event.drawn_at || 'Sorteio finalizado',
            status: 'completed',
            badge: 'Realizado',
            icon: 'gift',
            colorClass: 'text-rose-400 bg-rose-500/10 border-rose-500/30',
            badgeClass: 'bg-rose-500/15 text-rose-300 border-rose-500/30'
        });
    } else {
        list.push({
            id: 'draw_status',
            title: 'Aguardando sorteio do organizador',
            description: props.event.draw_date
                ? `Sorteio previsto para ${props.event.draw_date}. O organizador realizará o sorteio em breve.`
                : 'O organizador realizará o sorteio assim que todos os participantes entrarem.',
            date: props.event.draw_date ? `Previsto: ${props.event.draw_date}` : 'Aguardando',
            status: 'pending',
            badge: 'Aguardando',
            icon: 'clock',
            colorClass: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/30',
            badgeClass: 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30'
        });
    }

    // 4. Visualização / Revelação do Amigo Secreto
    if (isRevealed.value) {
        list.push({
            id: 'reveal_status',
            title: 'Você visualizou seu amigo secreto',
            description: 'Você abriu o cartão e descobriu quem é o seu amigo secreto! A lista de presentes dele(a) está disponível.',
            date: props.participant.revealed_at || 'Visualizado',
            status: 'completed',
            badge: 'Visualizado',
            icon: 'eye',
            colorClass: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
            badgeClass: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
        });
    } else if (props.event.is_drawn) {
        list.push({
            id: 'reveal_status',
            title: 'Seu amigo secreto está pronto para ser revelado!',
            description: 'Você ainda não abriu sua cartinha secreta. Toque no botão "Revelar Amigo Secreto" na página para descobrir!',
            date: 'Disponível agora',
            status: 'action_required',
            badge: 'Pronto para abrir',
            icon: 'party-popper',
            colorClass: 'text-rose-400 bg-rose-500/10 border-rose-500/30',
            badgeClass: 'bg-rose-500/15 text-rose-300 border-rose-500/30'
        });
    }

    // 5. Data da Confraternização / Evento
    if (props.event.event_date) {
        list.push({
            id: 'event_date',
            title: 'Data da confraternização se aproximando',
            description: `A confraternização acontecerá no dia ${props.event.event_date}${props.event.location ? ' no local: ' + props.event.location : ''}. Não se esqueça de comprar o presente com antecedência!`,
            date: props.event.event_date,
            status: 'info',
            badge: 'Confraternização',
            icon: 'party',
            colorClass: 'text-amber-400 bg-amber-500/10 border-amber-500/30',
            badgeClass: 'bg-amber-500/15 text-amber-300 border-amber-500/30'
        });
    }

    return list;
});

function addToGoogleCalendar() {
    const title = `Sorteio Amigo Secreto: ${props.event.title}`;
    const details = `Hoje é o dia de ver quem você tirou no Amigo Secreto!\n\nLink secreto individual:\n${window.location.href}\n\nOrganizado por: ${props.event.organizer_name}`;
    const location = props.event.location || '';
    
    const dateStr = props.event.raw_draw_date || props.event.raw_event_date;
    let datesParam = '';
    if (dateStr) {
        const clean = dateStr.replace(/-/g, '');
        datesParam = `${clean}T120000/${clean}T130000`;
    } else {
        const future = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
        const y = future.getFullYear();
        const m = String(future.getMonth() + 1).padStart(2, '0');
        const d = String(future.getDate()).padStart(2, '0');
        datesParam = `${y}${m}${d}T120000/${y}${m}${d}T130000`;
    }

    const gUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${datesParam}&details=${encodeURIComponent(details)}&location=${encodeURIComponent(location)}`;
    window.open(gUrl, '_blank');
    showToast('Abrindo Google Agenda...');
}

function downloadCalendarIcs() {
    const dateStr = props.event.raw_draw_date || props.event.raw_event_date;
    let startDate = '';
    let endDate = '';
    if (dateStr) {
        const clean = dateStr.replace(/-/g, '');
        startDate = `${clean}T120000`;
        endDate = `${clean}T130000`;
    } else {
        const future = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000);
        const y = future.getFullYear();
        const m = String(future.getMonth() + 1).padStart(2, '0');
        const d = String(future.getDate()).padStart(2, '0');
        startDate = `${y}${m}${d}T120000`;
        endDate = `${y}${m}${d}T130000`;
    }

    const title = `Sorteio Amigo Secreto: ${props.event.title}`;
    const details = `Seu link exclusivo para ver seu amigo secreto:\\n${window.location.href}`;
    const location = props.event.location || '';
    
    const icsContent = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//GiftMatch//PT',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        `UID:${props.participant.token}@giftmatch.online`,
        `DTSTAMP:${new Date().toISOString().replace(/[-:]/g, '').split('.')[0]}Z`,
        `DTSTART:${startDate}`,
        `DTEND:${endDate}`,
        `SUMMARY:${title}`,
        `DESCRIPTION:${details}`,
        `LOCATION:${location}`,
        `URL:${window.location.href}`,
        'BEGIN:VALARM',
        'TRIGGER:-PT15M',
        'ACTION:DISPLAY',
        'DESCRIPTION:Lembrete: Sorteio do Amigo Secreto',
        'END:VALARM',
        'END:VEVENT',
        'END:VCALENDAR'
    ].join('\r\n');

    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `amigo-secreto-${props.event.id}.ics`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    showToast('Lembrete de calendário baixado! Abra para salvar no seu celular.');
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
    ctx.fillText('GIFTMATCH', 450, 88);
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

        ctx.font = '17px sans-serif';
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
            ctx.fillText(`Sugestão de presente: ${friendWishes[0].title}`, 450, 660);
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

    // Link Access Box at Bottom with QR Code and URL
    ctx.save();
    roundRect(70, 735, 760, 260, 24);
    ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
    ctx.fill();
    ctx.strokeStyle = 'rgba(51, 65, 85, 0.85)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    const currentUrl = window.location.href;

    // 1. Draw QR Code on the left side (crisp white card with dark modules)
    const qrSize = 175;
    const qrX = 95;
    const qrY = 755;
    drawQrCodeToCanvas(ctx, currentUrl, qrX, qrY, qrSize, {
        bgColor: '#ffffff',
        fgColor: '#0f172a',
        padding: 12,
        borderRadius: 16,
    });

    // Caption under QR code
    ctx.font = 'bold 11px sans-serif';
    ctx.fillStyle = '#38bdf8';
    ctx.textAlign = 'center';
    ctx.fillText('📷 ESCANEAR QR CODE', qrX + (qrSize / 2), qrY + qrSize + 22);

    // 2. Right side information (URL and instructions)
    const textStartX = 300;
    ctx.textAlign = 'left';

    ctx.font = 'bold 15px sans-serif';
    ctx.fillStyle = '#38bdf8';
    ctx.fillText('SEU LINK EXCLUSIVO DE ACESSO:', textStartX, 772);

    ctx.font = '12px sans-serif';
    ctx.fillStyle = '#94a3b8';
    ctx.fillText('Aponte a câmera para o QR Code ou acesse a URL:', textStartX, 796);

    // URL container chip
    roundRect(textStartX, 810, 505, 72, 12);
    ctx.fillStyle = 'rgba(30, 41, 59, 0.9)';
    ctx.fill();
    ctx.strokeStyle = 'rgba(71, 85, 105, 0.6)';
    ctx.lineWidth = 1;
    ctx.stroke();

    function wrapUrlText(context, text, maxWidth) {
        if (context.measureText(text).width <= maxWidth) {
            return [text];
        }
        const lines = [];
        let current = '';
        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            const test = current + char;
            if (context.measureText(test).width > maxWidth && current.length > 0) {
                lines.push(current);
                current = char;
            } else {
                current = test;
            }
        }
        if (current.length > 0) {
            lines.push(current);
        }
        return lines;
    }

    ctx.font = '12px monospace';
    ctx.fillStyle = '#f8fafc';
    const urlLines = wrapUrlText(ctx, currentUrl, 480);
    const lineHeight = 18;
    const urlStartY = urlLines.length === 1 ? 848 : (urlLines.length === 2 ? 838 : 828);

    urlLines.slice(0, 3).forEach((line, idx) => {
        ctx.fillText(line, textStartX + 12, urlStartY + (idx * lineHeight));
    });

    ctx.font = '12px sans-serif';
    ctx.fillStyle = '#cbd5e1';
    ctx.fillText('💡 Dica: Salve esta imagem para consultar seus presentes quando quiser.', textStartX, 915);

    ctx.font = 'bold 12px sans-serif';
    ctx.fillStyle = '#f43f5e';
    ctx.fillText('🎄 Organizado com carinho pelo GiftMatch', textStartX, 942);
    ctx.restore();

    // Footer copyright
    ctx.save();
    ctx.font = '13px sans-serif';
    ctx.fillStyle = '#64748b';
    ctx.textAlign = 'center';
    ctx.fillText('Gerado automaticamente • GiftMatch (giftmatch.online)', 450, 1030);
    ctx.restore();

    // Download trigger
    const dataUrl = canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = dataUrl;
    const safeName = (props.participant.name || 'amigo').toLowerCase().replace(/[^a-z0-9]/g, '-');
    a.download = `giftmatch-${safeName}.png`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

onMounted(() => {
    try {
        notificationSupported.value = typeof window !== 'undefined' && ('Notification' in window);
        if (notificationSupported.value && typeof Notification !== 'undefined' && 'permission' in Notification) {
            notificationsEnabled.value = (Notification.permission === 'granted');
        }
    } catch (e) {
        notificationSupported.value = false;
    }

    try {
        const isJustRegistered = page.props.flash?.just_registered || (typeof window !== 'undefined' && new URLSearchParams(window.location.search).has('registered'));
        const hasSeenGuide = localStorage.getItem('onboarding_seen_' + props.participant.token);

        if (isJustRegistered || (!props.event.is_drawn && !hasSeenGuide)) {
            setTimeout(() => {
                showOnboardingSaveModal.value = true;
            }, 400);
        }
    } catch (e) {
        // Safe fallback for restricted storage environments
    }

    // If already revealed previously, fire a soft greeting confetti safely
    if (isRevealed.value) {
        setTimeout(() => {
            try {
                if (typeof window !== 'undefined' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    confetti({
                        particleCount: 35,
                        spread: 60,
                        origin: { y: 0.6 },
                        disableForReducedMotion: true,
                    });
                }
            } catch (e) {}
        }, 600);
    }
});
</script>

<template>
    <Head :title="'GiftMatch - ' + participant.name" />

    <div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden font-sans py-8 sm:py-12 px-4 sm:px-6">
        <ToastNotification />
        <CookieConsent />

        <!-- Festive Ambient Blobs -->
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-rose-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/2 -right-32 w-[550px] h-[550px] bg-amber-500/15 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-emerald-500/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto space-y-6 sm:space-y-8">
            <!-- Brand & Actions (Notificações + Salvar Acesso) -->
            <div class="flex items-center justify-between gap-2">
                <Link :href="route('home')" class="transition-transform hover:scale-105 shrink-0">
                    <ApplicationLogo />
                </Link>

                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Botão de Notificações / Central de Avisos -->
                    <button
                        id="btn-open-notifications"
                        @click="showNotificationsModal = true"
                        type="button"
                        class="relative inline-flex items-center justify-center gap-1.5 sm:gap-2 px-2.5 sm:px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 border border-slate-700 hover:border-slate-600 text-slate-100 hover:text-white transition-all shadow-md active:scale-95 cursor-pointer shrink-0 whitespace-nowrap"
                        title="Ver avisos e linha do tempo do evento"
                        aria-label="Ver avisos e linha do tempo do evento"
                    >
                        <BellRing v-if="!notificationsEnabled" class="w-4 h-4 text-amber-400 shrink-0" />
                        <Bell v-else class="w-4 h-4 text-amber-400 shrink-0" />
                        <span class="hidden sm:inline">Avisos</span>
                        <!-- Pulsing indicator if notifications pending -->
                        <span v-if="!notificationsEnabled" class="relative flex h-2 w-2 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </button>

                    <!-- Dropdown Salvar Acesso -->
                    <div class="relative shrink-0">
                        <button
                            @click="showSaveDropdown = !showSaveDropdown"
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-900 border border-slate-700 hover:border-slate-600 text-slate-100 hover:text-white transition-all shadow-md active:scale-95 cursor-pointer whitespace-nowrap shrink-0"
                            title="Salvar acesso a este evento"
                        >
                            <Bookmark class="w-4 h-4 text-rose-400 shrink-0" />
                            <span class="whitespace-nowrap">Salvar<span class="hidden sm:inline"> Acesso</span></span>
                            <ChevronDown class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': showSaveDropdown }" />
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
                            class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-2rem)] rounded-2xl bg-slate-900/95 border border-slate-700/80 p-2 shadow-2xl backdrop-blur-xl z-50 space-y-1 animate-in fade-in zoom-in-95"
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

                            <!-- 5. Avisos e Linha do Tempo -->
                            <button
                                @click="showSaveDropdown = false; showNotificationsModal = true;"
                                class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-slate-800/80 transition-colors group cursor-pointer"
                            >
                                <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <Bell class="w-4 h-4" />
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                        <span>Avisos e Linha do Tempo</span>
                                        <span v-if="!notificationsEnabled" class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium leading-tight">
                                        {{ notificationsEnabled ? 'Ver avisos e notificações' : 'Ativar avisos no navegador' }}
                                    </div>
                                </div>
                            </button>

                            <!-- 6. Sair deste dispositivo -->
                            <div class="pt-1 border-t border-slate-800/80 mt-1">
                                <button
                                    @click="showSaveDropdown = false; showDisconnectModal = true;"
                                    type="button"
                                    class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left hover:bg-rose-950/30 transition-colors group cursor-pointer text-slate-400 hover:text-rose-400"
                                >
                                    <div class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <LogOut class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-300 group-hover:text-rose-300">Sair deste dispositivo</div>
                                        <div class="text-[11px] text-slate-500 font-medium leading-tight">Desconectar sessão salva</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner: Ativar Notificações se pendente -->
            <div
                v-if="!notificationsEnabled && notificationSupported"
                class="flex items-center justify-between gap-3 p-3.5 sm:p-4 rounded-2xl bg-amber-500/10 border border-amber-500/25 text-amber-200 text-xs backdrop-blur-sm animate-in fade-in"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                        <BellRing class="w-4 h-4 animate-bounce" />
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-white block truncate">Ative as notificações deste amigo secreto</span>
                        <span class="text-[11px] text-amber-200/80 block truncate">Receba avisos instantâneos quando o sorteio for realizado</span>
                    </div>
                </div>
                <button
                    id="btn-banner-notifications"
                    @click="showNotificationsModal = true"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs bg-amber-500 hover:bg-amber-400 text-slate-950 transition-colors shrink-0 cursor-pointer shadow-md"
                >
                    Ver Avisos
                </button>
            </div>

            <!-- Greeting Card -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-5 sm:p-8 shadow-2xl backdrop-blur-xl space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30 shrink-0">
                        Espaço Exclusivo
                    </div>

                    <span class="text-xs text-slate-300 font-medium truncate max-w-full">
                        Evento: <strong class="text-white">{{ event.title }}</strong>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black font-display text-white">
                    Olá, <span class="inline-block pb-1 bg-gradient-to-r from-rose-400 to-amber-300 bg-clip-text text-transparent">{{ participant.name }}</span>!
                </h1>

                <!-- Event Details Pills -->
                <div class="pt-2 flex flex-wrap gap-2 text-xs">
                    <div v-if="event.theme" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold" :class="currentTheme.badgeClass">
                        <span>{{ currentTheme.emoji }}</span>
                        <span>Tema: {{ currentTheme.label }}</span>
                    </div>

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
                class="rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-12 text-center shadow-2xl backdrop-blur-xl space-y-5"
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

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button
                        type="button"
                        @click="showOnboardingSaveModal = true"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-lg shadow-rose-950/40 transition-all hover:scale-[1.02] cursor-pointer"
                    >
                        <Bookmark class="w-4 h-4 text-amber-300" />
                        <span>Como Salvar Meu Link e Programar Lembrete</span>
                    </button>
                </div>

                <div class="pt-2 flex items-center justify-center gap-4 text-xs text-slate-400 font-semibold">
                    <span>Participantes confirmados até agora: <strong class="text-rose-400">{{ event.participants_count }}</strong></span>
                </div>
            </div>

            <!-- DRAW STATUS 2: Event IS drawn -->
            <div v-else class="space-y-8">
                <!-- NOT REVEALED YET: Interactive 3D Gift Box Animation -->
                <div
                    v-if="!isRevealed"
                    class="rounded-3xl bg-gradient-to-br from-rose-950/40 via-slate-900 to-amber-950/40 border border-rose-500/30 p-6 sm:p-12 text-center shadow-2xl backdrop-blur-xl space-y-6"
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
                    <div class="py-4 flex flex-col items-center justify-center gap-5">
                        <button
                            type="button"
                            @click="revealFriend"
                            :disabled="isOpening"
                            aria-label="Abrir e revelar amigo secreto"
                            class="group relative focus:outline-none transition-transform duration-300 active:scale-95 cursor-pointer disabled:cursor-wait"
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

                        <!-- Explicit Action Button (100% reliable for all mobile/touch devices) -->
                        <div class="space-y-2 text-center w-full max-w-xs">
                            <button
                                type="button"
                                @click="revealFriend"
                                :disabled="isOpening"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white font-bold text-sm sm:text-base shadow-xl shadow-rose-950/40 border border-white/10 transition-all active:scale-95 cursor-pointer disabled:opacity-75 disabled:cursor-wait"
                            >
                                <Gift class="w-5 h-5 shrink-0" />
                                <span>{{ isOpening ? 'Abrindo Presente...' : 'Toque para Revelar Agora' }}</span>
                            </button>
                            <p class="text-xs text-slate-400 flex items-center justify-center gap-1.5 font-medium">
                                <PartyPopper class="w-4 h-4 text-amber-400 shrink-0" />
                                <span>Toque no presente ou no botão para descobrir</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ALREADY REVEALED: Friend details card with celebration -->
                <div
                    v-else
                    class="rounded-3xl bg-gradient-to-br from-rose-900/40 via-slate-900 to-emerald-950/40 border border-emerald-500/30 p-5 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shrink-0">
                            Revelado com Sucesso!
                        </div>

                        <button
                            @click="fireCelebrationConfetti"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-slate-100 transition-colors shrink-0 whitespace-nowrap cursor-pointer active:scale-95"
                        >
                            <PartyPopper class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                            <span>Soltar Confetes</span>
                        </button>
                    </div>

                    <!-- Big Secret Friend Reveal Name -->
                    <div class="text-center py-6 space-y-2">
                        <div class="text-sm font-bold uppercase tracking-wider text-rose-300">
                            Você tirou:
                        </div>
                        <div class="inline-block max-w-full text-4xl sm:text-5xl font-black font-display text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-rose-300 to-pink-400 tracking-tight leading-normal pt-1 pb-3 text-glow">
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

                        <!-- Standalone Action Button to Open Ideal Gift Suggestions Modal -->
                        <div class="pt-2">
                            <button
                                id="btn-open-ideal-gifts"
                                @click="showGiftSuggestionsModal = true"
                                type="button"
                                class="w-full flex items-center justify-center gap-2.5 px-5 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 via-rose-500 to-pink-500 hover:from-amber-400 hover:via-rose-400 hover:to-pink-400 text-slate-950 font-black shadow-lg shadow-rose-500/25 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer text-sm sm:text-base border border-amber-300/40"
                            >
                                <ShoppingBag class="w-5 h-5 shrink-0 text-slate-950" />
                                <span>Ver Produtos Ideais para {{ secret_friend?.name }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bilhete Secreto / Chat Anônimo (Apenas após o sorteio ser realizado e o amigo ser revelado) -->
            <SecretNotesChat
                v-if="event.is_drawn && isRevealed"
                :participant-token="participant.token"
                :friend-name="secret_friend?.name || 'seu Amigo Secreto'"
                :sent-questions="sent_questions"
                :received-questions="received_questions"
            />

            <!-- Affiliate Recommendations (Shopee & Mercado Livre) -->
            <AffiliateRecommendations :friend-name="secret_friend?.name" />

            <!-- Participant's OWN Wishlist Box (Can be edited anytime) -->
            <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-5 sm:p-8 shadow-2xl backdrop-blur-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-3 border-b border-slate-800/80">
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold font-display text-white flex items-center gap-2">
                            <Heart class="w-4 h-4 text-rose-500 shrink-0" />
                            <span>Sua Própria Lista de Presentes</span>
                        </h3>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                            Isso é o que quem tirou você está vendo. Você pode atualizar a qualquer momento!
                        </p>
                    </div>

                    <button
                        @click="showEditWishlistModal = true"
                        type="button"
                        class="self-start sm:self-auto shrink-0 inline-flex items-center justify-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl text-xs font-bold text-slate-100 bg-slate-800 hover:bg-slate-700 transition-colors border border-slate-700 shadow-sm cursor-pointer whitespace-nowrap active:scale-95"
                    >
                        <Edit class="w-3.5 h-3.5 text-rose-400" />
                        <span>Editar Minha Lista</span>
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

            <!-- Google AdSense Unit -->
            <GoogleAd slot-id="secret-bottom" />

            <!-- Legal Footer -->
            <footer class="pt-4 pb-2 text-center text-xs text-slate-500 space-x-3">
                <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
                <span>&bull;</span>
                <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
                <span>&bull;</span>
                <span>GiftMatch</span>
            </footer>
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

        <!-- MODAL: Salvar Link e Ativar Notificações / Programar Lembrete (Onboarding) -->
        <div v-if="showOnboardingSaveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-in fade-in duration-200">
            <div class="w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-700/80 p-6 sm:p-7 shadow-2xl space-y-5 animate-in zoom-in-95 duration-200 max-h-[92vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-500 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-rose-900/40 shrink-0">
                            <Gift class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold font-display text-white">
                                Guarde o Seu Link Exclusivo! 
                            </h3>
                            <p class="text-xs text-slate-300">
                                Olá, <strong class="text-white">{{ participant.name }}</strong>! É por este link que você saberá quem tirou.
                            </p>
                        </div>
                    </div>
                    <button @click="closeOnboardingSaveModal" class="text-slate-400 hover:text-white p-1 rounded-lg shrink-0 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Explanation Banner -->
                <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-200 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-amber-300">
                        <Bookmark class="w-4 h-4" />
                        <span>Não perca o acesso ao seu amigo secreto</span>
                    </div>
                    <p class="leading-relaxed text-[11px] text-amber-200/90">
                        Você não precisa criar senha nem login! Seu acesso é este link direto. Salve agora mesmo usando as opções abaixo para consultar facilmente no dia do sorteio.
                    </p>
                </div>

                <!-- Options Grid -->
                <div class="space-y-3 text-xs">
                    <!-- Opção 1: WhatsApp (Conversa Comigo) -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-white flex items-center gap-2">
                                <MessageCircle class="w-4 h-4 text-emerald-400 shrink-0" />
                                <span>Salvar no WhatsApp</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">Envie para si mesmo ou guarde na sua conversa</p>
                        </div>
                        <button
                            type="button"
                            @click="handleShareWhatsApp"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-300 bg-emerald-950/70 hover:bg-emerald-900 border border-emerald-800/60 transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer"
                        >
                            <MessageCircle class="w-3.5 h-3.5" />
                            <span>Enviar</span>
                        </button>
                    </div>

                    <!-- Opção: Baixar Cartão com QR Code -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-white flex items-center gap-2">
                                <Download class="w-4 h-4 text-amber-400 shrink-0" />
                                <span>Baixar Cartão com QR Code</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">Salva imagem com QR Code de acesso rápido na sua galeria</p>
                        </div>
                        <button
                            type="button"
                            @click="handleDownloadImage"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-amber-300 bg-amber-950/70 hover:bg-amber-900 border border-amber-800/60 transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer"
                        >
                            <Download class="w-3.5 h-3.5" />
                            <span>Baixar</span>
                        </button>
                    </div>

                    <!-- Opção 2: Copiar Link -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-white flex items-center gap-2">
                                <Copy class="w-4 h-4 text-indigo-400 shrink-0" />
                                <span>Copiar Link Secreto</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">Copie para colar nas suas notas ou favoritos</p>
                        </div>
                        <button
                            type="button"
                            @click="handleCopyLink"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-slate-700 hover:bg-slate-600 border border-slate-600 transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer"
                        >
                            <Check v-if="copiedPageLink" class="w-3.5 h-3.5 text-emerald-400" />
                            <Copy v-else class="w-3.5 h-3.5" />
                            <span>{{ copiedPageLink ? 'Copiado!' : 'Copiar' }}</span>
                        </button>
                    </div>

                    <!-- Opção 3: Adicionar Atalho à Tela Inicial -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-white flex items-center gap-2">
                                <Smartphone class="w-4 h-4 text-rose-400 shrink-0" />
                                <span>Adicionar à Tela de Início</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">Fica como um ícone de app no celular</p>
                        </div>
                        <button
                            type="button"
                            @click="handleAddToHomeScreen"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-300 bg-rose-950/70 hover:bg-rose-900 border border-rose-800/60 transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer"
                        >
                            <Smartphone class="w-3.5 h-3.5" />
                            <span>Ver Como</span>
                        </button>
                    </div>

                    <!-- Opção 4: Programar Lembrete no Calendário (Sem precisar de cron) -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 space-y-2.5">
                        <div class="space-y-0.5">
                            <div class="font-bold text-white flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-amber-400 shrink-0" />
                                <span>Programar Lembrete no Calendário</span>
                            </div>
                            <p class="text-[11px] text-slate-400">
                                O seu celular irá te avisar com alarme e o link direto do sorteio:
                            </p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            <button
                                type="button"
                                @click="addToGoogleCalendar"
                                class="w-full py-2 px-3 rounded-xl text-xs font-bold text-slate-100 bg-slate-700 hover:bg-slate-600 border border-slate-600 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <span>📅 Google Agenda</span>
                            </button>
                            <button
                                type="button"
                                @click="downloadCalendarIcs"
                                class="w-full py-2 px-3 rounded-xl text-xs font-bold text-slate-100 bg-slate-700 hover:bg-slate-600 border border-slate-600 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <span>📲 Apple / Celular (.ics)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Opção 5: Ativar Notificações do Navegador -->
                    <div class="p-3.5 rounded-2xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between gap-3">
                        <div class="space-y-0.5 min-w-0">
                            <div class="font-bold text-white flex items-center gap-2">
                                <BellRing class="w-4 h-4 text-yellow-400 shrink-0" />
                                <span>Notificações no Navegador</span>
                            </div>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ notificationsEnabled ? 'Notificações já estão ativadas!' : 'Receba avisos diretamente neste navegador' }}
                            </p>
                        </div>
                        <button
                            v-if="!notificationsEnabled"
                            type="button"
                            @click="requestBrowserNotifications"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-yellow-300 bg-yellow-950/70 hover:bg-yellow-900 border border-yellow-800/60 transition-colors shrink-0 flex items-center gap-1.5 cursor-pointer"
                        >
                            <Bell class="w-3.5 h-3.5" />
                            <span>Ativar</span>
                        </button>
                        <span v-else class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-400 bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-800/60 shrink-0">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                            <span>Ativado</span>
                        </span>
                    </div>
                </div>

                <!-- Footer button -->
                <div class="pt-2">
                    <button
                        type="button"
                        @click="closeOnboardingSaveModal"
                        class="w-full py-3 rounded-2xl font-bold text-xs sm:text-sm text-white bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 shadow-xl shadow-rose-950/40 transition-all active:scale-95 cursor-pointer"
                    >
                        Entendido, Já Salvei Meu Link! 👍
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: Central de Notificações e Linha do Tempo -->
        <div v-if="showNotificationsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-in fade-in duration-200">
            <div class="w-full max-w-lg rounded-3xl bg-slate-900 border border-slate-700/80 p-6 sm:p-7 shadow-2xl space-y-5 animate-in zoom-in-95 duration-200 max-h-[92vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 text-white flex items-center justify-center shadow-lg shadow-amber-900/30 shrink-0">
                            <Bell class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold font-display text-white">
                                Avisos e Notificações
                            </h3>
                            <p class="text-xs text-slate-300">
                                Evento: <strong class="text-white">{{ event.title }}</strong>
                            </p>
                        </div>
                    </div>
                    <button @click="showNotificationsModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg shrink-0 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Banner de Ativação / Status do Navegador -->
                <div 
                    class="p-4 rounded-2xl border transition-all"
                    :class="notificationsEnabled 
                        ? 'bg-emerald-950/40 border-emerald-500/30 text-emerald-200' 
                        : 'bg-amber-500/10 border-amber-500/30 text-amber-200'"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <div class="font-bold flex items-center gap-2 text-white text-sm">
                                <CheckCircle2 v-if="notificationsEnabled" class="w-4 h-4 text-emerald-400 shrink-0" />
                                <BellRing v-else class="w-4 h-4 text-amber-400 shrink-0 animate-bounce" />
                                <span>{{ notificationsEnabled ? 'Notificações Ativadas neste Navegador' : 'Ativar Notificações no Navegador' }}</span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                {{ notificationsEnabled 
                                    ? 'Você receberá avisos automáticos de sorteio e novidades deste evento diretamente no seu dispositivo.' 
                                    : 'Não perca o momento do sorteio! Ative para ser notificado assim que o organizador realizar o sorteio.' }}
                            </p>
                        </div>

                        <!-- Botão de Ação -->
                        <div class="shrink-0 pt-0.5">
                            <button
                                v-if="!notificationsEnabled"
                                type="button"
                                @click="requestBrowserNotifications"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-md transition-all active:scale-95 cursor-pointer flex items-center gap-1.5"
                            >
                                <Bell class="w-3.5 h-3.5" />
                                <span>Ativar Agora</span>
                            </button>
                            <button
                                v-else
                                type="button"
                                @click="sendTestNotification"
                                class="px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-300 bg-emerald-900/60 hover:bg-emerald-800/80 border border-emerald-700/60 transition-colors cursor-pointer flex items-center gap-1"
                            >
                                <span>Testar</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Linha do Tempo de Eventos -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-400 px-1">
                        <span>Linha do Tempo</span>
                        <span class="text-[11px] text-slate-500 font-normal">Histórico de Acontecimentos</span>
                    </div>

                    <div class="relative pl-6 space-y-3 before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-800">
                        <div 
                            v-for="item in eventNotifications" 
                            :key="item.id" 
                            class="relative group"
                        >
                            <!-- Timeline Dot / Icon -->
                            <div 
                                class="absolute -left-6 top-1.5 w-5 h-5 rounded-full border-2 border-slate-900 flex items-center justify-center shrink-0"
                                :class="item.colorClass"
                            >
                                <CheckCircle2 v-if="item.icon === 'check'" class="w-3 h-3" />
                                <BellRing v-else-if="item.icon === 'bell-ring'" class="w-3 h-3" />
                                <Bell v-else-if="item.icon === 'bell'" class="w-3 h-3" />
                                <Gift v-else-if="item.icon === 'gift'" class="w-3 h-3" />
                                <Clock v-else-if="item.icon === 'clock'" class="w-3 h-3" />
                                <Eye v-else-if="item.icon === 'eye'" class="w-3 h-3" />
                                <PartyPopper v-else class="w-3 h-3" />
                            </div>

                            <!-- Timeline Content Card -->
                            <div class="p-3.5 rounded-2xl bg-slate-800/70 border border-slate-700/70 space-y-1.5 hover:border-slate-600 transition-colors">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <h4 class="text-xs font-bold text-white flex items-center gap-1.5">
                                        {{ item.title }}
                                    </h4>
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                        :class="item.badgeClass"
                                    >
                                        {{ item.badge }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-300 leading-relaxed">
                                    {{ item.description }}
                                </p>
                                <div class="flex items-center justify-between pt-1 text-[11px] text-slate-400">
                                    <span>{{ item.date }}</span>
                                    <button
                                        v-if="item.action === 'activate_notifications' && !notificationsEnabled"
                                        type="button"
                                        @click="requestBrowserNotifications"
                                        class="text-amber-400 hover:text-amber-300 font-bold underline cursor-pointer"
                                    >
                                        Ativar Notificações &rarr;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lembrete no Calendário (Ações Rápidas) -->
                <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-200">
                        <Calendar class="w-4 h-4 text-amber-400" />
                        <span>Adicionar lembrete no calendário</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="addToGoogleCalendar"
                            class="py-2 px-3 rounded-xl text-xs font-semibold text-slate-200 bg-slate-700 hover:bg-slate-600 border border-slate-600 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span>📅 Google Agenda</span>
                        </button>
                        <button
                            type="button"
                            @click="downloadCalendarIcs"
                            class="py-2 px-3 rounded-xl text-xs font-semibold text-slate-200 bg-slate-700 hover:bg-slate-600 border border-slate-600 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span>📲 Apple / Celular (.ics)</span>
                        </button>
                    </div>
                </div>

                <!-- Close button -->
                <div>
                    <button
                        type="button"
                        @click="showNotificationsModal = false"
                        class="w-full py-2.5 rounded-2xl font-bold text-xs sm:text-sm text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors cursor-pointer"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação para Desconectar / Sair do Dispositivo -->
        <div
            v-if="showDisconnectModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in"
        >
            <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-700/80 p-6 shadow-2xl text-left space-y-5">
                <!-- Close button -->
                <button
                    @click="showDisconnectModal = false"
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
                            Evento: <strong class="text-slate-200">{{ event.title }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Body / Warning -->
                <div class="space-y-3 text-xs sm:text-sm text-slate-300 leading-relaxed bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                    <p>
                        Você não possui senha de acesso cadastrada. Para acessar novamente o seu amigo secreto e visualizar os presentes neste ou em outro aparelho, <strong>você precisará deste link exclusivo</strong>.
                    </p>
                    <p class="text-rose-300/90 font-medium">
                        Se você sair sem copiar o link, não conseguirá abrir seu amigo secreto até pedir o link novamente ao organizador.
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
                            :value="currentUrl"
                            class="flex-1 px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-slate-200 font-mono focus:outline-none select-all"
                        />
                        <button
                            @click="handleCopyDisconnectLink"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white transition-all shrink-0 cursor-pointer"
                            :class="copiedDisconnectLink ? 'bg-emerald-600' : 'bg-rose-600 hover:bg-rose-500'"
                        >
                            <Check v-if="copiedDisconnectLink" class="w-3.5 h-3.5" />
                            <Copy v-else class="w-3.5 h-3.5" />
                            <span>{{ copiedDisconnectLink ? 'Copiado!' : 'Copiar Link' }}</span>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        💡 Cole no seu WhatsApp ou guarde nos favoritos antes de confirmar a saída.
                    </p>
                </div>

                <!-- Modal Footer Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2">
                    <button
                        @click="showDisconnectModal = false"
                        type="button"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors"
                    >
                        Cancelar e Continuar Conectado
                    </button>
                    <button
                        @click="handleConfirmDisconnect"
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

        <!-- Modal de Soft Prompt (Pré-aviso amigável antes da caixa nativa do navegador) -->
        <div
            v-if="showSoftPromptModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in"
        >
            <div class="relative w-full max-w-md rounded-3xl bg-slate-900 border border-slate-700/80 p-6 shadow-2xl text-left space-y-5 animate-in zoom-in-95">
                <!-- Close button -->
                <button
                    @click="showSoftPromptModal = false"
                    type="button"
                    class="absolute top-4 right-4 p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    <X class="w-5 h-5" />
                </button>

                <!-- Icon & Header -->
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                        <BellRing class="w-6 h-6 animate-pulse" />
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-white">
                            Ativar avisos do Amigo Secreto
                        </h3>
                        <p class="text-xs text-slate-400">
                            Evento: <strong class="text-slate-200">{{ event.title }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Explanatory content -->
                <div class="space-y-2.5 text-xs sm:text-sm text-slate-300 leading-relaxed bg-slate-950/60 p-4 rounded-2xl border border-slate-800/80">
                    <p>
                        Quer saber quem você tirou assim que o organizador realizar o sorteio?
                    </p>
                    <p class="text-slate-400 text-xs">
                        Ao clicar em <strong>"Continuar e Ativar"</strong>, o seu navegador (Edge/Chrome/Safari) exibirá uma confirmação solicitando permissão. Basta tocar em <strong>"Permitir"</strong> para receber o aviso neste dispositivo.
                    </p>
                </div>

                <!-- Safety badge -->
                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                    <ShieldCheck class="w-4 h-4 text-emerald-400 shrink-0" />
                    <span>Sem spam. Apenas avisos importantes deste amigo secreto.</span>
                </div>

                <!-- Action buttons -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-2">
                    <button
                        @click="showSoftPromptModal = false"
                        type="button"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors cursor-pointer"
                    >
                        Agora Não
                    </button>
                    <button
                        @click="confirmBrowserNotificationPermission"
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-500 hover:bg-amber-400 text-slate-950 transition-all shadow-lg shadow-amber-950/50 cursor-pointer active:scale-95"
                    >
                        <Bell class="w-4 h-4" />
                        <span>Continuar e Ativar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Sugestões de Presentes Ideais para o Amigo Sorteado -->
        <GiftSuggestionsModal
            :is-open="showGiftSuggestionsModal"
            :friend-name="secret_friend?.name"
            :wishlist="secret_friend?.wishlist"
            :theme="event.theme"
            :budget-limit="event.budget_limit"
            :clothing-size="secret_friend?.clothing_size"
            :shoe-size="secret_friend?.shoe_size"
            :likes="secret_friend?.likes"
            :dislikes="secret_friend?.dislikes"
            @close="showGiftSuggestionsModal = false"
        />

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
                class="fixed bottom-6 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-emerald-950/95 border border-emerald-500/40 text-emerald-100 shadow-2xl text-xs font-bold backdrop-blur-xl"
            >
                <Check class="w-4 h-4 text-emerald-400 shrink-0" />
                <span>{{ clientToast }}</span>
            </div>
        </transition>
    </div>
</template>
