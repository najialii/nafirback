<?php
namespace Modules\Zoom\Data;

use Modules\Zoom\Enums\ZoomMeetingType;

class MeetingData {
    public function __construct(
        public readonly string $topic,
        public readonly string $startTime,
        public readonly int $duration,
        public readonly string $agenda,
        public readonly ?ZoomMeetingType $type
    ) {}

    public function toArray(): array {
        return [
            'topic' => $this->topic,
            'start_time' => $this->startTime,
            'duration' => $this->duration,
            'agenda' => $this->agenda,
            'type' => $this->type?->value ?? ZoomMeetingType::SCHEDULE->value
        ];
    }
}
