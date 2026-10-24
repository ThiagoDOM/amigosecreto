<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    Gift, 
    ArrowLeft, 
    DollarSign, 
    Calendar, 
    MapPin, 
    Sparkles, 
    CheckCircle2 
} from 'lucide-vue-next';

const form = useForm({
    title: '',
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
                    class="p-2 rounded-xl text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                >
                    <ArrowLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-black font-display text-slate-900 dark:text-white">
                        Novo Amigo Secreto
                    </h1>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 font-medium">
                        Preencha os detalhes do evento para gerar o link de convite.
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Column -->
            <div class="lg:col-span-2">
                <form
                    @submit.prevent="submit"
                    class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6"
                >
                    <!-- Title -->
                    <div class="space-y-1.5">
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            Nome do Evento <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Ex: Família Silva 2026, Turma da Firma, Galera da Faculdade"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            required
                        />
                        <div v-if="form.errors.title" class="text-xs text-rose-500 font-semibold">
                            {{ form.errors.title }}
                        </div>
                    </div>

                    <!-- Budget Limit -->
                    <div class="space-y-1.5">
                        <label for="budget_limit" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            Valor Sugerido do Presente (R$)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 dark:text-slate-400 font-bold text-sm">
                                R$
                            </div>
                            <input
                                id="budget_limit"
                                v-model="form.budget_limit"
                                type="number"
                                step="0.50"
                                min="0"
                                placeholder="50.00"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Opcional: serve como guia de preço para os amigos.</p>
                        <div v-if="form.errors.budget_limit" class="text-xs text-rose-500 font-semibold">
                            {{ form.errors.budget_limit }}
                        </div>
                    </div>

                    <!-- Dates Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="draw_date" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                Data Prevista do Sorteio
                            </label>
                            <input
                                id="draw_date"
                                v-model="form.draw_date"
                                type="date"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                            <div v-if="form.errors.draw_date" class="text-xs text-rose-500 font-semibold">
                                {{ form.errors.draw_date }}
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="event_date" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                Data da Confraternização
                            </label>
                            <input
                                id="event_date"
                                v-model="form.event_date"
                                type="date"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                            <div v-if="form.errors.event_date" class="text-xs text-rose-500 font-semibold">
                                {{ form.errors.event_date }}
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="space-y-1.5">
                        <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
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
                                placeholder="Ex: Casa do Bruno, Churrascaria Estrela ou Online no Google Meet"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                            />
                        </div>
                        <div v-if="form.errors.location" class="text-xs text-rose-500 font-semibold">
                            {{ form.errors.location }}
                        </div>
                    </div>

                    <!-- Description / Regras -->
                    <div class="space-y-1.5">
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            Regras e Informações Adicionais
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Ex: Cada um leva 1 prato de comida e a sua bebida. Presente temático de chocolates!"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                        ></textarea>
                        <div v-if="form.errors.description" class="text-xs text-rose-500 font-semibold">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                        <Link
                            :href="route('dashboard')"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            Cancelar
                        </Link>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/25 transition-all disabled:opacity-50 hover:scale-[1.01]"
                        >
                            <Gift class="w-4 h-4" />
                            <span>{{ form.processing ? 'Criando Evento...' : 'Criar Evento e Obter Convite' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tips Sidebar -->
            <div class="space-y-6">
                <div class="rounded-3xl bg-gradient-to-br from-rose-500/10 via-amber-500/5 to-emerald-500/10 border border-rose-500/20 p-6 space-y-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center font-bold shadow-md shadow-rose-500/20">
                        <Sparkles class="w-5 h-5" />
                    </div>

                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">
                        O que acontece em seguida?
                    </h3>

                    <ul class="space-y-3 text-xs text-slate-700 dark:text-slate-200 font-medium">
                        <li class="flex items-start gap-2">
                            <CheckCircle2 class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" />
                            <span>Você recebe um <strong class="text-slate-900 dark:text-white">Link de Convite exclusivo</strong> para compartilhar no WhatsApp.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <CheckCircle2 class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" />
                            <span>Cada participante entra e preenche seus presentes com links sem precisar criar senha.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <CheckCircle2 class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" />
                            <span>Você poderá configurar <strong class="text-slate-900 dark:text-white">restrições de casais</strong> antes do sorteio.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <CheckCircle2 class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" />
                            <span>O sorteio é realizado com 1 clique e cada um descobre seu amigo com confetes!</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
