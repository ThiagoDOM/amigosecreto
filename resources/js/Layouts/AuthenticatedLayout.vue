<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ToastNotification from '@/Components/ToastNotification.vue';
import { Link } from '@inertiajs/vue3';
import { PlusCircle, Calendar, User, LogOut } from 'lucide-vue-next';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
        <!-- Toast Flash Notification -->
        <ToastNotification />

        <!-- Navigation Bar -->
        <nav class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <Link :href="route('dashboard')" class="flex items-center transition-transform hover:scale-[1.02]">
                            <ApplicationLogo />
                        </Link>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden sm:flex items-center gap-2">
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard') || route().current('events.show')"
                                class="inline-flex items-center gap-2 text-xs uppercase tracking-wider font-bold transition-colors"
                            >
                                <Calendar class="w-4 h-4 text-slate-400" />
                                <span>Meus Eventos</span>
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right Header Actions -->
                    <div class="hidden sm:flex sm:items-center sm:gap-3">
                        <!-- <Link
                            :href="route('events.create')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm shadow-rose-900/30 transition-all active:scale-95"
                        >
                            <PlusCircle class="w-4 h-4" />
                            <span>Novo Evento</span>
                        </Link> -->

                        <!-- User Profile Dropdown -->
                        <div class="relative ms-2">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2.5 rounded-xl border border-slate-800 bg-slate-900 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:border-slate-700 hover:bg-slate-800/70 focus:outline-none transition-all"
                                    >
                                        <div class="w-6 h-6 rounded-lg bg-rose-600/20 border border-rose-500/30 text-rose-300 font-bold flex items-center justify-center text-xs">
                                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span>{{ $page.props.auth.user.name }}</span>
                                        <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 text-xs text-slate-400 border-b border-slate-800">
                                        Conectado como <strong class="text-slate-200 block truncate">{{ $page.props.auth.user.email }}</strong>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2 text-xs">
                                        <User class="w-3.5 h-3.5" />
                                        Meu Perfil
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-xs text-rose-400 hover:text-rose-300">
                                        <LogOut class="w-3.5 h-3.5" />
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
                            class="inline-flex items-center justify-center rounded-xl p-2 text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 focus:outline-none transition-colors"
                        >
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-b border-slate-800 bg-slate-950 px-4 pt-3 pb-4 space-y-3">
                <!-- <Link
                    :href="route('events.create')"
                    class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 shadow-sm transition-all"
                >
                    <PlusCircle class="w-4 h-4" />
                    Novo Evento
                </Link> -->

                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Meus Eventos
                </ResponsiveNavLink>

                <div class="pt-3 border-t border-slate-800 space-y-1">
                    <div class="px-3 py-1">
                        <div class="text-xs font-semibold text-white">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-[11px] text-slate-400 truncate">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                    <ResponsiveNavLink :href="route('profile.edit')">
                        Meu Perfil
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-rose-400">
                        Sair da Conta
                    </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Sub-Header (Slot) -->
        <header v-if="$slots.header" class="border-b border-slate-800/80 bg-slate-900/40">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <slot />
            </div>
        </main>

        <!-- Clean Footer -->
        <footer class="mt-auto py-5 border-t border-slate-800/80 text-xs text-slate-500 bg-slate-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                <div class="font-medium text-slate-400">
                    GiftMatch &bull; Painel do Organizador
                </div>
                <div class="flex items-center gap-3 text-slate-500">
                    <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
                    <span>&bull;</span>
                    <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
