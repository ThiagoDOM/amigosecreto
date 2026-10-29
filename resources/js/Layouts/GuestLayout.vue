<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    maxWidth: {
        type: String,
        default: 'sm:max-w-md',
    },
    hideLogo: {
        type: Boolean,
        default: false,
    },
    customCard: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center bg-slate-950 py-10 px-4 sm:px-6 overflow-hidden font-sans text-slate-100">
        <ToastNotification />

        <!-- Subtle background glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-rose-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div v-if="!hideLogo" class="relative z-10 flex flex-col items-center mb-6">
            <Link :href="route('home')" class="transition-transform hover:scale-[1.02]">
                <ApplicationLogo />
            </Link>
        </div>

        <div v-if="!customCard" :class="['relative z-10 w-full bg-slate-900/90 backdrop-blur-xl px-6 sm:px-8 py-7 sm:py-8 shadow-2xl rounded-2xl border border-slate-800 text-slate-100', maxWidth]">
            <slot />
        </div>
        <div v-else :class="['relative z-10 w-full', maxWidth]">
            <slot />
        </div>

        <div class="relative z-10 mt-6 flex items-center gap-3 text-xs text-slate-500">
            <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
            <span>&bull;</span>
            <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
        </div>
    </div>
</template>
