<?php

namespace Modules\PersonMS\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VerifyInfoNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
//    public function toMail($notifiable): MailMessage
//    {
//        return (new MailMessage)
//            ->line('The introduction to the notification.')
//            ->action('Notification Action', 'https://laravel.com')
//            ->line('Thank you for using our application!');
//    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $message = 'به سیستم ایل یار خوش آمید برای استفاده از سیستم لطفا اطلاعات خود را تایید کنید';
//        $message = 'confirm your info';
        return [
            'message' => $message,
        ];
    }
}
