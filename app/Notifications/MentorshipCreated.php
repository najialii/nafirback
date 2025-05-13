<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Mentorship;
use App\Models\User; // Assuming User model for notifiable

class MentorshipCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $mentorship;

    /**
     * Create a new notification instance.
     *
     * @param Mentorship $mentorship
     * @return void
     */
    public function __construct(Mentorship $mentorship)
    {
        $this->mentorship = $mentorship;
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
        $subject = 'New Mentorship Program Created: ' . $this->mentorship->name;
        $greeting = 'Hello ' . ($notifiable->name ?? 'User') . ',';
        $line1 = 'A new mentorship program, "' . $this->mentorship->name . '", has been successfully created.';
        $actionText = 'View Mentorship Program';

        //Todo: Add real urls and pass base url as environment variable
        if ($notifiable->hasRole(User::ROLE_MENTOR)) {
            $actionUrl = "https://nafir.net/mentor/mentorships/{$this->mentorship->id}";
        } else if ($notifiable->hasRole(User::ROLE_MENTEE)) {
            $actionUrl = "https://nafir.net/mentee/mentorships/{$this->mentorship->id}";
        } else if ($notifiable->hasRole(User::ROLE_ADMIN)) {
            $actionUrl = "https://nafir.net/mentee/mentorships/{$this->mentorship->id}";
        } else {
            $actionUrl = '';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->line('Details: ' . ($this->mentorship->description ?? 'N/A'))
            ->action($actionText, $actionUrl)
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
            'mentorship_id' => $this->mentorship->id,
            'mentorship_name' => $this->mentorship->name,
            'message' => 'A new mentorship program "' . $this->mentorship->name . '" has been created.',
            'link' => url('/mentorships/' . $this->mentorship->id),
        ];
    }
}
