<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobFailedNotification extends Notification
{
    use Queueable;

    private $jobClass;

    private $errorMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct($jobClass, $errorMessage)
    {
        $jobClass = $this->jobClass;
        $errorMessage = $this->errorMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Job Failure Notification')
            ->line('A job has failed.')
            ->line('Job Class: ' . $this->jobClass)
            ->line('Error Message: ' . $this->errorMessage)
            ->action('View Laravel Horizon', url('/horizon'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
