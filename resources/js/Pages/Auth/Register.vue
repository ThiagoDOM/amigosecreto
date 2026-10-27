<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    UserPlus,
    Gift,
    ShieldCheck,
    Sliders,
    ShoppingBag,
    MessageCircle,
    CheckCircle2,
} from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    marketing_opt_in: true,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :custom-card="true" max-width="max-w-md lg:max-w-5xl" :hide-logo="true">
        <Head title="Criar Conta Gratuita" />

        <!-- Logo on mobile only (on desktop it is integrated in the left column) -->
        <div class="lg:hidden flex justify-center mb-6">
            <Link :href="route('home')" class="transition-transform hover:scale-[1.02]">
                <ApplicationLogo />
            </Link>
        </div>

        <!-- 2-Column Split Card Container -->
        <div class="rounded-3xl bg-slate-900/90 backdrop-blur-xl border border-slate-800 shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 text-slate-100">
            
            <!-- LEFT COLUMN: Value Proposition & Feature Highlights (Desktop: 5 cols) -->
            <div class="hidden lg:flex lg:col-span-5 relative bg-gradient-to-br from-rose-950/40 via-slate-900/95 to-amber-950/30 p-8 xl:p-10 border-r border-slate-800/80 flex-col justify-between overflow-hidden">
                <!-- Background ambient blobs inside left panel -->
                <div class="absolute -top-24 -left-24 w-60 h-60 bg-rose-600/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-60 h-60 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <Link :href="route('home')" class="inline-block transition-transform hover:scale-[1.02]">
                        <ApplicationLogo />
                    </Link>

                    <div class="space-y-2 pt-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                            <Gift class="w-3.5 h-3.5 text-rose-400" />
                            <span>Espaço do Organizador</span>
                        </div>
                        <h2 class="text-2xl xl:text-3xl font-black font-display text-white leading-tight">
                            Organize o amigo secreto perfeito em minutos.
                        </h2>
                        <p class="text-xs xl:text-sm text-slate-300 leading-relaxed font-medium">
                            Crie seu grupo, defina regras entre casais ou familiares e compartilhe os convites no WhatsApp com total privacidade.
                        </p>
                    </div>

                    <!-- Benefit Bullet Points -->
                    <div class="space-y-3.5 pt-1">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <ShieldCheck class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white">Sorteio 100% Secreto</h3>
                                <p class="text-[11px] text-slate-400 leading-snug">
                                    Ninguém descobre antes da hora. Revelação mágica e individual para cada participante.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <Sliders class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white">Regras de Restrição</h3>
                                <p class="text-[11px] text-slate-400 leading-snug">
                                    Defina quem não pode tirar quem (ex: casais, irmãos ou mesma casa) sem travar o sorteio.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <ShoppingBag class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white">Lista de Desejos com Links</h3>
                                <p class="text-[11px] text-slate-400 leading-snug">
                                    Participantes sugerem o que querem ganhar com links diretos de lojas parceiras.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <MessageCircle class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white">Convites via WhatsApp</h3>
                                <p class="text-[11px] text-slate-400 leading-snug">
                                    Envie o link de inscrição diretamente pelo WhatsApp. Prático e sem precisar baixar aplicativo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Trust Badge -->
                <div class="relative z-10 pt-6 mt-6 border-t border-slate-800/80">
                    <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800 flex items-center gap-2.5">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                        <span class="text-[11px] text-slate-300 font-medium">100% Gratuito &bull; Sem limites de participantes</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Registration Form (Desktop: 7 cols) -->
            <div class="lg:col-span-7 p-6 sm:p-8 xl:p-10 flex flex-col justify-center">
                <div class="mb-6">
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
                            placeholder="Seu nome completo"
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <InputLabel for="password" value="Criar Senha" class="text-slate-300 font-bold text-xs uppercase tracking-wider" />

                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500"
                                v-model="form.password"
                                placeholder="Mínimo 8 caracteres"
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
                                placeholder="Repita a senha"
                                required
                                autocomplete="new-password"
                            />

                            <InputError
                                class="mt-1.5"
                                :message="form.errors.password_confirmation"
                            />
                        </div>
                    </div>

                    <!-- Terms of Use & Privacy Policy Acceptance -->
                    <div class="pt-1">
                        <label class="flex items-start gap-2.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="form.terms"
                                required
                                class="mt-0.5 w-4 h-4 rounded border-slate-700 bg-slate-950 text-rose-600 focus:ring-rose-500/30 focus:ring-offset-slate-900 transition-colors"
                            />
                            <span class="text-xs text-slate-300 leading-relaxed">
                                Concordo com os
                                <Link :href="route('terms')" target="_blank" class="font-bold text-rose-400 hover:text-rose-300 underline">Termos de Uso</Link>
                                e a
                                <Link :href="route('privacy')" target="_blank" class="font-bold text-rose-400 hover:text-rose-300 underline">Política de Privacidade</Link>.
                            </span>
                        </label>
                        <InputError class="mt-1.5" :message="form.errors.terms" />
                    </div>

                    <!-- Marketing Email Opt-in -->
                    <div class="pt-0.5">
                        <label class="flex items-start gap-2.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="form.marketing_opt_in"
                                class="mt-0.5 w-4 h-4 rounded border-slate-700 bg-slate-950 text-rose-600 focus:ring-rose-500/30 focus:ring-offset-slate-900 transition-colors"
                            />
                            <span class="text-xs text-slate-300 leading-relaxed">
                                Aceito receber novidades, dicas e e-mails promocionais do GiftMatch.
                            </span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-lg shadow-rose-950/40 transition-all hover:scale-[1.01] active:scale-95 disabled:opacity-50 cursor-pointer"
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
            </div>
        </div>
    </GuestLayout>
</template>
