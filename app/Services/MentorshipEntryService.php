<?php

namespace App\Services;

use App\Models\MentorshipEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Zoom\Data\ScheduleMeetingData;

class MentorshipEntryService
{
    public function __construct(private readonly MentorshipMeetingService $meetingService)
    {
    }

    public function schedule(MentorshipEntry $mentorshipEntry, ?ScheduleMeetingData $meetingData = null): bool
    {
        return DB::transaction(function () use ($mentorshipEntry, $meetingData) {
            $meeting = $this->meetingService->schedule($mentorshipEntry, $meetingData);
            return (bool) $meeting;
        });
    }

    public function reschedule(MentorshipEntry $mentorshipEntry, array $attributes = []): bool
    {
        return DB::transaction(function () use ($mentorshipEntry, $attributes) {
            $meeting = $this->meetingService->reschedule($mentorshipEntry, $attributes);
            return (bool) $meeting;
        });
    }

    public function cancel(MentorshipEntry $mentorshipEntry): bool
    {
        return DB::transaction(function () use ($mentorshipEntry) {
            return $this->meetingService->cancel($mentorshipEntry);
        });
    }

    public function complete(MentorshipEntry $mentorshipEntry): bool
    {
        return DB::transaction(function () use ($mentorshipEntry) {
            return $this->meetingService->complete($mentorshipEntry);
        });
    }
}
