<?php

namespace Modules\Zoom\Data;

class ScheduleMeetingData
{
    public function __construct(
        public readonly string $topic,
        public readonly string $startTime,
        public readonly int $duration,
        public readonly string $agenda = '',
        public readonly array|ZoomMeetingSettingsData $settings = []
    ){}

    public function toArray(): array
    {
        return [
            'topic' => $this->topic,
            'preferred_start_time' => $this->startTime,
            'duration' => $this->duration,
            'agenda' => $this->agenda,
            'settings' => array_merge([
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => true,
                'waiting_room' => true,
                'meeting_authentication' => true,
                'auto_recording' => 'none',
            ], is_array($this->settings) ? $this->settings : $this->settings->toArray())
        ];
    }
}
