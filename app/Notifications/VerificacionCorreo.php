<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerificacionCorreo extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->greeting('¡Hola ' . $notifiable->first_name . '!')
            ->line('Gracias por registrarte. Por favor verifica tu correo haciendo clic en el botón.')
            ->action('Verificar correo', $url)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no creaste una cuenta, ignora este mensaje.')
            ->salutation(' ');
    }
}