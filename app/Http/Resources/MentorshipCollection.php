<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MentorshipCollection extends ResourceCollection
{
 /**
  * Transform the resource collection into an array.
  *
  * @return array<int|string, mixed>
  */
 public function toArray(Request $request): array
 {
  return [
   'data' => $this->collection->map(function ($mentorship) {
    return [
     'id'            => $mentorship->id,
     'name'          => $mentorship->name,
     'mentor'        => $mentorship->mentor = [
      'img'  => $mentorship->mentor->profile_pic,
      'id'   => $mentorship->mentor->id,
      'name' => $mentorship->mentor->name,
     ],
     'department_id' => $mentorship->department_id,
     'date'          => $mentorship->date,
     'av_time'       => $mentorship->av_time,
     'created_at'    => $mentorship->created_at,
     'updated_at'    => $mentorship->updated_at,
    ];
   }),
  ];
 }
}
