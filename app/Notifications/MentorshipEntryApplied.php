<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MentorshipEntry; // Assuming you have this model
use App\Models\User;

class MentorshipEntryApplied extends Notification implements ShouldQueue
{
    use Queueable;

    public $mentorshipEntry;

    /**
     * Create a new notification instance.
     *
     * @param MentorshipEntry $mentorshipEntry
     * @return void
     */
    public function __construct(MentorshipEntry $mentorshipEntry)
    {
        $this->mentorshipEntry = $mentorshipEntry;
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
     * @param  mixed  $notifiable (This will typically be the Mentor)
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mentee = $this->mentorshipEntry->mentee; // Assuming a 'mentee' relationship on MentorshipEntry
        $mentorship = $this->mentorshipEntry->mentorship; // Assuming a 'mentorship' relationship

        $subject = 'New Mentorship Application: ' . $mentorship->name;
        $greeting = 'Hello ' . ($notifiable->name ?? 'Mentor') . ',';
        $line1 = ($mentee->name ?? 'A mentee') . ' has applied for your mentorship program: "' . $mentorship->name . ".";
        $actionText = 'View Application';
        // You'll need to define a route to view a specific application

        //Todo: Add real urls and pass base url as environment variable
        if ($notifiable->hasRole(User::ROLE_MENTOR)) {
            $actionUrl = "https://nafir.net/mentor/mentorship-entry/{$this->mentorshipEntry->id}";
        } else if ($notifiable->hasRole(User::ROLE_MENTEE)) {
            $actionUrl = "https://nafir.net/mentee/mentorship-entry/{$this->mentorshipEntry->id}";
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
                    ->line('Reason for application: ' . ($this->mentorshipEntry->application_details ?? 'N/A')) // Assuming a field for application details
                    ->action($actionText, $actionUrl)
                    ->line('Please review the application at your earliest convenience.');
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
            'mentorship_entry_id' => $this->mentorshipEntry->id,
            'mentorship_id' => $this->mentorshipEntry->mentorship_id,
            'mentorship_name' => $this->mentorshipEntry->mentorship->name ?? 'N/A',
            'mentee_id' => $this->mentorshipEntry->mentee_id,
            'mentee_name' => $this->mentorshipEntry->mentee->name ?? 'N/A',
            'message' => ($this->mentorshipEntry->mentee->name ?? 'A mentee') . ' applied for "' . ($this->mentorshipEntry->mentorship->name ?? 'N/A') . ".",
            // Assuming you have a named route like 'mentorship-entries.show'
            'link' => route('mentorship-entries.show', $this->mentorshipEntry->id),
        ];
    }
}
