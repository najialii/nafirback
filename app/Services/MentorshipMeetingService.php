<?php

namespace App\Services;

use App\Models\MentorshipEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Zoom\Data\ScheduleMeetingData;
use Modules\Zoom\Facades\ZoomMeetingFacade;
use Modules\Zoom\Models\ZoomMeeting;

class MentorshipMeetingService
{
    public function schedule(MentorshipEntry $mentorshipEntry, ?ScheduleMeetingData $meetingData = null): ZoomMeeting
    {
        return DB::transaction(function () use ($mentorshipEntry, $meetingData) {
            $this->validateMentorshipForMeeting($mentorshipEntry);
            $meetingData = $this->prepareMeetingData($mentorshipEntry, $meetingData?->toArray() ?? []);

            $meeting = ZoomMeetingFacade::createMeeting(
                model: $mentorshipEntry,
                attributes: $meetingData,
                supervisor: $mentorshipEntry->mentor
            );

            ZoomMeetingFacade::sendNotifications($meeting);
            $mentorshipEntry->update([
                'status' => MentorshipEntry::STATUS_SCHEDULED,
            ]);
            return $meeting;
        });
    }

    public function reschedule(MentorshipEntry $mentorshipEntry, array $attributes = []): ZoomMeeting
    {
        $this->validateExistingMeeting($mentorshipEntry);

        return DB::transaction(function () use ($mentorshipEntry, $attributes) {
            $meetingData = $this->prepareMeetingData($mentorshipEntry, $attributes);
            $mentorshipEntry->update([
                'status' => MentorshipEntry::STATUS_SCHEDULED,
            ]);

            return ZoomMeetingFacade::updateMeeting(
                $mentorshipEntry->zoomMeeting,
                $meetingData
            );
        });
    }

    public function start(MentorshipEntry $mentorshipEntry): bool
    {
        $this->validateMentorshipAndMeetingForStart($mentorshipEntry);

        return DB::transaction(function () use ($mentorshipEntry) {
            $started = ZoomMeetingFacade::startMeeting($mentorshipEntry->zoomMeeting);

            if ($started) {
                $mentorshipEntry->update([
                    'status' => MentorshipEntry::STATUS_PROCESSING,
                    //Todo: Add started at
                    // 'started_at' => now(),
                ]);
            }

            return $started;
        });
    }

    public function complete(MentorshipEntry $mentorshipEntry): bool
    {
        $this->validateMentorshipAndMeetingForCompletion($mentorshipEntry);

        return DB::transaction(function () use ($mentorshipEntry) {
            $completed = ZoomMeetingFacade::completeMeeting($mentorshipEntry->zoomMeeting);

            if ($completed) {
                $mentorshipEntry->update([
                    'status' => MentorshipEntry::STATUS_COMPLETED,
                ]);
            }

            return $completed;
        });
    }

    public function cancel(MentorshipEntry $mentorshipEntry): bool
    {
        $this->validateExistingMeeting($mentorshipEntry);

        return DB::transaction(function () use ($mentorshipEntry) {
            $cancelled = ZoomMeetingFacade::cancelMeeting($mentorshipEntry->zoomMeeting);

            if ($cancelled) {
                $mentorshipEntry->update([
                    'status' => MentorshipEntry::STATUS_CANCELLED,
                ]);
            }

            return $cancelled;
        });
    }

    protected function validateMentorshipForMeeting(MentorshipEntry $mentorshipEntry): void
    {
        if ($mentorshipEntry->zoomMeeting()->exists()) {
            throw new \Exception('Mentorship entry already has a meeting start time');
        }

        if (!$mentorshipEntry->start_at) {
            throw new \Exception('Mentorship entry must have a start time');
        }

        if (!$mentorshipEntry->duration) {
            throw new \Exception('Mentorship entry must have a duration');
        }
    }

    protected function validateExistingMeeting(MentorshipEntry $mentorshipEntry): void
    {
        if (!$mentorshipEntry->zoomMeeting) {
            throw new \Exception('Mentorship entry does not have a meeting scheduled');
        }

        if ($mentorshipEntry->zoomMeeting->isStarted()) {
            throw new \Exception('Cannot modify an active meeting');
        }
    }

    protected function validateMentorshipAndMeetingForStart(MentorshipEntry $mentorshipEntry): void
    {
        if (!$mentorshipEntry->zoomMeeting) {
            throw new \Exception('No meeting scheduled for this mentorship entry');
        }

        if ($mentorshipEntry->zoomMeeting->isStarted()) {
            throw new \Exception('Meeting is already started');
        }
    }

    protected function validateMentorshipAndMeetingForCompletion(MentorshipEntry $mentorshipEntry): void
    {
        if (!$mentorshipEntry->zoomMeeting?->isStarted()) {
            throw new \Exception('Meeting must be started before completion');
        }
    }

    protected function prepareMeetingData(MentorshipEntry $mentorshipEntry, array $attributes): array
    {
        // Calculate duration and cap it at 1440 minutes (24 hours) to satisfy Zoom API limits
        $duration = $attributes['duration'] ?? $mentorshipEntry->duration;
        $duration = min($duration, 1440);

        return [
            'topic' => $attributes['topic'] ?? "Mentorship Session with {$mentorshipEntry->mentor->name}",
            'preferred_start_time' => $attributes['start_time'] ?? $mentorshipEntry->start_at,
            'duration' => $duration,
            'agenda' => $attributes['agenda'] ?? "Mentorship session for {$mentorshipEntry->mentee->name}",
            'settings' => $attributes['settings'] ?? [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => true,
                'waiting_room' => true,
                'meeting_authentication' => true,
                'auto_recording' => 'none',
            ]
        ];
    }
}
