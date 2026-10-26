<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import { ArrowLeft, FileText, CheckCircle2, AlertTriangle, Scale } from 'lucide-vue-next';

defineProps({
    updatedAt: {
        type: String,
        default: '24 de Outubro de 2026',
    },
});
</script>

<template>
    <Head title="Termos de Uso - GiftMatch" />
    <CookieConsent />

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-rose-500 selection:text-white flex flex-col justify-between">
        <!-- Ambient lighting -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[350px] bg-gradient-to-b from-rose-600/10 via-rose-500/5 to-transparent rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Top Navigation -->
        <header class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <Link :href="route('home')" class="transition-transform hover:scale-[1.02]">
                    <ApplicationLogo />
                </Link>

                <Link
                    :href="route('home')"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition-colors"
                >
                    <ArrowLeft class="w-4 h-4" />
                    <span>Voltar ao início</span>
                </Link>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-14 space-y-10">
            <!-- Header Section -->
            <div class="space-y-3 text-center sm:text-left border-b border-slate-800/80 pb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold">
                    <FileText class="w-4 h-4" />
                    <span>Termos & Condições de Uso da Plataforma</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight">
                    Termos de Uso
                </h1>
                <p class="text-xs sm:text-sm text-slate-400">
                    Última atualização: {{ updatedAt }} &bull; Domínio oficial: <strong>giftmatch.online</strong>
                </p>
            </div>

            <!-- Content Sections -->
            <div class="space-y-8 text-sm leading-relaxed text-slate-300">
                <!-- 1. Aceitação -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-emerald-400" />
                        1. Aceitação dos Termos
                    </h2>
                    <p>
                        Ao acessar ou utilizar a plataforma <strong>GiftMatch</strong> (disponível no endereço <strong>https://giftmatch.online</strong>), seja criando uma conta como organizador ou ingressando como participante em um evento, você expressa sua concordância integral e irrevogável com estes Termos de Uso e com a nossa Política de Privacidade.
                    </p>
                    <p>
                        Caso não concorde com qualquer uma das disposições, você deve abster-se de utilizar a plataforma.
                    </p>
                </section>

                <!-- 2. Descrição dos Serviços -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        2. O Serviço Oferecido
                    </h2>
                    <p>
                        O GiftMatch é um serviço digital gratuito destinado a facilitar a criação, gestão e realização de sorteios de confraternização (como o tradicional "Amigo Secreto" ou "Amigo Oculto"). Nossos recursos incluem:
                    </p>
                    <ul class="list-disc list-inside space-y-1 ps-2">
                        <li>Criação e personalização de eventos de Amigo Secreto;</li>
                        <li>Configuração de restrições de sorteio (ex: impedir que pares ou casais tirem um ao outro);</li>
                        <li>Geração de links de convite públicos e links secretos exclusivos para cada participante;</li>
                        <li>Lista de presentes sugeridos, tamanhos e preferências cadastradas pelos próprios participantes;</li>
                        <li>Exportação de cartões em imagem para compartilhamento visual.</li>
                    </ul>
                </section>

                <!-- 3. Contas e Cadastro -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        3. Cadastro e Segurança da Conta
                    </h2>
                    <p>
                        Para organizar eventos, é necessário cadastrar-se fornecendo nome, e-mail válido e senha. O usuário é o único responsável pela veracidade dos dados informados e pela guarda confidencial de suas credenciais de acesso, devendo notificar o GiftMatch em caso de uso não autorizado.
                    </p>
                </section>

                <!-- 4. Algoritmo de Sorteio e Sigilo -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        4. Sigilo e Aleatoriedade do Sorteio
                    </h2>
                    <p>
                        O algoritmo do GiftMatch é projetado para garantir sorteios justos, aleatórios e imparciais:
                    </p>
                    <ul class="list-disc list-inside space-y-1 ps-2">
                        <li>Nenhum participante tirará a si mesmo;</li>
                        <li>Todas as regras de exclusão registradas antes do sorteio serão rigorosamente respeitadas;</li>
                        <li>O organizador do evento não tem privilégio de visualizar os pares sorteados, mantendo a surpresa para todos os envolvidos.</li>
                    </ul>
                </section>

                <!-- 5. Responsabilidade sobre Presentes e Terceiros -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <AlertTriangle class="w-5 h-5 text-amber-400" />
                        5. Limitação de Responsabilidade
                    </h2>
                    <p>
                        O GiftMatch é uma ferramenta estritamente intermediária de organização e sorteio. O serviço <strong>não realiza vendas diretas, entrega de mercadorias, nem se responsabiliza por:</strong>
                    </p>
                    <ul class="list-disc list-inside space-y-1 ps-2">
                        <li>Prazos, entrega, qualidade, defeitos ou valores de presentes adquiridos em lojas físicas ou virtuais de terceiros;</li>
                        <li>Inadimplência ou não cumprimento da entrega de presentes entre os participantes de um grupo;</li>
                        <li>Links externos ou conteúdos de terceiros indicados nas listas de presentes.</li>
                    </ul>
                </section>

                <!-- 6. Publicidade e Financiamento -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        6. Financiamento e Exibição de Anúncios
                    </h2>
                    <p>
                        Para assegurar que o GiftMatch permaneça gratuito para todos os usuários sem cobrança de taxas de assinatura, a plataforma exibe anúncios publicitários gerenciados pelo <strong>Google AdSense</strong> e programas de afiliados. Ao utilizar a plataforma, você concorda com a veiculação desses anúncios de forma não invasiva.
                    </p>
                </section>

                <!-- 7. Propriedade Intelectual -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        7. Propriedade Intelectual
                    </h2>
                    <p>
                        A marca <strong>GiftMatch</strong>, seu logotipo, código-fonte, layout, identidade visual e banco de dados são de propriedade exclusiva de seus desenvolvedores e estão protegidos pelas leis de propriedade intelectual brasileiras e internacionais. É proibida a reprodução, engenharia reversa ou comercialização não autorizada do serviço.
                    </p>
                </section>

                <!-- 8. Foro e Legislação -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Scale class="w-5 h-5 text-rose-400" />
                        8. Legislação Aplicável e Foro
                    </h2>
                    <p>
                        Estes Termos de Uso são regidos e interpretados em conformidade com as Leis da República Federativa do Brasil, em especial o Marco Civil da Internet (Lei nº 12.965/2014) e o Código de Defesa do Consumidor (Lei nº 8.078/1990). Qualquer controvérsia decorrente destes termos será submetida ao foro da comarca de domicílio do usuário.
                    </p>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 py-6 text-xs text-slate-500 bg-slate-950">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <ApplicationLogo />
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <Link :href="route('terms')" class="hover:text-rose-400 transition-colors font-bold text-slate-200">Termos de Uso</Link>
                    <span>&bull;</span>
                    <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors">Política de Privacidade</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
