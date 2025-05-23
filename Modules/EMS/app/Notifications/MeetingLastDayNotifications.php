<?php

namespace Modules\EMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class MeetingLastDayNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private string $username;
    private string $date;

    private string $meetingType;

    /**
     * @param string $otpCode
     */
    public function __construct(string $username, string $date , $text)
    {
        $this->username = $username;
        $this->date = $date;
        $this->meetingType = $text;
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
            ->send("patterncode=ye3xdk0ub8clsh5 \n username={$this->username} \n date={$this->date} \n meetingType={$this->meetingType}")
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
