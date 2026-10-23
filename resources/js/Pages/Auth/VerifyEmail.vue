<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, LogOut } from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar E-mail" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black font-display text-slate-900 dark:text-white">
                Verifique seu E-mail
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                Obrigado por se cadastrar! Enviamos um link de confirmação para o seu e-mail. Por favor, clique no link para ativar todos os recursos da sua conta.
            </p>
        </div>

        <div
            class="mb-4 text-xs font-semibold text-emerald-600 dark:text-emerald-400 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800"
            v-if="verificationLinkSent"
        >
            ✓ Um novo link de verificação foi enviado para o endereço de e-mail informado no cadastro.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/25 transition-all disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <Mail class="w-4 h-4" />
                    <span>Reenviar E-mail de Verificação</span>
                </button>
            </div>

            <div class="pt-2 text-center">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline"
                >
                    <LogOut class="w-3.5 h-3.5" />
                    <span>Sair da conta</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
