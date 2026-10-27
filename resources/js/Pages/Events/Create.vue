<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    Gift, 
    ArrowLeft, 
    Calendar, 
    MapPin, 
    CheckCircle2,
    Check
} from 'lucide-vue-next';
import { THEMES } from '@/Utils/themes';

const form = useForm({
    title: '',
    theme: 'traditional',
    description: '',
    budget_limit: '',
    draw_date: '',
    event_date: '',
    location: '',
});

function submit() {
    form.post(route('events.store'));
}
</script>

<template>
    <Head title="Criar Amigo Secreto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('dashboard')"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 transition-colors"
                >
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black font-display text-white">
                        Novo Amigo Secreto
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">
                        Preencha os detalhes do grupo para gerar o link de convite.
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Form Column -->
            <div class="lg:col-span-2">
                <form
                    @submit.prevent="submit"
                    class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-sm space-y-5"
                >
                    <!-- Title -->
                    <div class="space-y-1.5">
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Nome do Evento <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Ex: Família Silva 2026, Turma da Firma"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                            required
                        />
                        <div v-if="form.errors.title" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.title }}
                        </div>
                    </div>

                    <!-- Theme Selector -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Tema do Amigo Secreto
                        </label>
                        <p class="text-[11px] text-slate-400">
                            Escolha se a troca terá um tema divertido específico ou se será tradicional.
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                            <button
                                v-for="(themeItem, themeKey) in THEMES"
                                :key="themeKey"
                                type="button"
                                @click="form.theme = themeKey"
                                class="p-3 rounded-xl border text-left transition-all cursor-pointer flex flex-col justify-between space-y-1 relative"
                                :class="[
                                    form.theme === themeKey
                                        ? 'bg-rose-500/15 border-rose-500 ring-2 ring-rose-500/30'
                                        : 'bg-slate-950/80 border-slate-800 hover:border-slate-700 text-slate-300'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xl">{{ themeItem.emoji }}</span>
                                    <span v-if="form.theme === themeKey" class="w-4 h-4 rounded-full bg-rose-500 text-white flex items-center justify-center">
                                        <Check class="w-2.5 h-2.5 stroke-[3]" />
                                    </span>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white leading-tight">
                                        {{ themeItem.label }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5 font-normal">
                                        {{ themeItem.tagline }}
                                    </div>
                                </div>
                            </button>
                        </div>
                        <div v-if="form.errors.theme" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.theme }}
                        </div>
                    </div>

                    <!-- Budget Limit -->
                    <div class="space-y-1.5">
                        <label for="budget_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Valor Sugerido do Presente (R$)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs sm:text-sm">
                                R$
                            </div>
                            <input
                                id="budget_limit"
                                v-model="form.budget_limit"
                                type="number"
                                step="0.50"
                                min="0"
                                placeholder="50.00"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400">Opcional: serve como guia de preço para os amigos.</p>
                        <div v-if="form.errors.budget_limit" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.budget_limit }}
                        </div>
                    </div>

                    <!-- Dates Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="draw_date" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                Data Prevista do Sorteio
                            </label>
                            <input
                                id="draw_date"
                                v-model="form.draw_date"
                                type="date"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                            />
                            <div v-if="form.errors.draw_date" class="text-xs text-rose-400 font-semibold">
                                {{ form.errors.draw_date }}
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="event_date" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                Data da Confraternização
                            </label>
                            <input
                                id="event_date"
                                v-model="form.event_date"
                                type="date"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                            />
                            <div v-if="form.errors.event_date" class="text-xs text-rose-400 font-semibold">
                                {{ form.errors.event_date }}
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="space-y-1.5">
                        <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Local / Plataforma
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <MapPin class="w-4 h-4" />
                            </div>
                            <input
                                id="location"
                                v-model="form.location"
                                type="text"
                                placeholder="Ex: Casa do Bruno, Churrascaria ou Online"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                            />
                        </div>
                        <div v-if="form.errors.location" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.location }}
                        </div>
                    </div>

                    <!-- Description / Regras -->
                    <div class="space-y-1.5">
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                            Regras e Informações Adicionais
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Ex: Cada um leva 1 prato de comida e a sua bebida. Presente temático de chocolates!"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-xs text-rose-400 font-semibold">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                        <Link
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                        >
                            Cancelar
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-900/30 transition-all disabled:opacity-50 active:scale-95"
                        >
                            <Gift class="w-4 h-4" />
                            <span>{{ form.processing ? 'Criando Evento...' : 'Criar Evento' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tips Sidebar -->
            <div class="space-y-4">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center font-bold">
                        <Gift class="w-5 h-5" />
                    </div>

                    <h3 class="text-base font-bold font-display text-white">
                        Como funciona?
                    </h3>

                    <ul class="space-y-3 text-xs text-slate-300 leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Você recebe um <strong>Link de Convite</strong> para enviar aos amigos no WhatsApp.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Cada participante cadastra seus desejos sem precisar criar conta.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Você pode definir <strong>regras para casais</strong> antes do sorteio.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                            <span>O sorteio é realizado com 1 clique de forma justa e segura.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
