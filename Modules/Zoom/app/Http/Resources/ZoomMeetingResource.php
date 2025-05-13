<?php

namespace Modules\Zoom\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoomMeetingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'zoomMeeting',
            'attributes' => [
                'id' => $this->id,
                'topic' => $this->topic,
                'agenda' => $this->agenda,
                'preferredStartTime' => $this->preferred_start_time->format('c'),
                'startTime' => $this->start_time?->format('c'),
                'duration' => $this->duration,
                'joinUrl' => $this->join_url,
                'startUrl' => $this->when($this->canBeStarted(), $this->start_url, null),
                'password' => $this->password,
                'status' => $this->status,
                'isApproved' => $this->is_approved,
                'startedAt' => $this->started_at?->format('c'),
                'completedAt' => $this->completed_at?->format('c'),
                'timestamps' => [
                    'created' => $this->created_at?->format('c'),
                    'modified' => $this->updated_at?->format('c'),
                    'deleted' => $this->deleted_at?->format('c'),
                ]
            ],
            'relationships' => [
                'supervisor' => $this->whenLoaded('supervisor', fn() => new UserResource($this->supervisor), null),
            ],
            'meta' => [
                'canBeStarted' => $this->canBeStarted(),
                'canBeCompleted' => $this->canBeCompleted(),
                'canBeCancelled' => $this->canBeCancelled(),
            ]
        ];
    }
}
