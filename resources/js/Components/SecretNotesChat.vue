<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { 
    MessageCircle, 
    Send, 
    Lock, 
    CheckCircle2, 
    Clock, 
    HelpCircle, 
    UserCheck,
    AlertCircle,
    ChevronDown,
    ChevronUp
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
    <div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-5 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                    <Lock class="w-3.5 h-3.5 text-amber-400" />
                    <span>100% Anônimo & Seguro</span>
                </div>
                <h3 class="text-lg sm:text-xl font-black font-display text-white flex items-center gap-2">
                    <MessageCircle class="w-5 h-5 text-rose-500" />
                    <span>Bilhetes Secretos</span>
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed">
                    Tire dúvidas sobre o presente sem revelar sua identidade, ou responda às perguntas de quem tirou você!
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Box 1: Send Question to Secret Friend -->
            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                            <Send class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">
                                Perguntar para {{ friendName }}
                            </h4>
                            <p class="text-[11px] text-slate-400">
                                Ele(a) responderá sem jamais saber quem tirou ele(a).
                            </p>
                        </div>
                    </div>

                    <!-- Ask Form -->
                    <form @submit.prevent="submitQuestion" class="space-y-2.5">
                        <textarea
                            v-model="askForm.question"
                            rows="2"
                            maxlength="400"
                            placeholder="Ex: Qual seu doce favorito? Prefere cores neutras ou vibrantes? Tem alguma restrição?"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-900 text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-colors resize-none"
                            required
                        ></textarea>

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-500">
                                {{ 400 - (askForm.question?.length || 0) }} caracteres restantes
                            </span>

                            <button
                                type="submit"
                                :disabled="!askForm.question.trim() || isAsking || askForm.processing"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 disabled:opacity-40 transition-all cursor-pointer shadow-sm active:scale-95"
                            >
                                <Send class="w-3.5 h-3.5" />
                                <span>{{ isAsking ? 'Enviando...' : 'Enviar Pergunta Secreta' }}</span>
                            </button>
                        </div>

                        <div v-if="askForm.errors.question" class="text-xs text-rose-400 font-semibold">
                            {{ askForm.errors.question }}
                        </div>
                    </form>

                    <!-- List of Sent Questions -->
                    <div class="space-y-3 pt-3 border-t border-slate-800/80">
                        <div class="text-xs font-bold text-slate-300 flex items-center justify-between">
                            <span>Perguntas que você enviou</span>
                            <span class="text-[11px] font-semibold text-slate-500">({{ sentQuestions.length }})</span>
                        </div>

                        <div v-if="sentQuestions.length === 0" class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/60 text-center text-xs text-slate-500">
                            Nenhuma pergunta enviada ainda. Aproveite para tirar dúvidas!
                        </div>

                        <div v-else class="space-y-2.5 max-h-60 overflow-y-auto pr-1 custom-scrollbar">
                            <div
                                v-for="q in sentQuestions"
                                :key="q.id"
                                class="p-3 rounded-xl bg-slate-900 border border-slate-800/90 space-y-2"
                            >
                                <!-- The Question -->
                                <div class="text-xs text-slate-200">
                                    <span class="font-bold text-rose-400">Você:</span>
                                    <span class="ml-1 leading-relaxed">{{ q.question }}</span>
                                </div>

                                <!-- Status & Answer -->
                                <div v-if="q.answer" class="p-2.5 rounded-lg bg-emerald-950/40 border border-emerald-800/50 space-y-1">
                                    <div class="flex items-center justify-between gap-2 text-[10px] text-emerald-400 font-bold">
                                        <span class="flex items-center gap-1">
                                            <CheckCircle2 class="w-3 h-3" />
                                            Resposta de {{ friendName }}:
                                        </span>
                                        <span class="text-slate-500 font-normal">{{ q.answered_at }}</span>
                                    </div>
                                    <div class="text-xs text-emerald-100 font-medium leading-relaxed">
                                        {{ q.answer }}
                                    </div>
                                </div>

                                <div v-else class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
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
            </div>

            <!-- Box 2: Questions Received from Whoever Drew You -->
            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0">
                            <HelpCircle class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">
                                Perguntas de quem tirou você
                            </h4>
                            <p class="text-[11px] text-slate-400">
                                Seu amigo secreto quer acertar em cheio no seu presente!
                            </p>
                        </div>
                    </div>

                    <!-- List of Received Questions -->
                    <div class="space-y-3 pt-1">
                        <div v-if="receivedQuestions.length === 0" class="p-6 rounded-xl bg-slate-900/50 border border-slate-800/60 text-center space-y-2">
                            <Clock class="w-6 h-6 text-slate-600 mx-auto" />
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">
                                Nenhuma pergunta recebida no momento.<br />
                                Se quem tirou você tiver alguma dúvida, o bilhete secreto aparecerá aqui!
                            </p>
                        </div>

                        <div v-else class="space-y-3 max-h-[340px] overflow-y-auto pr-1 custom-scrollbar">
                            <div
                                v-for="item in receivedQuestions"
                                :key="item.id"
                                class="p-3.5 rounded-xl bg-slate-900 border border-slate-800/90 space-y-2.5"
                            >
                                <!-- Question from Secret Santa -->
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2 text-[10px] text-slate-400">
                                        <span class="inline-flex items-center gap-1 font-bold text-amber-400">
                                            <Lock class="w-3 h-3" />
                                            Seu Amigo Secreto perguntou:
                                        </span>
                                        <span>{{ item.created_at }}</span>
                                    </div>
                                    <p class="text-xs text-white font-medium leading-relaxed bg-slate-950/60 p-2.5 rounded-lg border border-slate-800">
                                        "{{ item.question }}"
                                    </p>
                                </div>

                                <!-- Already Answered View -->
                                <div v-if="item.answer" class="p-2.5 rounded-lg bg-emerald-950/40 border border-emerald-800/50 space-y-1">
                                    <div class="flex items-center justify-between text-[10px] text-emerald-400 font-bold">
                                        <span>Sua resposta enviada:</span>
                                        <span class="text-slate-500 font-normal">{{ item.answered_at }}</span>
                                    </div>
                                    <p class="text-xs text-emerald-100 font-medium leading-relaxed">
                                        {{ item.answer }}
                                    </p>
                                </div>

                                <!-- Answer Form when open -->
                                <div v-else-if="answeringMessageId === item.id" class="space-y-2 pt-1 border-t border-slate-800">
                                    <textarea
                                        v-model="answerForm.answer"
                                        rows="2"
                                        maxlength="500"
                                        placeholder="Digite sua resposta sincera para ajudar seu amigo..."
                                        class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors resize-none"
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
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 disabled:opacity-40 transition-all cursor-pointer shadow-sm active:scale-95"
                                        >
                                            <Send class="w-3 h-3" />
                                            <span>{{ isAnswering ? 'Enviando...' : 'Enviar Resposta' }}</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Button to Open Reply Form -->
                                <div v-else class="pt-1 flex justify-end">
                                    <button
                                        @click="startAnswering(item.id)"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-amber-300 hover:text-white bg-amber-950/50 hover:bg-amber-900/60 border border-amber-800/60 transition-all cursor-pointer"
                                    >
                                        <MessageCircle class="w-3 h-3" />
                                        <span>Responder agora</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
