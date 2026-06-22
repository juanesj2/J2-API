<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // 1. Obtener la app (love-widget o enfoca)
        $app = $notifiable->app ?? 'enfoca';

        // 2. Construir la URL de reseteo.
        // Si tienes una URL de frontend especifica, puedes cambiar esto.
        // Por ahora usamos la de Laravel por defecto con nuestro nuevo diseño.
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // 3. Devolver la vista personalizada pasando la variable $app y $url
        return (new MailMessage)
            ->subject(Lang::get('Restablecer Contraseña'))
            ->view('emails.password.reset', [
                'url' => $url,
                'app' => $app,
            ]);
    }
}
