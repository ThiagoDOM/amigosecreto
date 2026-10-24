<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Send, ArrowLeft } from 'lucide-vue-next';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Senha" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black font-display text-slate-900 dark:text-white">
                Recuperar Senha
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                Informe o seu e-mail cadastrado e enviaremos um link para você redefinir sua senha com segurança.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-4 text-xs font-semibold text-emerald-600 dark:text-emerald-400 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Seu E-mail Cadastrado" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    v-model="form.email"
                    placeholder="seu@email.com"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/25 transition-all hover:scale-[1.01] disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <Send class="w-4 h-4" />
                    <span>{{ form.processing ? 'Enviando link...' : 'Enviar Link de Redefinição' }}</span>
                </button>
            </div>

            <div class="pt-4 text-center border-t border-slate-200 dark:border-slate-800">
                <Link :href="route('login')" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    Voltar para o login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
