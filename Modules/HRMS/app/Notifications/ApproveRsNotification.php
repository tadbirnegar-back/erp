<?php

namespace Modules\HRMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class ApproveRsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
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
        return [SmsChannel::class];
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

    /**
     * Get the repicients and body of the notification.
     *
     * @param mixed $notifiable
     * @return \Exception|Builder
     */
    public function toSms($notifiable)
    {
        try {
            //Todo: Change Pattern
            $a = (new Builder)->via('farazsmspattern') # via() is Optional
            ->send("patterncode=6kqdiqpxgzoiujk \n username={$this->username}")
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
