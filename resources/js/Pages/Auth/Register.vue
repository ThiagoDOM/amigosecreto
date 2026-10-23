<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar Conta Gratuita" />

        <div class="mb-6 text-center">
            <h2 class="text-xl sm:text-2xl font-black font-display text-white">
                Criar Conta Grátis
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Cadastre-se como organizador em segundos e crie seu evento.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Seu Nome Completo" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.name"
                    placeholder="Ex: Ana Clara"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Seu E-mail" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.email"
                    placeholder="seu@email.com"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Criar Senha" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.password"
                    placeholder="Pelo menos 8 caracteres"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Senha"
                    class="text-slate-300 font-bold text-xs uppercase tracking-wider"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.password_confirmation"
                    placeholder="Repita a senha criada"
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
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-900/30 transition-all active:scale-95 disabled:opacity-50"
                    :disabled="form.processing"
                >
                    <UserPlus class="w-4 h-4" />
                    <span>{{ form.processing ? 'Cadastrando...' : 'Cadastrar Gratuitamente' }}</span>
                </button>
            </div>

            <div class="pt-4 text-center border-t border-slate-800 text-xs text-slate-400">
                Já tem uma conta cadastrada?
                <Link :href="route('login')" class="font-bold text-rose-400 hover:text-rose-300 hover:underline ms-1">
                    Entrar na minha conta
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
