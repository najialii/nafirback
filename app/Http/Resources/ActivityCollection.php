<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActivityCollection extends ResourceCollection
{
 /**
  * Transform the resource collection into an array.
  *
  * @return array<int|string, mixed>
  */
 public function toArray(Request $request): array
 {
  return $this->collection->map(function ($activity) {
   return [
    'id'            => $activity->id,
    'name'          => $activity->name,
    'department_id' => $activity->department_id,
    'location'      => $activity->location,
    'img'           => $activity->img,
    'description' => $activity->description,
    'presentors'    => $activity->instructors->map(function ($instructor) {
     return [
      'id'   => $instructor->user->id,
      'name' => $instructor->user->name,
      'img'  => $instructor->user->profile_pic,
     ];
    }),
    'user'  => $activity->user = [
     'img'  => $activity->user->profile_pic,
     'id'   => $activity->user->id,
     'name' => $activity->user->name,
    ],
    'time'          => $activity->time,
    'type'          => $activity->type,
    'extra' => [
        'liked_by_user' => $activity->liked_by_user,
    ],
   ];
  })->toArray();
 }
}
