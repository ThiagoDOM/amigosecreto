<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, AlertCircle, X } from 'lucide-vue-next';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref('success');
let timeoutId = null;

const flash = computed(() => page.props.flash || {});

watch(
    () => [flash.value?.success, flash.value?.error],
    ([successMsg, errorMsg]) => {
        if (successMsg) {
            type.value = 'success';
            message.value = successMsg;
            show();
        } else if (errorMsg) {
            type.value = 'error';
            message.value = errorMsg;
            show();
        }
    },
    { immediate: true }
);

function show() {
    visible.value = true;
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        visible.value = false;
    }, 6000);
}

function close() {
    visible.value = false;
    if (timeoutId) clearTimeout(timeoutId);
}
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible && message"
            class="fixed bottom-5 right-5 z-50 max-w-md w-full shadow-2xl rounded-2xl p-4 border backdrop-blur-xl flex items-start gap-3"
            :class="type === 'success' 
                ? 'bg-emerald-950/90 text-emerald-100 border-emerald-500/40 shadow-emerald-950/50' 
                : 'bg-rose-950/90 text-rose-100 border-rose-500/40 shadow-rose-950/50'"
        >
            <div class="flex-shrink-0 mt-0.5">
                <CheckCircle2 v-if="type === 'success'" class="w-5 h-5 text-emerald-400" />
                <AlertCircle v-else class="w-5 h-5 text-rose-400" />
            </div>

            <div class="flex-1 text-sm font-medium leading-relaxed">
                {{ message }}
            </div>

            <button
                @click="close"
                class="flex-shrink-0 text-slate-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </transition>
</template>
