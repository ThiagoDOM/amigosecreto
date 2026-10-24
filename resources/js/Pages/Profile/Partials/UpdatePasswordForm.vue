<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                Alterar Senha
            </h2>

            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Certifique-se de que sua conta esteja utilizando uma senha forte para maior segurança.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-4">
            <div>
                <InputLabel for="current_password" value="Senha Atual" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-1.5"
                />
            </div>

            <div>
                <InputLabel for="password" value="Nova Senha" class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-1.5" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar Nova Senha"
                    class="text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-1.5"
                />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 shadow-md shadow-rose-500/20 hover:from-rose-500 hover:to-amber-400 transition-all disabled:opacity-50"
                >
                    Salvar Nova Senha
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                    >
                        ✓ Senha atualizada com sucesso.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
