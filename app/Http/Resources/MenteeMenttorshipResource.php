<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MenteeMenttorshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $user = Auth::user();
    
        $entryIds = $this->entries->pluck('id');
        
        $userRequest = \App\Models\MentorshipReq::whereIn('mentorship_entry_id', $entryIds)
            ->where('mentee_id', $user->id)
            // ->orderByRaw("FIELD(status, 'accepted', 'pending', 'rejected')")
            ->first();
    
        // $userStatus = $userRequest;
        $userStatus = $userRequest
    ? [
        'status' => $userRequest->status,
        'entry_id' => $userRequest->mentorship_entry_id,
        'request_id' => $userRequest->id,
        // 'message' => $userRequest->message,
    ]
    : null;
        // ? $userRequest->status : 'not_booked';
    
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mentor' => [
                'id' => $this->mentor->id,
                'name' => $this->mentor->name,
                'img' => $this->mentor->img,
            ],
            'entries' => $this->entries->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'start_date' => $entry->start_date,
                    'duration' => $entry->duration,
                    'status' => $entry->status,
                    'available' => $entry->status !== 'completed',
                ];
            }),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'description' => $this->description,
            'benefits' => $this->benefits ,
            'department_id' => $this->department_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
    
            'user_status' => $userStatus,
        ];
    }
}
