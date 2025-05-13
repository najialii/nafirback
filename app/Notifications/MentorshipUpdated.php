<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Mentorship;
use App\Models\User;

class MentorshipUpdated extends Notification implements ShouldQueue
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
        $subject = 'Mentorship Program Updated: ' . $this->mentorship->name;
        $greeting = 'Hello ' . ($notifiable->name ?? 'User') . ',';
        $line1 = 'The mentorship program "' . $this->mentorship->name . '" has been updated.';
        $actionText = 'View Mentorship Program';
        // Assuming you have a named route like 'mentorships.show'

        //Todo: Add real urls and pass base url as environment variable
        if ($notifiable->hasRole(User::ROLE_MENTOR)) {
            $actionUrl = "https://nafir.net/mentor/mentorships/{$this->mentorship->id}";
        } else if ($notifiable->hasRole(User::ROLE_MENTEE)) {
            $actionUrl = "https://nafir.net/mentee/mentorships/{$this->mentorship->id}";
        } else if ($notifiable->hasRole(User::ROLE_ADMIN)) {
            //Todo: use dashboard route for admin
            $actionUrl = "";
        } else {
            $actionUrl = '';
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
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
            'message' => 'The mentorship program "' . $this->mentorship->name . '" has been updated.',
            'link' => url('/mentorships/' . $this->mentorship->id),
        ];
    }
}
