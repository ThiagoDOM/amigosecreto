<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ArrowLeft, ShieldCheck, Lock, Eye, Cookie, Mail } from 'lucide-vue-next';

defineProps({
    updatedAt: {
        type: String,
        default: '24 de Outubro de 2026',
    },
});
</script>

<template>
    <Head title="Política de Privacidade - GiftMatch" />

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
                    <ShieldCheck class="w-4 h-4" />
                    <span>Conformidade com LGPD & Google AdSense</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight">
                    Política de Privacidade
                </h1>
                <p class="text-xs sm:text-sm text-slate-400">
                    Última atualização: {{ updatedAt }} &bull; Domínio oficial: <strong>giftmatch.online</strong>
                </p>
            </div>

            <!-- Content Sections -->
            <div class="space-y-8 text-sm leading-relaxed text-slate-300">
                <!-- 1. Introdução -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Lock class="w-5 h-5 text-rose-400" />
                        1. Introdução e Compromisso
                    </h2>
                    <p>
                        O <strong>GiftMatch</strong> (disponível no endereço <strong>https://giftmatch.online</strong>) tem como prioridade a segurança, a confidencialidade e a privacidade de todos os seus usuários e participantes de sorteios de amigo secreto.
                    </p>
                    <p>
                        Esta Política de Privacidade explica de forma clara e transparente como tratamos, coletamos e protegemos os seus dados, em estrita conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (LGPD - Lei nº 13.709/2018)</strong> e as diretrizes internacionais de publicidade digital e proteção ao consumidor.
                    </p>
                </section>

                <!-- 2. Dados Coletados -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Eye class="w-5 h-5 text-amber-400" />
                        2. Dados que Coletamos
                    </h2>
                    <p>
                        Coletamos estritamente o necessário para operacionalizar os grupos e sorteios de amigo secreto:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 ps-2 text-slate-300">
                        <li>
                            <strong>Organizadores (Usuários Registrados):</strong> Nome completo, endereço de e-mail e senha criptografada (hash seguro).
                        </li>
                        <li>
                            <strong>Participantes de Eventos:</strong> Nome ou apelido, número de telefone WhatsApp (opcional, para envio do link individual) e lista de presentes/tamanhos (opcional, preenchida pelo próprio participante).
                        </li>
                        <li>
                            <strong>Dados de Navegação Técnicos:</strong> Endereço IP, data e hora de acesso, tipo de navegador e identificadores de cookies essenciais para prevenção de fraudes e estabilidade da aplicação.
                        </li>
                    </ul>
                </section>

                <!-- 3. Finalidade do Tratamento -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        3. Finalidade e Sigilo do Sorteio
                    </h2>
                    <p>
                        Todos os dados fornecidos são utilizados <strong>exclusivamente</strong> para:
                    </p>
                    <ul class="list-disc list-inside space-y-1 ps-2">
                        <li>Realizar o sorteio criptográfico aleatório sem que ninguém tire a si mesmo e respeitando regras de exclusão;</li>
                        <li>Permitir que cada participante acerte e consulte seu amigo secreto e lista de desejos por meio de seu link exclusivo;</li>
                        <li>Garantir o sigilo absoluto: o organizador do evento não tem acesso a quem tirou quem, salvo se os participantes revelarem voluntariamente.</li>
                    </ul>
                    <p class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs text-rose-300 font-medium">
                        🛡️ <strong>Garantia de Não Venda:</strong> O GiftMatch <strong>NÃO</strong> comercializa, aluga nem repassa listas de e-mails ou números de telefone de participantes a corretores de dados (data brokers) ou empresas de telemarketing.
                    </p>
                </section>

                <!-- 4. Google AdSense e Cookies de Publicidade -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white flex items-center gap-2">
                        <Cookie class="w-5 h-5 text-indigo-400" />
                        4. Google AdSense, Cookies e Publicidade de Terceiros
                    </h2>
                    <p>
                        Para manter o serviço <strong>100% gratuito</strong> para todos os usuários, o GiftMatch exibe anúncios fornecidos pelo <strong>Google AdSense</strong> e fornecedores parceiros certificados.
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 ps-2">
                        <li>
                            O Google, como fornecedor terceiro, utiliza <strong>cookies</strong> para veicular anúncios com base nas visitas anteriores dos usuários a este ou a outros sites na Internet.
                        </li>
                        <li>
                            Com o uso de cookies de publicidade (incluindo o cookie DART), o Google e seus parceiros podem veicular anúncios personalizados com base nos seus interesses e comportamento de navegação na web.
                        </li>
                        <li>
                            <strong>Como Desativar a Publicidade Personalizada:</strong> Você pode desativar a personalização de anúncios a qualquer momento visitando as <a href="https://adssettings.google.com" target="_blank" rel="noopener noreferrer" class="text-rose-400 hover:underline font-bold">Configurações de Anúncios do Google</a>. Alternativamente, você pode optar por desativar o uso de cookies de terceiros acessando o portal da <a href="http://www.aboutads.info/choices/" target="_blank" rel="noopener noreferrer" class="text-rose-400 hover:underline font-bold">Digital Advertising Alliance (AboutAds)</a> ou da <a href="http://www.networkadvertising.org/choices/" target="_blank" rel="noopener noreferrer" class="text-rose-400 hover:underline font-bold">Network Advertising Initiative</a>.
                        </li>
                    </ul>
                </section>

                <!-- 5. Links de Afiliados -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        5. Links de Afiliados e Parcerias Comerciais
                    </h2>
                    <p>
                        O GiftMatch pode sugerir ideias de presentes contendo links de programas de afiliados (como Amazon, Mercado Livre ou Shopee). Caso você opte por clicar em um link e efetuar uma compra no site do parceiro, poderemos receber uma pequena comissão sem qualquer custo adicional para você. O GiftMatch não se responsabiliza pela entrega, estoque ou política de preços dos parceiros externos.
                    </p>
                </section>

                <!-- 6. Direitos do Titular (LGPD) -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        6. Seus Direitos (LGPD - Artigo 18)
                    </h2>
                    <p>
                        Como titular dos seus dados pessoais, a legislação brasileira assegura a você os seguintes direitos:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <strong class="text-white block mb-0.5">Acesso e Confirmação:</strong>
                            Confirmar a existência de tratamento e consultar os dados cadastrados.
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <strong class="text-white block mb-0.5">Correção de Dados:</strong>
                            Solicitar a atualização de dados incompletos ou inexatos.
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <strong class="text-white block mb-0.5">Eliminação e Revogação:</strong>
                            Excluir sua conta e histórico de eventos a qualquer momento no painel.
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-xs">
                            <strong class="text-white block mb-0.5">Portabilidade:</strong>
                            Solicitar cópia dos dados do seu evento em formato acessível.
                        </div>
                    </div>
                </section>

                <!-- 7. Segurança e Retenção -->
                <section class="space-y-3">
                    <h2 class="text-xl font-bold font-display text-white">
                        7. Armazenamento e Segurança da Informação
                    </h2>
                    <p>
                        Adotamos medidas técnicas e administrativas aptas a proteger seus dados contra acessos não autorizados, incidentes e vazamentos, incluindo criptografia SSL/TLS em todas as comunicações, senhas com hashing de padrão da indústria (Bcrypt) e isolamento de permissões de banco de dados.
                    </p>
                </section>

                <!-- 8. Contato e Encarregado -->
                <section class="space-y-3 p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-rose-950/30 border border-slate-800">
                    <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                        <Mail class="w-5 h-5 text-rose-400" />
                        8. Contato e Encarregado de Proteção de Dados (DPO)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Para dúvidas, solicitações de exclusão de dados ou esclarecimentos sobre esta política, entre em contato diretamente pelo nosso canal oficial de atendimento:
                    </p>
                    <div class="pt-2">
                        <a
                            href="mailto:contato@giftmatch.online"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600/20 border border-rose-500/40 text-rose-300 text-xs font-bold hover:bg-rose-600/30 transition-colors"
                        >
                            <Mail class="w-4 h-4" />
                            <span>contato@giftmatch.online</span>
                        </a>
                    </div>
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
                    <Link :href="route('terms')" class="hover:text-rose-400 transition-colors">Termos de Uso</Link>
                    <span>&bull;</span>
                    <Link :href="route('privacy')" class="hover:text-rose-400 transition-colors font-bold text-slate-200">Política de Privacidade</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
