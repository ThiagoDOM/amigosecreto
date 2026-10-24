<script setup>
import { Plus, Trash2, Link as LinkIcon, Gift, FileText } from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['update:modelValue']);

function addItem() {
    const updated = [...props.modelValue, { title: '', url: '', notes: '' }];
    emit('update:modelValue', updated);
}

function removeItem(index) {
    const updated = props.modelValue.filter((_, i) => i !== index);
    emit('update:modelValue', updated);
}

function updateField(index, field, value) {
    const updated = props.modelValue.map((item, i) => {
        if (i === index) {
            return { ...item, [field]: value };
        }
        return item;
    });
    emit('update:modelValue', updated);
}
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Lista de Presentes Desejados 🎁
            </label>
            <button
                type="button"
                @click="addItem"
                class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors"
            >
                <Plus class="w-3.5 h-3.5" />
                <span>Adicionar Presente</span>
            </button>
        </div>

        <div v-if="modelValue.length === 0" class="p-4 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 text-center space-y-2">
            <div class="text-xs text-slate-600 dark:text-slate-300">
                Você ainda não adicionou nenhum item à sua lista de desejos.
            </div>
            <button
                type="button"
                @click="addItem"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition-all"
            >
                <Plus class="w-3.5 h-3.5" />
                Adicionar Primeiro Presente
            </button>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="(item, index) in modelValue"
                :key="index"
                class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-2 relative transition-all"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                        <Gift class="w-3.5 h-3.5 text-rose-500" />
                        Item #{{ index + 1 }}
                    </span>
                    <button
                        type="button"
                        @click="removeItem(index)"
                        class="text-slate-400 hover:text-rose-600 p-1 rounded-lg transition-colors"
                        title="Remover este item"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                    </button>
                </div>

                <!-- Gift Title -->
                <div>
                    <input
                        :value="item.title"
                        @input="updateField(index, 'title', $event.target.value)"
                        type="text"
                        placeholder="Nome do presente (ex: Livro de Ficção, Camisa Azul, Caneca Térmica)"
                        class="w-full px-3 py-2 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        required
                    />
                </div>

                <!-- URL & Notes Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div class="relative">
                        <LinkIcon class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            :value="item.url"
                            @input="updateField(index, 'url', $event.target.value)"
                            type="url"
                            placeholder="Link onde encontrar (opcional: https://...)"
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        />
                    </div>

                    <div class="relative">
                        <FileText class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            :value="item.notes"
                            @input="updateField(index, 'notes', $event.target.value)"
                            type="text"
                            placeholder="Detalhes (opcional: cor, edição, modelo)"
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        />
                    </div>
                </div>
            </div>

            <button
                type="button"
                @click="addItem"
                class="w-full py-2.5 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-rose-600 hover:border-rose-500 flex items-center justify-center gap-1.5 transition-colors"
            >
                <Plus class="w-3.5 h-3.5" />
                Adicionar Outro Presente
            </button>
        </div>
    </div>
</template>
