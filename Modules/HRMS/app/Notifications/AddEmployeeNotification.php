<?php

namespace Modules\HRMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class AddEmployeeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private string $username;
    private string $posname;
    private string $ounit_name;

    public function __construct(string $username, $posName, $ounit_name)
    {
        $this->username = $username;
        $this->posname = $posName;
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
            $a = (new Builder)->via('farazsmspattern') # via() is Optional
            ->send("patterncode=ovukyjs4dyfllf1 \n username={$this->username} \n posname={$this->posname} \n ounit={$this -> ounit_name}")
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
