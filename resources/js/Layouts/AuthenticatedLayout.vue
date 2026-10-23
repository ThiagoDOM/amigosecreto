<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { Link } from '@inertiajs/vue3';
import { PlusCircle, Calendar, User, LogOut, Sparkles } from 'lucide-vue-next';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col font-sans transition-colors duration-300">
        <!-- Toast Flash Notification -->
        <ToastNotification />

        <!-- Navigation Bar -->
        <nav class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <Link :href="route('dashboard')" class="flex items-center transition-transform hover:scale-105">
                            <ApplicationLogo />
                        </Link>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden sm:flex items-center gap-4">
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard') || route().current('events.show')"
                                class="inline-flex items-center gap-2 text-sm font-semibold transition-colors"
                            >
                                <Calendar class="w-4 h-4" />
                                Meus Eventos
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Header Actions -->
                    <div class="hidden sm:flex sm:items-center sm:gap-3">
                        <Link
                            :href="route('events.create')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 hover:from-rose-500 hover:to-amber-400 shadow-md shadow-rose-500/20 hover:shadow-lg hover:shadow-rose-500/30 transition-all duration-200"
                        >
                            <PlusCircle class="w-4 h-4" />
                            Novo Evento
                        </Link>

                        <!-- User Profile Dropdown -->
                        <div class="relative ms-2">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700/60 focus:outline-none transition-colors"
                                    >
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-rose-500 to-amber-500 text-white font-bold flex items-center justify-center text-xs">
                                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 text-xs text-slate-400 border-b border-slate-100 dark:border-slate-700">
                                        Conectado como <strong class="text-slate-700 dark:text-slate-200">{{ $page.props.auth.user.email }}</strong>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2">
                                        <User class="w-4 h-4" />
                                        Meu Perfil
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                                        <LogOut class="w-4 h-4" />
                                        Sair da Conta
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile Hamburger -->
                    <div class="flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-2 pb-4 space-y-2">
                <Link
                    :href="route('events.create')"
                    class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-rose-600 to-amber-500 shadow-md shadow-rose-500/20"
                >
                    <PlusCircle class="w-4 h-4" />
                    Novo Evento
                </Link>

                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Meus Eventos
                </ResponsiveNavLink>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-800">
                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-xs text-slate-500 mb-2">
                        {{ $page.props.auth.user.email }}
                    </div>
                    <ResponsiveNavLink :href="route('profile.edit')">
                        Meu Perfil
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-rose-600 dark:text-rose-400">
                        Sair da Conta
                    </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Sub-Header (Slot) -->
        <header v-if="$slots.header" class="bg-white/60 dark:bg-slate-900/60 border-b border-slate-200/60 dark:border-slate-800/80 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Clean Footer -->
        <footer class="mt-auto py-6 border-t border-slate-200/80 dark:border-slate-800 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-1.5 font-medium">
                    <Sparkles class="w-3.5 h-3.5 text-amber-500" />
                    <span>Amigo Secreto VIP &bull; Organize sem complicações</span>
                </div>
                <div>Gratuito, rápido e com envio direto no WhatsApp</div>
            </div>
        </footer>
    </div>
</template>
