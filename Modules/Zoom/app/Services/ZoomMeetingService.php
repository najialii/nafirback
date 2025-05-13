<?php
namespace Modules\Zoom\Services;

use Modules\Zoom\Enums\ZoomMeetingStatus;
use App\Models\User;
use  Modules\Zoom\Models\ZoomMeeting;
use Modules\Zoom\Notifications\ZoomMeetingScheduled;
use Modules\Zoom\Services\ZoomService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ZoomMeetingService
{
    public function __construct(
        private readonly ZoomService $zoomService
    ) {}

    protected function findAvailableZoomUser(Carbon $startTime, int $duration, ?User $supervisor = null): ?string
    {
        // Get all available Zoom users for the time slot
        $availableZoomUsers = $this->zoomService->getAvailableUsers($startTime, $duration);

        if ($availableZoomUsers->isEmpty()) {
            throw new \Exception('No available Zoom users found for the requested time slot');
        }

        // If supervisor is provided, check their availability
        if ($supervisor) {
            // Check if supervisor has any conflicting meetings in our system
            $hasConflict = ZoomMeeting::query()
                ->where('supervisor_id', $supervisor->id)
                ->where(function ($query) use ($startTime, $duration) {
                    $endTime = $startTime->copy()->addMinutes($duration);

                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                            ->where('start_time', '>=', $startTime);
                    })->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) > ?', [$startTime]);
                    });
                })
                ->exists();

            //TODO: enable has conflict with proper localization
            // if ($hasConflict) {
            //     throw new \Exception('Supervisor is not available at the requested time');
            // }
        }

        // Return first available user's ID
        return $availableZoomUsers->first()['id'];
    }

    public function createMeeting(
        Model $model,
        array $attributes,
        ?User $supervisor = null
    ): ?ZoomMeeting {
        try {
            return DB::transaction(function () use ($model, $attributes, $supervisor) {
                $startTime = Carbon::parse($attributes['preferred_start_time']);
                //Todo: add check for supervisor and add superviso role
                $supervisor ??= User::whereHas('roles', fn($query) => $query->whereIn('name', ['supervisor']))->first();

                // Find available Zoom user
                $userId = $this->findAvailableZoomUser(
                    $startTime,
                    $attributes['duration'],
                    $supervisor
                );

                $meetingData = [
                    'topic' => $attributes['topic'],
                    'type' => 2, // Scheduled meeting
                    'start_time' => $startTime->format('Y-m-d\TH:i:s'),
                    'duration' => $attributes['duration'],
                    'timezone' => config('app.timezone'),
                    'agenda' => $attributes['agenda'] ?? null,
                    'settings' => array_merge([
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                        'waiting_room' => true,
                        'meeting_authentication' => true,
                        'auto_recording' => 'none',
                    ], $attributes['settings'] ?? [])
                ];

                $response = $this->zoomService->createMeeting($userId, $meetingData);
                return $model->zoomMeeting()->create([
                    'supervisor_id' => $supervisor?->getKey(),
                    'zoom_user_id' => $userId,
                    'zoom_meeting_id' => $response['id'],
                    'topic' => $response['topic'],
                    'agenda' => $attributes['agenda'] ?? null,
                    'preferred_start_time' => $startTime,
                    'start_time' => $startTime,
                    'duration' => $response['duration'],
                    'password' => $response['password'] ?? null,
                    'join_url' => $response['join_url'],
                    'start_url' => $response['start_url'],
                    'settings' => $response['settings'],
                ]);
            });
        } catch (\Throwable $th) {
            Log::error('Error creating Zoom meeting', [
                'model_id' => $model->getKey(),
                'attributes' => $attributes,
                'supervisor_id' => $supervisor?->getKey(),
                'exception' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            if (!app()->environment('production')) {
                throw $th;
            }

            return null;
        }
    }

    public function updateMeeting(ZoomMeeting $meeting, array $attributes): ZoomMeeting
    {
        return DB::transaction(function () use ($meeting, $attributes) {
            // Check availability for new time if it's being updated
            if (isset($attributes['preferred_start_time'])) {
                $startTime = Carbon::parse($attributes['preferred_start_time']);
                $duration = $attributes['duration'] ?? $meeting->duration;

                // Find available user for new time slot
                $userId = $this->findAvailableZoomUser(
                    $startTime,
                    $duration,
                    $meeting->supervisor
                );

                // If different user is available, we need to create new meeting and delete old one
                if ($meeting->zoom_user_id !== $userId) {
                    $this->zoomService->deleteMeeting($meeting->zoom_meeting_id);

                    $response = $this->zoomService->createMeeting($userId, [
                        'topic' => $attributes['topic'] ?? $meeting->topic,
                        'start_time' => $startTime->format('Y-m-d\TH:i:s'),
                        'duration' => $duration,
                        'timezone' => config('app.timezone'),
                        'agenda' => $attributes['agenda'] ?? $meeting->agenda,
                        'settings' => array_merge(
                            $meeting->settings,
                            $attributes['settings'] ?? []
                        )
                    ]);

                    $meeting->update([
                        'zoom_meeting_id' => $response['id'],
                        'topic' => $response['topic'],
                        'agenda' => $attributes['agenda'] ?? $meeting->agenda,
                        'preferred_start_time' => $startTime,
                        'start_time' => $startTime,
                        'duration' => $response['duration'],
                        'password' => $response['password'] ?? null,
                        'join_url' => $response['join_url'],
                        'start_url' => $response['start_url'],
                        'settings' => $response['settings'],
                    ]);
                } else {
                    // Update existing meeting
                    $response = $this->zoomService->updateMeeting(
                        $meeting->zoom_meeting_id,
                        [
                            'topic' => $attributes['topic'] ?? $meeting->topic,
                            'start_time' => $startTime->format('Y-m-d\TH:i:s'),
                            'duration' => $duration,
                            'agenda' => $attributes['agenda'] ?? $meeting->agenda,
                            'settings' => array_merge(
                                $meeting->settings,
                                $attributes['settings'] ?? []
                            )
                        ]
                    );

                    $meeting->update([
                        'topic' => $response['topic'],
                        'agenda' => $attributes['agenda'] ?? $meeting->agenda,
                        'preferred_start_time' => $startTime,
                        'start_time' => $startTime,
                        'duration' => $response['duration'],
                        'settings' => $response['settings'],
                    ]);
                }
            }

            return $meeting->fresh();
        });
    }
    public function completeMeeting(ZoomMeeting $meeting): bool
    {
        if (!$meeting->canBeCompleted()) {
            throw new \Exception('Meeting cannot be completed in its current state');
        }

        return DB::transaction(function () use ($meeting) {
            return $meeting->update([
                'status' => ZoomMeetingStatus::COMPLETED,
                'completed_at' => now(),
            ]);
        });
    }

    public function startMeeting(ZoomMeeting $meeting): bool
    {
        if (!$meeting->canBeStarted()) {
            throw new \Exception('Meeting cannot be started in its current state');
        }

        return DB::transaction(function () use ($meeting) {
            return $meeting->update([
                'status' => ZoomMeetingStatus::STARTED,
                'started_at' => now(),
            ]);
        });
    }

    public function cancelMeeting(ZoomMeeting $meeting): bool
    {
        if (!$meeting->canBeCancelled()) {
            throw new \Exception('Meeting cannot be cancelled in its current state');
        }

        return DB::transaction(function () use ($meeting) {
            // Delete meeting from Zoom
            $this->zoomService->deleteMeeting($meeting->zoom_meeting_id);

            return $meeting->update([
                'status' => ZoomMeetingStatus::CANCELLED,
            ]);
        });
    }

    public function approveMeeting(ZoomMeeting $meeting, User $supervisor): bool
    {
        if ($meeting->is_approved) {
            return true;
        }

        // Check supervisor availability
        $this->checkSupervisorAvailability(
            $supervisor,
            $meeting->start_time,
            $meeting->duration
        );

        return DB::transaction(function () use ($meeting, $supervisor) {
            return $meeting->update([
                'supervisor_id' => $supervisor->id,
                'is_approved' => true,
            ]);
        });
    }

    protected function checkSupervisorAvailability(User $supervisor, Carbon $startTime, int $duration): void
    {
        $hasConflict = ZoomMeeting::query()
            ->where('supervisor_id', $supervisor->id)
            ->where('status', '!=', ZoomMeetingStatus::CANCELLED)
            ->where(function ($query) use ($startTime, $duration) {
                $endTime = $startTime->copy()->addMinutes($duration);

                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('start_time', '>=', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                        ->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) > ?', [$startTime]);
                });
            })
            ->exists();

            //TODO: enable has conflict with proper localization
            // if ($hasConflict) {
            //     throw new \Exception('Supervisor is not available at the requested time');
            // }
        }

    public function sendNotifications(ZoomMeeting $meeting): void
    {
        if ($meeting->notifications_sent_at) {
            return;
        }

        // Send notifications to relevant parties
        if ($supervisor = $meeting->supervisor) {
            $supervisor->notify(new ZoomMeetingScheduled($meeting));
        }

        $meeting->update(['notifications_sent_at' => now()]);
    }

    public function syncMeetingStatus(ZoomMeeting $meeting): void
    {
        try {
            $zoomMeetingDetails = $this->zoomService->getMeeting($meeting->zoom_meeting_id);

            // Update local meeting status based on Zoom status
            $status = match ($zoomMeetingDetails['status']) {
                'waiting' => ZoomMeetingStatus::SCHEDULED,
                'started' => ZoomMeetingStatus::STARTED,
                'ended' => ZoomMeetingStatus::COMPLETED,
                default => $meeting->status
            };

            if ($status !== $meeting->status) {
                $meeting->update([
                    'status' => $status,
                    'started_at' => $status === ZoomMeetingStatus::STARTED ? now() : $meeting->started_at,
                    'completed_at' => $status === ZoomMeetingStatus::COMPLETED ? now() : $meeting->completed_at,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync meeting status', [
                'meeting_id' => $meeting->id,
                'zoom_meeting_id' => $meeting->zoom_meeting_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function validateMeetingTime(Carbon $startTime, int $duration): void
    {
        // Ensure meeting is not in the past
        if ($startTime->isPast()) {
            throw new \Exception('Meeting cannot be scheduled in the past');
        }

        // Ensure duration is within acceptable limits (15 minutes to 24 hours)
        if ($duration < 15 || $duration > 1440) {
            throw new \Exception('Meeting duration must be between 15 minutes and 24 hours');
        }

        // Ensure meeting is not too far in the future (e.g., 60 days)
        if ($startTime->diffInDays(now()) > 60) {
            throw new \Exception('Meeting cannot be scheduled more than 60 days in advance');
        }
    }

    public function reschedule(ZoomMeeting $meeting, Carbon $newStartTime, ?int $newDuration = null): ZoomMeeting
    {
        return $this->updateMeeting($meeting, [
            'preferred_start_time' => $newStartTime,
            'duration' => $newDuration ?? $meeting->duration,
        ]);
    }
}
