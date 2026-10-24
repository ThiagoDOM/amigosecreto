<template>
    <div class="inline-flex items-center gap-2">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 via-festive-600 to-amber-500 p-0.5 shadow-lg shadow-rose-500/25 flex items-center justify-center">
            <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-rose-500/20 to-amber-500/10"></div>
                <!-- Gift Box Icon -->
                <svg class="w-5 h-5 text-amber-400 relative z-10 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="8" width="18" height="4" rx="1"/>
                    <path d="M12 8v13"/>
                    <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/>
                    <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/>
                </svg>
            </div>
        </div>
        <span class="font-display font-extrabold text-xl tracking-tight bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 bg-clip-text text-transparent">
            Amigo<span class="text-slate-800 dark:text-white">Secreto</span>
        </span>
    </div>
</template>
