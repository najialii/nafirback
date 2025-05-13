<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MentorshipEntry;
use App\Models\User;

class MentorshipEntryStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $mentorshipEntry;
    public $oldStatus;

    /**
     * Create a new notification instance.
     *
     * @param MentorshipEntry $mentorshipEntry
     * @param string $oldStatus
     * @return void
     */
    public function __construct(MentorshipEntry $mentorshipEntry, string $oldStatus)
    {
        $this->mentorshipEntry = $mentorshipEntry;
        $this->oldStatus = $oldStatus;
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
     * @param  mixed  $notifiable (This will typically be the Mentee)
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mentorship = $this->mentorshipEntry->mentorship;
        $newStatus = $this->mentorshipEntry->status;

        $subject = 'Mentorship Application Status Updated: ' . $mentorship->name;
        $greeting = 'Hello ' . ($notifiable->name ?? 'Mentee') . ',';
        $line1 = 'The status of your application for the mentorship program "' . $mentorship->name . '" has been updated.';
        $line2 = 'Previous Status: ' . ucfirst($this->oldStatus);
        $line3 = 'New Status: ' . ucfirst($newStatus);
        $actionText = 'View Application Details';

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

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->line($line2)
            ->line($line3);

        if ($newStatus === 'approved') {
            $mailMessage->line('Congratulations! Your application has been approved.');
            // You could add more details here, like next steps or contact information for the mentor.
        } elseif ($newStatus === 'rejected') {
            $mailMessage->line('We regret to inform you that your application was not approved at this time.');
            // You could offer generic feedback or links to other resources.
        }

        $mailMessage->action($actionText, $actionUrl)
                    ->line('Thank you for your interest!');

        return $mailMessage;
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->mentorshipEntry->status,
            'message' => 'Your application status for "' . ($this->mentorshipEntry->mentorship->name ?? 'N/A') . '" changed from ' . $this->oldStatus . ' to ' . $this->mentorshipEntry->status . '.',
            'link' => url('/my-applications/' . $this->mentorshipEntry->id), // Or a relevant URL for the mentee
        ];
    }
}
