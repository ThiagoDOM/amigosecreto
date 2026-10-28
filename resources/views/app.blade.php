<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $component = $page['component'] ?? '';
        $props = $page['props'] ?? [];
        $event = $props['event'] ?? null;

        // Ensure HTTPS when in production, on shardweb.app, or on giftmatch.online
        $isSecure =
            request()->isSecure() ||
            request()->header('x-forwarded-proto') === 'https' ||
            str_contains(request()->getHost(), 'shardweb.app') ||
            str_contains(request()->getHost(), 'giftmatch.online') ||
            app()->environment('production');

        $currentUrl = url(request()->getRequestUri(), [], $isSecure);

        if ($component === 'Invite/Show' && !empty($event['title'])) {
            $metaTitle = "Convite: {$event['title']} - GiftMatch";
            $organizer = $event['organizer_name'] ?? 'o organizador';
            $descParts = [
                "Você foi convidado(a) por {$organizer} para o Amigo Secreto \"{$event['title']}\" no GiftMatch!",
            ];
            if (!empty($event['event_date'])) {
                $descParts[] = "Confraternização: {$event['event_date']}.";
            }
            if (!empty($event['budget_limit'])) {
                $descParts[] = "Valor sugerido: R$ " . number_format((float) $event['budget_limit'], 2, ',', '.') . '.';
            }
            $descParts[] =
                'Clique aqui para confirmar sua presença, indicar seus presentes desejados e participar do sorteio.';
            $metaDesc = implode(' ', $descParts);
            $metaImage = url('og-invite.jpg', [], $isSecure);
            $imgWidth = 1376;
            $imgHeight = 768;
        } elseif ($component === 'Secret/Show') {
            $metaTitle = 'Meu Amigo Secreto - GiftMatch';
            $metaDesc =
                'Descubra quem você tirou no Amigo Secreto no GiftMatch, confira a lista de desejos e prepare o seu presente!';
            $metaImage = url('og-home.jpg', [], $isSecure);
            $imgWidth = 1376;
            $imgHeight = 768;
        } elseif ($component === 'Simulation/Index') {
            $metaTitle = 'Simulação Interativa de Amigo Secreto - GiftMatch';
            $metaDesc =
                'Veja na prática como seus convidados se inscrevem e revelam o amigo secreto no GiftMatch. Teste 100% grátis e sem cadastro.';
            $metaImage = url('og-home.jpg', [], $isSecure);
            $imgWidth = 1376;
            $imgHeight = 768;
        } else {
            $metaTitle = 'GiftMatch - Amigo Secreto Online Inteligente e Grátis';
            $metaDesc =
                'Organize seu Amigo Secreto no GiftMatch de forma simples, rápida e gratuita! Sorteio automático sem ninguém tirar a si mesmo, regras para casais, lista de desejos e envio direto no WhatsApp.';
            $metaImage = url('og-home.jpg', [], $isSecure);
            $imgWidth = 1376;
            $imgHeight = 768;
        }
    @endphp

    <title inertia>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- Open Graph / Facebook / WhatsApp (Primary Crawler Target) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GiftMatch">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:secure_url" content="{{ $metaImage }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="{{ $imgWidth }}">
    <meta property="og:image:height" content="{{ $imgHeight }}">
    <meta property="og:image:alt" content="{{ $metaTitle }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    <!-- PWA & Service Worker (Registered safely after window load without blocking render) -->
    <script>
        (function() {
            try {
                // One-time safe cache migration check without thrashing storage on every visit
                var CACHE_VER = 'gm_sw_clean_20261028';
                if (window.localStorage && !localStorage.getItem(CACHE_VER)) {
                    if ('caches' in window) {
                        caches.keys().then(function(names) {
                            names.forEach(function(n) { caches.delete(n); });
                        }).catch(function() {});
                    }
                    localStorage.setItem(CACHE_VER, '1');
                }
            } catch (e) {}

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    try {
                        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function(err) {
                            console.debug('SW reg notice:', err);
                        });
                    } catch (e) {}
                });
            }
        })();
    </script>

    <!-- Instant Preloader (renders immediately while Inertia/Vue bundle downloads) -->
    <style>
        #app:empty {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #020617;
        }
        #app:empty::before {
            content: '';
            display: block;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid rgba(244, 63, 94, 0.2);
            border-top-color: #f43f5e;
            animation: gm-spin 0.75s linear infinite;
            margin-bottom: 14px;
        }
        #app:empty::after {
            content: 'Carregando GiftMatch...';
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            letter-spacing: 0.03em;
        }
        @keyframes gm-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <!-- Favicons & App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ url('favicon.svg', [], $isSecure) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('favicon-32x32.png', [], $isSecure) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('favicon-16x16.png', [], $isSecure) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('apple-touch-icon.png', [], $isSecure) }}">
    <link rel="shortcut icon" href="{{ url('favicon.ico', [], $isSecure) }}">

    <!-- PWA & Mobile Web App Manifest -->
    <link rel="manifest" href="{{ url('manifest.webmanifest', [], $isSecure) }}">
    <meta name="theme-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GiftMatch">

    <!-- Fonts (High-performance non-render-blocking) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap">
    </noscript>

    <!-- Google AdSense -->
    @php
        $adsenseClientId = config('services.adsense.client_id');
    @endphp
    @if(!empty($adsenseClientId))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsenseClientId }}" crossorigin="anonymous"></script>
    @endif

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased bg-slate-950 text-slate-100 selection:bg-rose-500 selection:text-white">
    @inertia
</body>

</html>
