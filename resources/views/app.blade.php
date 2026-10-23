<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $component = $page['component'] ?? '';
            $props = $page['props'] ?? [];
            $event = $props['event'] ?? null;

            // Ensure HTTPS when in production or on shardweb.app
            $isSecure = request()->isSecure()
                || request()->header('x-forwarded-proto') === 'https'
                || str_contains(request()->getHost(), 'shardweb.app')
                || app()->environment('production');

            $currentUrl = url(request()->getRequestUri(), [], $isSecure);

            if ($component === 'Invite/Show' && !empty($event['title'])) {
                $metaTitle = "Convite: {$event['title']} 🎁 - Amigo Secreto";
                $organizer = $event['organizer_name'] ?? 'o organizador';
                $descParts = ["Você foi convidado(a) por {$organizer} para o Amigo Secreto \"{$event['title']}\"!"];
                if (!empty($event['event_date'])) {
                    $descParts[] = "Confraternização: {$event['event_date']}.";
                }
                if (!empty($event['budget_limit'])) {
                    $descParts[] = "Valor sugerido: R$ " . number_format((float) $event['budget_limit'], 2, ',', '.') . ".";
                }
                $descParts[] = "Clique aqui para confirmar sua presença, indicar seus presentes desejados e participar do sorteio.";
                $metaDesc = implode(' ', $descParts);
                $metaImage = url('og-invite.jpg', [], $isSecure);
            } elseif ($component === 'Secret/Show') {
                $metaTitle = "Meu Amigo Secreto 🎁 - Link Exclusivo";
                $metaDesc = "Descubra quem você tirou no Amigo Secreto, confira a lista de desejos e prepare o seu presente!";
                $metaImage = url('og-home.jpg', [], $isSecure);
            } else {
                $metaTitle = "Amigo Secreto Online 🎁 - Sorteio Grátis com Regras e Desejos";
                $metaDesc = "Organize seu Amigo Secreto de forma simples, rápida e gratuita! Sorteio automático sem ninguém tirar a si mesmo, regras para casais, lista de desejos e envio direto no WhatsApp.";
                $metaImage = url('og-home.jpg', [], $isSecure);
            }
        @endphp

        <title inertia>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDesc }}">
        <link rel="canonical" href="{{ $currentUrl }}">

        <!-- Open Graph / Facebook / WhatsApp -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Amigo Secreto Online">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDesc }}">
        <meta property="og:url" content="{{ $currentUrl }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:image:secure_url" content="{{ $metaImage }}">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $metaTitle }}">

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDesc }}">
        <meta name="twitter:image" content="{{ $metaImage }}">

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ url('favicon.ico', [], $isSecure) }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 selection:bg-rose-500 selection:text-white">
        @inertia
    </body>
</html>
