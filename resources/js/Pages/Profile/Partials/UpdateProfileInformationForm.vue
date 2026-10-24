<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-base font-bold font-display text-white">
                Informações do Perfil
            </h2>

            <p class="mt-1 text-xs text-slate-400">
                Atualize seus dados pessoais e endereço de e-mail de organizador.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-4"
        >
            <div>
                <InputLabel for="name" value="Nome Completo" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Endereço de E-mail" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm focus:outline-none focus:border-rose-500"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-xs text-slate-300">
                    Seu endereço de e-mail ainda não foi verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-xs text-rose-400 underline font-semibold hover:text-rose-300"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-semibold text-emerald-400"
                >
                    Um novo link de verificação foi enviado para o seu e-mail.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition-all disabled:opacity-50 active:scale-95"
                >
                    Salvar Alterações
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-semibold text-emerald-400"
                    >
                        ✓ Dados salvos com sucesso.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
