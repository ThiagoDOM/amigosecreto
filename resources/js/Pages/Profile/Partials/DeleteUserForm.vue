<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-4">
        <header>
            <h2 class="text-lg font-bold font-display text-rose-600 dark:text-rose-400">
                Excluir Minha Conta
            </h2>

            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                Ao excluir sua conta, todos os seus dados, eventos e participações serão excluídos permanentemente.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 hover:bg-rose-100 transition-colors"
        >
            Excluir Minha Conta
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                    Tem certeza de que deseja excluir sua conta?
                </h2>

                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Esta ação é irreversível. Todos os seus eventos criados serão removidos. Por favor, confirme digitando sua senha abaixo.
                </p>

                <div class="mt-4">
                    <InputLabel
                        for="password"
                        value="Sua Senha"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
                        placeholder="Digite sua senha para confirmar"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-1.5" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal" class="rounded-xl text-xs font-semibold">
                        Cancelar
                    </SecondaryButton>

                    <button
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-600/30 transition-all disabled:opacity-50"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Sim, Excluir Minha Conta
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
