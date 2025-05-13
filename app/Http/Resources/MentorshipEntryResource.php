<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorshipEntryResource extends JsonResource
{
    protected $role;

    public function __construct($resource, $role = null)
    {
        parent::__construct($resource);
        $this->role = $role;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'start_date' => $this->date,
            'duration' => $this->duration,
            'status' => $this->status,
            'mentorship_id' => $this->mentorship_id,
            'available' => $this->status !== 'booked-out',
        ];
        if ($this->role === 'mentor' && $this->accepted_request_id) {
            $data['accepted_request'] = new mentorshipReqResource($this->request);
        }
        return $data;
    }
}
