<?php

namespace Modules\EMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class AlertMMLastDatNotification extends Notification
{
    use Queueable;

    private string $username;

    /**
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', 'https://laravel.com')
            ->line('Thank you for using our application!');
    }


    public function toSms($notifiable)
    {
        //TODO: Change Pattern

        try {
            $a = (new Builder)->via('farazsmspattern') # via() is Optional
            ->send("patterncode=l9i5x7nfvw7oofq \n username={$this->username}")
                ->to($notifiable->mobile);


            return $a;
        } catch (InvalidMessageException $e) {
            return $e;
        }

    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
