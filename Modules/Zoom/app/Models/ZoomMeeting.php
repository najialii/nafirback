<?php

namespace Modules\Zoom\Models;

use Modules\Zoom\Enums\ZoomMeetingStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomMeeting extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'zoom_meetings';

    protected $fillable = [
        'supervisor_id',
        'zoom_user_id',
        'zoom_meeting_id',
        'topic',
        'agenda',
        'preferred_start_time',
        'start_time',
        'duration',
        'password',
        'join_url',
        'start_url',
        'is_approved',
        'settings',
        'status',
        'started_at',
        'completed_at',
        'notifications_sent_at'
    ];

    protected $casts = [
        'preferred_start_time' => 'datetime',
        'start_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'notifications_sent_at' => 'datetime',
        'is_approved' => 'boolean',
        'settings' => 'array',
        'status' => ZoomMeetingStatus::class
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function isScheduled(): bool
    {
        return $this->status === ZoomMeetingStatus::SCHEDULED;
    }

    public function isStarted(): bool
    {
        return $this->status === ZoomMeetingStatus::STARTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === ZoomMeetingStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === ZoomMeetingStatus::CANCELLED;
    }

    public function canBeStarted(): bool
    {
        return $this->isScheduled() && $this->is_approved;
    }

    public function canBeCompleted(): bool
    {
        return $this->isStarted();
    }

    public function canBeCancelled(): bool
    {
        return $this->isScheduled();
    }

    public function getStatusBadgeAttribute(): array
    {
        return [
            'label' => $this->status->label(),
            'color' => $this->status->color(),
        ];
    }
}
