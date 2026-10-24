<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LogIn, ArrowRight } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar na Conta" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black font-display text-slate-900 dark:text-white">
                Entrar na Conta
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                Acesse o painel para gerenciar seus grupos e sorteios.
            </p>
        </div>

        <div v-if="status" class="mb-4 text-xs font-semibold text-emerald-600 dark:text-emerald-400 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Seu E-mail" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

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

            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Sua Senha" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="block pt-1">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded text-rose-600 focus:ring-rose-500" />
                    <span class="ms-2 text-xs font-medium text-slate-700 dark:text-slate-300">
                        Lembrar deste dispositivo
                    </span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/25 transition-all hover:scale-[1.01] disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <LogIn class="w-4 h-4" />
                    <span>{{ form.processing ? 'Entrando...' : 'Entrar na Conta' }}</span>
                </button>
            </div>

            <div class="pt-4 text-center border-t border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                Ainda não tem conta?
                <Link :href="route('register')" class="font-bold text-rose-600 dark:text-rose-400 hover:underline ms-1">
                    Cadastre-se gratuitamente
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
