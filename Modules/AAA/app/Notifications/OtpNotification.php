<?php

namespace Modules\AAA\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\AAA\app\Http\Enums\OtpPatternsEnum;
use Tzsk\Sms\Builder;
use Tzsk\Sms\Channels\SmsChannel;
use Tzsk\Sms\Exceptions\InvalidMessageException;

class OtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private string $otpCode;
    private string $patternCode;
    /**
     * @param string $otpCode
     */
    public function __construct(string $otpCode , string $patternCode)
    {
        $this -> patternCode = $patternCode;
        $this->otpCode = $otpCode;
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
            $a= (new Builder)->via('farazsmspattern') # via() is Optional
            ->send("patterncode=l9i5x7nfvw7oofq \n verification-code={$this->otpCode}")
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
