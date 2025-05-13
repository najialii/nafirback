<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class MentorshipDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $mentorshipName;

    /**
     * Create a new notification instance.
     *
     * @param string $mentorshipName
     * @return void
     */
    public function __construct(string $mentorshipName)
    {
        $this->mentorshipName = $mentorshipName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = 'Mentorship Program Deleted: ' . $this->mentorshipName;
        $greeting = 'Hello ' . ($notifiable->name ?? 'User') . ',';
        $line1 = 'The mentorship program "' . $this->mentorshipName . '" has been deleted.';
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'mentorship_name' => $this->mentorshipName,
            'message' => 'The mentorship program "' . $this->mentorshipName . '" has been deleted.',
        ];
    }
}
