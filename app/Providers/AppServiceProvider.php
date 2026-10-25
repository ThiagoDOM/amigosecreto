<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Recuperação de Senha - '.config('app.name'))
                ->greeting('Olá, '.($notifiable->name ?: 'amigo(a)').'!')
                ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta.')
                ->action('Redefinir Minha Senha', $url)
                ->line('Este link de redefinição de senha expirará em '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutos.')
                ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária. Sua conta continua em segurança.')
                ->salutation('Atenciosamente,'.PHP_EOL.'Equipe '.config('app.name'));
        });
    }
}
