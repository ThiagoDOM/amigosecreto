<p align="center">
  <a href="https://giftmatch.online" target="_blank">
    <img src="public/favicon.svg" width="90" alt="GiftMatch Logo" />
  </a>
</p>

<h1 align="center">GiftMatch &bull; Amigo Secreto Online Inteligente</h1>

<p align="center">
  <strong>Plataforma Web Full-Stack moderna para criação e gestão de sorteios de Amigo Secreto / Amigo Oculto com regras de restrição para casais, listas de desejos e monetização por afiliados.</strong>
</p>

<p align="center">
  <a href="https://giftmatch.online" target="_blank">
    <img src="https://img.shields.io/badge/Demo_Online-giftmatch.online-e11d48?style=for-the-badge&logo=google-chrome&logoColor=white" alt="Demo Online" />
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.4" />
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=flat-square&logo=inertia&logoColor=white" alt="Inertia.js" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/PostgreSQL-Neon_Serverless-4169E1?style=flat-square&logo=postgresql&logoColor=white" alt="PostgreSQL Neon" />
  <img src="https://img.shields.io/badge/Tests-35_Passing-10b981?style=flat-square&logo=githubactions&logoColor=white" alt="Tests" />
  <img src="https://img.shields.io/badge/License-MIT-blue?style=flat-square" alt="License MIT" />
</p>

---

## 📸 Demonstração Visual Completa do Sistema

### 1. Painel Principal do Organizador (Dashboard)
Visão consolidada de todos os eventos ativos, status do sorteio, contagem de participantes e atalhos rápidos de gestão.
![GiftMatch Dashboard](docs/screenshots/01-dashboard.png)

---

### 2. Criação de Novo Amigo Secreto
Cadastro rápido com valor sugerido, data da confraternização, data do sorteio, local e orientações aos participantes.
![GiftMatch Criar Evento](docs/screenshots/02-event-create.png)

---

### 3. Painel de Gestão do Evento e Participantes
Visualização da lista completa de participantes, link exclusivo de convite, status do sorteio, botão de envio direto no WhatsApp e links secretos de revelação.
![GiftMatch Gestão de Evento](docs/screenshots/03-event-management.png)

---

### 4. Configuração de Regras de Restrição (Casais & Família)
Interface para configurar exclusões mútuas com apenas um clique (ex: impedir que parceiros, irmãos ou pessoas que já se tiraram no ano anterior tirem um ao outro).
![GiftMatch Restrições de Casais](docs/screenshots/04-couple-restrictions.png)

---

### 5. Página Pública de Inscrição & Lista de Desejos (Convidados)
Sem exigir criação de senha, os convidados acessam pelo WhatsApp, confirmam presença, informam tamanhos de roupa, calçado, preferências e cadastram sua lista de presentes com links.
![GiftMatch Convite e Inscrição](docs/screenshots/05-guest-invite.png)

---

### 6. Tela Mágica de Revelação Secreta com Ideias de Presentes
Ao abrir o link secreto, o participante descobre seu amigo secreto, confere os tamanhos, o que a pessoa gosta/não gosta e tem acesso à lista de presentes com botões inteligentes da **Shopee** e **Mercado Livre**.
![GiftMatch Revelação Secreta com Afiliados](docs/screenshots/06-secret-reveal.png)

---

### 7. Tela de Suspense Pré-Revelação
Animação e contagem regressiva para criar o clima festivo antes de revelar quem foi sorteado.
![GiftMatch Suspense Pré-Revelação](docs/screenshots/07-secret-suspense.png)

---

### 8. Landing Page / Apresentação Institucional
Design responsivo em Dark Mode nativo com iluminação ambiente e alta performance para dispositivos móveis e desktop.
![GiftMatch Home Hero](docs/screenshots/home-hero.png)

---

## ✨ Principais Diferenciais e Funcionalidades Técnicas

### 🎲 1. Algoritmo Inteligente de Sorteio com Restrições
* **Algoritmo de Derangement:** Garante matematicamente que ninguém tire a si mesmo ($f(x) \neq x$).
* **Matriz de Exclusões Bidirecionais:** Permite definir regras restritivas complexas (ex: casais, familiares diretos ou desafetos) sem gerar ciclos inválidos.
* **Prevenção de Impasses e Deadlocks:** Validação prévia da viabilidade do grafo de sorteio com algoritmo de backtracking e limite estocástico, informando ao organizador caso as restrições tornem o sorteio impossível.

### 📱 2. Experiência Zero-Friction via WhatsApp
* **Acesso Sem Senha para Convidados:** Participantes entram pelo link de convite e interagem sem atritos de cadastro demorado.
* **Ficha de Presentes Completa:** Tamanho de vestuário, calçado, gostos, aversões/alergias e lista com suporte a links externos.
* **Tokens Criptográficos Individuais:** Cada participante possui uma chave de acesso segura de 32 caracteres (alfanumérico aleatório) para conferir seu amigo secreto a qualquer momento.

