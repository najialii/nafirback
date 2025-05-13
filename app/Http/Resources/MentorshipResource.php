<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorshipResource extends JsonResource
{
 /**
  * Transform the resource into an array.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return array<string, mixed>
  */
 public function toArray(Request $request): array
 {
  return [
   'id'            => $this->id,
   'name'          => $this->name,
   'start_date'    => $this->start_date,
   'end_date'      => $this->end_date,

   'mentor'        => new BriefUserResource($this->whenLoaded('mentor')),
   'department_id' => $this->department_id,
   'benefits'          => $this->benefits,
   'created_at'    => $this->created_at,
   'updated_at'    => $this->updated_at,
   'entries'       => MentorshipEntryResource::collection($this->whenLoaded('mentorship_entry')),
  ];
 }
}
