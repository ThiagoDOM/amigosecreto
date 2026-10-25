<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorteio Realizado - GiftMatch</title>
</head>
<body style="margin: 0; padding: 0; background-color: #020617; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f8fafc;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #020617; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #0f172a; border-radius: 24px; border: 1px solid #1e293b; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 32px 24px 32px; background: linear-gradient(135deg, rgba(225, 29, 72, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);">
                            <div style="display: inline-block; padding: 8px 16px; border-radius: 9999px; background-color: rgba(225, 29, 72, 0.2); border: 1px solid rgba(225, 29, 72, 0.4); margin-bottom: 16px;">
                                <span style="font-size: 13px; font-weight: bold; color: #fda4af; text-transform: uppercase; letter-spacing: 0.05em;">🎉 Sorteio Realizado!</span>
                            </div>
                            <h1 style="margin: 0; font-size: 26px; font-weight: 900; color: #ffffff; letter-spacing: -0.025em; line-height: 1.25;">
                                Amigo Secreto GiftMatch
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px;">
                            <p style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #ffffff;">
                                Olá, {{ $participant->name }}! 👋
                            </p>
                            <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #cbd5e1;">
                                Boas notícias! O sorteio do amigo secreto <strong style="color: #ffffff;">"{{ $event->title }}"</strong> acabou de ser realizado.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $revealUrl }}" target="_blank" style="display: inline-block; padding: 16px 36px; background: linear-gradient(135deg, #e11d48 0%, #f43f5e 50%, #f59e0b 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 800; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.5); letter-spacing: 0.02em;">
                                            🎁 Ver Meu Amigo Secreto
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Event Details Card -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 20px; margin-bottom: 24px;">
                                <tr>
                                    <td>
                                        <h2 style="margin: 0 0 12px 0; font-size: 13px; font-weight: 800; color: #fda4af; text-transform: uppercase; letter-spacing: 0.05em;">
                                            Detalhes do Evento
                                        </h2>
                                        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="4" style="font-size: 14px; color: #e2e8f0;">
                                            <tr>
                                                <td width="35%" style="color: #94a3b8; font-weight: 600;">Evento:</td>
                                                <td style="font-weight: 700; color: #ffffff;">{{ $event->title }}</td>
                                            </tr>
                                            @if($event->user?->name)
                                            <tr>
                                                <td style="color: #94a3b8; font-weight: 600;">Organizado por:</td>
                                                <td>{{ $event->user->name }}</td>
                                            </tr>
                                            @endif
                                            @if($event->budget_limit)
                                            <tr>
                                                <td style="color: #94a3b8; font-weight: 600;">Valor sugerido:</td>
                                                <td style="color: #34d399; font-weight: bold;">R$ {{ number_format($event->budget_limit, 2, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            @if($event->event_date)
                                            <tr>
                                                <td style="color: #94a3b8; font-weight: 600;">Confraternização:</td>
                                                <td>{{ $event->event_date->format('d/m/Y') }}</td>
                                            </tr>
                                            @endif
                                            @if($event->location)
                                            <tr>
                                                <td style="color: #94a3b8; font-weight: 600;">Local:</td>
                                                <td>{{ $event->location }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Useful tip -->
                            <div style="background-color: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 8px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #fde68a;">
                                    💡 <strong>Dica:</strong> Guarde o link de acesso aos seus favoritos. Ao acessar, você descobrirá quem você tirou e também poderá visualizar ou atualizar a sua própria lista de presentes e preferências (tamanhos de roupas, calçados, gostos).
                                </p>
                            </div>

                            <!-- Raw link fallback -->
                            <p style="margin: 0; font-size: 12px; line-height: 1.6; color: #64748b; word-break: break-all;">
                                Se o botão acima não funcionar, copie e cole este link no seu navegador:<br>
                                <a href="{{ $revealUrl }}" target="_blank" style="color: #f43f5e; text-decoration: underline;">{{ $revealUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 24px 32px; background-color: #090d16; border-top: 1px solid #1e293b; font-size: 12px; color: #64748b; line-height: 1.5;">
                            <p style="margin: 0 0 8px 0;">
                                Enviado pelo <strong style="color: #94a3b8;">GiftMatch</strong> &bull; O jeito moderno e fácil de organizar seu Amigo Secreto.
                            </p>
                            <p style="margin: 0;">
                                Você recebeu este e-mail porque se inscreveu no evento "{{ $event->title }}".
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
