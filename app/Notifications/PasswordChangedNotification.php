<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu contraseña ha sido cambiada')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line('Te informamos que tu contraseña fue cambiada exitosamente.')
            ->line('Si no realizaste este cambio, contacta al soporte inmediatamente.')
            ->action('Iniciar sesión', url('/login'))
            ->line('Gracias por usar nuestro servicio.');
    }
}