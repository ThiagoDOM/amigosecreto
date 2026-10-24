<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { KeyRound } from 'lucide-vue-next';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir Senha" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black font-display text-slate-900 dark:text-white">
                Redefinir Senha
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                Digite sua nova senha abaixo para recuperar o acesso à sua conta.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Seu E-mail" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Nova Senha" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Nova Senha"
                    class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-1.5"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/25 transition-all hover:scale-[1.01] disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <KeyRound class="w-4 h-4" />
                    <span>{{ form.processing ? 'Atualizando...' : 'Redefinir e Salvar Senha' }}</span>
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
