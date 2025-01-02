<?php

namespace Modules\OUnitMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class ChangeNumNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private string $pre_num;
    private string $current_num;


    public function __construct(string $pre_num, string $current_num)
    {
        $this->pre_num = $pre_num;
        $this->current_num = $current_num;

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
            $a = (new Builder)->via('farazsmspattern')
            ->send("patterncode=zd9ez1vcrggytr8 \n pre_num={$this->pre_num} \n current_num={$this -> current_num}")
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
