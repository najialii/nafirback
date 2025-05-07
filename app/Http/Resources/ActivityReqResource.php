<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityReqResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'participant_id' => $this->participant_id,
            'activity_id' => $this->activity_id,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'activity' => [
                'passcode' => $this->activity->passcode,
                'instructions' => $this->activity->instructions,
                'link' => $this->activity->link,
            ],
        ];
    }
}