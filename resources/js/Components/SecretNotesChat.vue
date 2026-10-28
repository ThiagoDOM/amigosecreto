<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { 
    MessageCircle, 
    Send, 
    Lock, 
    CheckCircle2, 
    Clock, 
    HelpCircle,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    participantToken: {
        type: String,
        required: true,
    },
    friendName: {
        type: String,
        required: true,
    },
    sentQuestions: {
        type: Array,
        default: () => [],
    },
    receivedQuestions: {
        type: Array,
        default: () => [],
    },
});

const unansweredCount = computed(() => {
    return props.receivedQuestions.filter(q => !q.answer).length;
});

// Default to received tab if there are pending questions to answer, else 'ask'
const activeTab = ref(unansweredCount.value > 0 ? 'received' : 'ask');

const askForm = useForm({
    question: '',
});

const answeringMessageId = ref(null);
const answerForm = useForm({
    answer: '',
});

const isAsking = ref(false);
const isAnswering = ref(false);

function submitQuestion() {
    if (!askForm.question.trim() || isAsking.value) return;

    isAsking.value = true;
    askForm.post(route('secret.ask', props.participantToken), {
        preserveScroll: true,
        onSuccess: () => {
            askForm.reset('question');
            isAsking.value = false;
        },
        onError: () => {
            isAsking.value = false;
        },
    });
}

function startAnswering(messageId) {
    answeringMessageId.value = messageId;
    answerForm.reset('answer');
}

function cancelAnswering() {
    answeringMessageId.value = null;
    answerForm.reset('answer');
}

function submitAnswer(messageId) {
    if (!answerForm.answer.trim() || isAnswering.value) return;

    isAnswering.value = true;
    answerForm.post(route('secret.answer', [props.participantToken, messageId]), {
        preserveScroll: true,
        onSuccess: () => {
            answeringMessageId.value = null;
            answerForm.reset('answer');
            isAnswering.value = false;
        },
        onError: () => {
            isAnswering.value = false;
        },
    });
}
</script>

