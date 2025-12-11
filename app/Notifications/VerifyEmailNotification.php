<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmailBase
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        // Set locale to Armenian for email
        $locale = 'hy';

        return (new MailMessage)
            ->subject(__('auth.verify_email_subject', [], $locale))
            ->greeting(__('auth.verify_email_greeting', ['name' => $notifiable->name], $locale))
            ->line(__('auth.verify_email_line1', [], $locale))
            ->action(__('auth.verify_email_action', [], $locale), $verificationUrl)
            ->line(__('auth.verify_email_line2', [], $locale))
            ->salutation(__('auth.verify_email_salutation', [], $locale));
    }
}
