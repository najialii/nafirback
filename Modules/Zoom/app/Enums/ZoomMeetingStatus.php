<?php
namespace Modules\Zoom\Enums;

enum ZoomMeetingStatus: string
{
    case SCHEDULED = 'scheduled';
    case STARTED = 'started';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'Scheduled',
            self::STARTED => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SCHEDULED => 'blue',
            self::STARTED => 'green',
            self::COMPLETED => 'gray',
            self::CANCELLED => 'red',
        };
    }
}
