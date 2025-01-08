<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpSendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    /**
 * Get the mail representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }


    public function build()
    {
        return $this->view('emails.auth.forgot_password_otp',['user' => $this->user]);

    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {

        //dd($notifiable->email);
        return (new MailMessage)
            ->subject($this->user->subject)
            ->view('emails.auth.forgot_password_otp', ['user' => $this->user]);

           // ->to($notifiable->email);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
