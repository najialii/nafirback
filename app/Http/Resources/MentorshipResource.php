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
   'mentor'        => $this->mentor = [
    'img'  => $this->mentor->profile_pic,
    'id'   => $this->mentor->id,
    'name' => $this->mentor->name,
   ],
   'department_id' => $this->department_id,
   'date'          => $this->date,
   'av_time'       => $this->av_time,
   'created_at'    => $this->created_at,
   'updated_at'    => $this->updated_at,
  ];
 }
}
