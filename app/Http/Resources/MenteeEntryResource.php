<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenteeEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'mentorship_id' => $this->mentorship_id,
            // 'available' => $this->status !== 'booked-out',
            'mentorship' => new MentorshipResource($this->whenLoaded('mentorship')),
            'requests' => MentorshipReqResource::collection($this->whenLoaded('requests')),
        ];
    }
}
