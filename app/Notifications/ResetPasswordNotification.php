<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
        
        // Set locale to Armenian for email
        $locale = 'hy';

        return (new MailMessage)
            ->subject(__('auth.reset_password_subject', [], $locale))
            ->greeting(__('auth.reset_password_greeting', ['name' => $notifiable->name], $locale))
            ->line(__('auth.reset_password_line1', [], $locale))
            ->action(__('auth.reset_password_action', [], $locale), $url)
            ->line(__('auth.reset_password_line2', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')], $locale))
            ->line(__('auth.reset_password_line3', [], $locale))
            ->salutation(__('auth.reset_password_salutation', [], $locale));
    }
}
