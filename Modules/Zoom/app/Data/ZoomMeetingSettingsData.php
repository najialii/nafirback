<?php

namespace Modules\Zoom\Data;
class ZoomMeetingSettingsData
{
    public function __construct(
        public readonly bool $hostVideo = true,
        public readonly bool $participantVideo = true,
        public readonly bool $joinBeforeHost = false,
        public readonly bool $muteUponEntry = true,
        public readonly bool $waitingRoom = true,
        public readonly bool $meetingAuthentication = true,
        public readonly string $autoRecording = 'none'

    ){}

    public function toArray(): array
    {
        return [
            'host_video' => $this->hostVideo,
            'participant_video' => $this->participantVideo,
            'join_before_host' => $this->joinBeforeHost,
            'mute_upon_entry' => $this->muteUponEntry,
            'waiting_room' => $this->waitingRoom,
            'meeting_authentication' => $this->meetingAuthentication,
            'auto_recording' => $this->autoRecording,
        ];
    }
}