<template>
    <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-4 sm:p-6 shadow-2xl backdrop-blur-xl space-y-4">
        <!-- Header: Minimal & Clean -->
        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-500/15 text-rose-400 flex items-center justify-center shrink-0">
                    <MessageCircle class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold font-display text-white flex items-center gap-2">
                        <span>Bilhetes Secretos</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                            <Lock class="w-2.5 h-2.5" />
                            Anônimo
                        </span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Segmented Tabs Navigation -->
        <div class="flex items-center p-1 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs font-bold">
            <button
                @click="activeTab = 'ask'"
                type="button"
                class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                :class="activeTab === 'ask'
                    ? 'bg-rose-600 text-white shadow-md shadow-rose-950/30'
                    : 'text-slate-400 hover:text-white'"
            >
                <Send class="w-3.5 h-3.5" />
                <span>Perguntar para {{ friendName }}</span>
                <span
                    v-if="sentQuestions.length > 0"
                    class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white"
                >
                    {{ sentQuestions.length }}
                </span>
            </button>

            <button
                @click="activeTab = 'received'"
                type="button"
                class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                :class="activeTab === 'received'
                    ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-950/30'
                    : 'text-slate-400 hover:text-white'"
            >
                <HelpCircle class="w-3.5 h-3.5" />
                <span>Recebidas</span>
                <span
                    v-if="unansweredCount > 0"
                    class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-extrabold bg-rose-600 text-white animate-pulse"
                >
                    {{ unansweredCount }} nova{{ unansweredCount > 1 ? 's' : '' }}
                </span>
                <span
                    v-else-if="receivedQuestions.length > 0"
                    class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-slate-800 text-slate-300"
                >
                    {{ receivedQuestions.length }}
                </span>
            </button>
        </div>

        <!-- Tab 1: Perguntar para o Amigo Secreto -->
        <div v-if="activeTab === 'ask'" class="space-y-4">
            <!-- Form Card -->
            <form @submit.prevent="submitQuestion" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-300">
                        Envie uma pergunta para <strong class="text-white">{{ friendName }}</strong>:
                    </label>

                    <textarea
                        v-model="askForm.question"
                        rows="3"
                        maxlength="400"
                        placeholder="Ex: Qual sua cor favorita? Prefere doce ou salgado? Tem alguma restrição?"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors resize-none leading-relaxed"
                        required
                    ></textarea>
                </div>

                <div class="flex items-center justify-between gap-2 pt-0.5">
                    <span class="text-[11px] text-slate-500">
                        {{ 400 - (askForm.question?.length || 0) }} restantes
                    </span>

                    <button
                        type="submit"
                        :disabled="!askForm.question.trim() || isAsking || askForm.processing"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 disabled:opacity-40 transition-all cursor-pointer shadow-sm active:scale-95 shrink-0"
                    >
                        <Send class="w-3.5 h-3.5" />
                        <span>{{ isAsking ? 'Enviando...' : 'Enviar Bilhete' }}</span>
                    </button>
                </div>

                <div v-if="askForm.errors.question" class="text-xs text-rose-400 font-semibold">
                    {{ askForm.errors.question }}
                </div>
            </form>

            <!-- Sent Questions History (Only shown if questions exist) -->
            <div v-if="sentQuestions.length > 0" class="space-y-2.5">
                <div class="text-xs font-bold text-slate-300 flex items-center justify-between px-1">
                    <span>Perguntas Enviadas</span>
                    <span class="text-[11px] text-slate-500">({{ sentQuestions.length }})</span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                    <div
                        v-for="q in sentQuestions"
                        :key="q.id"
                        class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-2"
                    >
                        <!-- The Question Sent -->
                        <div class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                            <span class="font-bold text-rose-400">Você perguntou:</span>
                            <span class="ml-1.5 text-white">{{ q.question }}</span>
                        </div>

                        <!-- Answered View -->
                        <div v-if="q.answer" class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-800/50 space-y-1">
                            <div class="flex items-center justify-between gap-2 text-[10px] text-emerald-400 font-bold">
                                <span class="flex items-center gap-1">
                                    <CheckCircle2 class="w-3 h-3" />
                                    Resposta de {{ friendName }}:
                                </span>
                                <span class="text-slate-500 font-normal">{{ q.answered_at }}</span>
                            </div>
                            <div class="text-xs sm:text-sm text-emerald-100 font-medium leading-relaxed">
                                {{ q.answer }}
                            </div>
                        </div>

                        <!-- Waiting View -->
                        <div v-else class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                            <span class="flex items-center gap-1 text-amber-400/90 font-medium">
                                <Clock class="w-3 h-3" />
                                Aguardando {{ friendName }} responder...
                            </span>
                            <span>{{ q.created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Perguntas Recebidas de Quem Tirou Você -->
        <div v-else-if="activeTab === 'received'" class="space-y-3">
            <!-- Empty State: Simple & Discreet -->
            <div
                v-if="receivedQuestions.length === 0"
                class="p-6 rounded-2xl bg-slate-950/50 border border-slate-800/70 text-center space-y-1.5"
            >
                <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center mx-auto mb-2">
                    <Clock class="w-4 h-4" />
                </div>
                <h4 class="text-xs sm:text-sm font-bold text-slate-300">
                    Nenhuma pergunta recebida ainda
                </h4>
                <p class="text-[11px] sm:text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    Quando seu amigo secreto tiver alguma dúvida sobre o seu presente, o bilhete anônimo aparecerá aqui para você responder!
                </p>
            </div>

            <!-- List of Received Questions -->
            <div v-else class="space-y-3 max-h-96 overflow-y-auto pr-1 custom-scrollbar">
                <div
                    v-for="item in receivedQuestions"
                    :key="item.id"
                    class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3"
                >
                    <!-- Question Box -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2 text-[10px] text-slate-400">
                            <span class="inline-flex items-center gap-1 font-bold text-amber-400">
                                <Lock class="w-3 h-3" />
                                Seu Amigo Secreto perguntou:
                            </span>
                            <span>{{ item.created_at }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-white font-medium leading-relaxed bg-slate-900/90 p-3 rounded-xl border border-slate-800">
                            "{{ item.question }}"
                        </p>
                    </div>

                    <!-- Already Answered View -->
                    <div v-if="item.answer" class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-800/50 space-y-1">
                        <div class="flex items-center justify-between text-[10px] text-emerald-400 font-bold">
                            <span>Sua resposta:</span>
                            <span class="text-slate-500 font-normal">{{ item.answered_at }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-emerald-100 font-medium leading-relaxed">
                            {{ item.answer }}
                        </p>
                    </div>

                    <!-- Inline Reply Form (when open) -->
                    <div v-else-if="answeringMessageId === item.id" class="space-y-2 pt-2 border-t border-slate-800">
                        <textarea
                            v-model="answerForm.answer"
                            rows="2"
                            maxlength="500"
                            placeholder="Digite sua resposta..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-900 text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors resize-none leading-relaxed"
                            required
                        ></textarea>

                        <div class="flex items-center justify-end gap-2">
                            <button
                                @click="cancelAnswering"
                                type="button"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-white transition-colors cursor-pointer"
                            >
                                Cancelar
                            </button>

                            <button
                                @click="submitAnswer(item.id)"
                                :disabled="!answerForm.answer.trim() || isAnswering || answerForm.processing"
                                type="button"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 disabled:opacity-40 transition-all cursor-pointer shadow-sm active:scale-95"
                            >
                                <Send class="w-3 h-3" />
                                <span>{{ isAnswering ? 'Enviando...' : 'Enviar Resposta' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Button to open answer form -->
                    <div v-else class="flex justify-end pt-1">
                        <button
                            @click="startAnswering(item.id)"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-amber-300 hover:text-white bg-amber-950/50 hover:bg-amber-900/60 border border-amber-800/60 transition-all cursor-pointer active:scale-95"
                        >
                            <MessageCircle class="w-3 h-3" />
                            <span>Responder ao Amigo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
