<?php

namespace Modules\HRMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class PayanKhedmatRsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private string $username;
    private string $ounit_name;
    private string $position_name;

    /**
     * @param string $otpCode
     */
    public function __construct(string $username, string $ounit_name, string $position_name)
    {
        $this->username = $username;
        $this->position_name = $position_name;
        $this->ounit_name = $ounit_name;
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
            ->send("patterncode=l9i5x7nfvw7oofq \n username={$this->username} \n  ounit_name={$this->ounit_name} \n  position_name={$this->position_name} ")
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
