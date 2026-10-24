<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LogIn } from 'lucide-vue-next';

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
            <h2 class="text-xl sm:text-2xl font-black font-display text-white">
                Entrar na Conta
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Acesse o painel para gerenciar seus grupos e sorteios.
            </p>
        </div>

        <div v-if="status" class="mb-4 text-xs font-semibold text-emerald-400 p-3 rounded-xl bg-emerald-950/40 border border-emerald-800">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Seu E-mail" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
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
                    <InputLabel for="password" value="Sua Senha" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-rose-400 hover:text-rose-300 hover:underline"
                    >
                        Esqueceu a senha?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="block pt-1">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-700 bg-slate-950 text-rose-600 focus:ring-rose-500" />
                    <span class="ms-2 text-xs font-medium text-slate-300">
                        Lembrar deste dispositivo
                    </span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-900/30 transition-all active:scale-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <LogIn class="w-4 h-4" />
                    <span>{{ form.processing ? 'Entrando...' : 'Entrar na Conta' }}</span>
                </button>
            </div>

            <div class="pt-4 text-center border-t border-slate-800 text-xs text-slate-400">
                Ainda não tem conta?
                <Link :href="route('register')" class="font-bold text-rose-400 hover:text-rose-300 hover:underline ms-1">
                    Cadastre-se gratuitamente
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
