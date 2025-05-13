<?php

namespace Modules\Zoom\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Zoom\Models\ZoomMeeting;

class ZoomMeetingScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly ZoomMeeting $zoomMeeting)
    {
        //
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
        $locale = $this->zoomMeeting->model->locale ?? config('app.locale');

        return (new MailMessage)
            ->subject(__('notifications.meeting_scheduled.subject', [], $locale))
            ->line(__('notifications.meeting_scheduled.intro', [], $locale))
            ->line(__('notifications.meeting_scheduled.details', [
                'topic' => $this->zoomMeeting->topic,
                'time' => $this->zoomMeeting->start_time->format('F j, Y g:i A'),
                'duration' => $this->zoomMeeting->duration,
            ], $locale))
            ->action(
                __('notifications.meeting_scheduled.action', [], $locale),
                route('Event.meetings.show', $this->zoomMeeting->id)
            );
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