### 🛍️ 3. Motor de Monetização Integrada (Shopee & Mercado Livre)
* **Busca Contextual Inteligente:** Botões automáticos em cada presente da lista apontando para a busca correspondente na Shopee e no Mercado Livre já com parâmetros de afiliado injetados (`matt_tool`, `matt_word`, `utm_source`, `mmp_pid`).
* **Conversor de Links no Backend:** O serviço [AffiliateLinkService.php](app/Services/AffiliateLinkService.php) intercepta links colados pelos convidados e adiciona os identificadores de afiliado de forma transparente.
* **Transparência & Compliance:** Mensagens explícitas de transparência nos termos da legislação brasileira (CDC, LGPD e diretrizes do CONAR).
* **Google AdSense:** Carregamento assíncrono não invasivo com componentes Vue preparados.

### ⚡ 4. Alta Performance & Otimização Core Web Vitals
* **Google Fonts Assíncrono:** Preload com `media="print" onload="this.media='all'"` e `display: swap`, eliminando bloqueios de renderização e derrubando FCP/LCP para menos de 1 segundo.
* **Vite / Rollup Manual Chunks:** Unificação de micro-arquivos da biblioteca de ícones em pacote único cacheável (`vendor-icons.js`), eliminando dezenas de requisições encadeadas da cascata de rede.
* **SEO e Compartilhamento Social:** Metatags Open Graph dinâmicas com geração de previews temáticos no WhatsApp, Facebook e Twitter.

---

## 🛠️ Stack Tecnológica

| Camada | Tecnologia | Descrição |
| :--- | :--- | :--- |
| **Backend** | [PHP 8.4](https://www.php.net/) + [Laravel 12](https://laravel.com/) | Arquitetura MVC, Form Requests, Services e Eloquent ORM |
| **Frontend** | [Vue 3](https://vuejs.org/) (`<script setup>`) | Composition API reativa e componentes modulares |
| **Ponte SPA** | [Inertia.js v2](https://inertiajs.com/) | Roteamento SPA fluido sem sobrecarga de APIs GraphQL/REST manuais |
| **Estilização** | [Tailwind CSS v3](https://tailwindcss.com/) | Design System festivo e moderno em Dark Theme nativo |
| **Banco de Dados**| [PostgreSQL (Neon)](https://neon.tech/) / [SQLite](https://sqlite.org/) | Migrações relacionais e suporte a múltiplos drivers |
| **E-mails** | [Resend](https://resend.com/) / SMTP | Notificações transacionais seguras |
| **Efeitos Visuais**| [Canvas-Confetti](https://www.npmjs.com/package/canvas-confetti) | Animações de celebração na revelação |
| **Testes** | [PHPUnit](https://phpunit.de/) | 35 testes automatizados cobrindo autenticação, restrições e sorteio |

---

## 🚀 Como Executar Localmente

### Pré-requisitos
* PHP >= 8.2 (Recomendado PHP 8.4)
* Composer
* Node.js >= 18 e NPM
* SQLite ou PostgreSQL

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/giftmatch/amigosecreto.git
   cd amigosecreto
   ```

2. **Instale as dependências do PHP:**
   ```bash
   composer install
   ```

3. **Instale as dependências do Frontend:**
   ```bash
   npm install
   ```

4. **Configure as variáveis de ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Execute as migrações e popule o banco de dados com dados de teste:**
   ```bash
   php artisan migrate --seed
   ```
   > O seeder cria automaticamente o usuário `admin@giftmatch.online` (senha: `password`) com 2 eventos configurados, participantes, restrições de casais e listas de presentes para demonstração imediata!

6. **Inicie o servidor de desenvolvimento:**
   * Em um terminal:
     ```bash
     npm run dev
     ```
   * Em outro terminal:
     ```bash
     php artisan serve
     ```

7. **Acesse no navegador:**
   [http://localhost:8000](http://localhost:8000)

---

## 🧪 Suíte de Testes Automatizados

O projeto conta com suíte de testes cobrindo autenticação, ciclo de vida dos eventos, restrições de casais, validação de tokens e algoritmo de sorteio:

```bash
php artisan test --compact
```

Exemplo de resultado:
```json
{"tool":"phpunit","result":"passed","tests":35,"passed":35,"assertions":120}
```

---

## 🔒 Segurança & Privacidade

* **Tokens Não Sequenciais:** Participantes recebem tokens alfanuméricos criptograficamente seguros.
* **Proteção contra SQL Injection & Mass Assignment:** Validações rigorosas via Form Requests e Eloquent ORM.
* **Sanitização de URLs:** Proteção contra ataques XSS em links de presentes externos.
* **LGPD e Termos de Uso:** Páginas jurídicas institucionais com consentimento de cookies e tratamento de dados em conformidade com as normas brasileiras.

---

## 👨‍💻 Mantenedor & Contato

Desenvolvido pela equipe **GiftMatch**.

* **Projeto Online:** [https://giftmatch.online](https://giftmatch.online)
* **Suporte:** [suporte@giftmatch.online](mailto:suporte@giftmatch.online)

---

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE).
