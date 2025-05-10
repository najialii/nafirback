<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
 /**
  * Transform the resource into an array.
  *
  * @return array<string, mixed>
  */
 public function toArray(Request $request): array
 {
  return [

   'id'            => $this->id,
   'name'          => $this->name,
   'img'           => $this->img,
   'description'   => $this->description,
   'department_id' => $this->department_id,
   'date'          => $this->date,
   'location'      => $this->location,
   'time'          => $this->time,
   'type'          => $this->type,
   'user'          => $this->user = [
       'img'  => $this->user->profile_pic,
    'id'   => $this->user->id,
    'name' => $this->user->name,
   ],
   'benefits'      => $this->benifites,
   'instructors'   => $this->instructors->map(function ($instructor) {
    return [
     'id'   => $instructor->user->id,
     'name' => $instructor->user->name,
     'img'  => $instructor->user->profile_pic,
    ];
   }),
   

  ];
 }
}
