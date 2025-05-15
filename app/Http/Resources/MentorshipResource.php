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
  public function toArray($request)
  {
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
                  'accepted_request' => $entry->acceptedRequest ? [
                      'id' => $entry->acceptedRequest->id,
                      'mentee_id' => $entry->acceptedRequest->mentee_id,
                      'message' => $entry->acceptedRequest->message,
                  ] : null,
              ];
          }),
          'start_date' => $this->start_date,
          'end_date' => $this->end_date,
          'description' => $this->description,
          'benefits' => $this->benefits,

          'department_id' => $this->department_id,
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
      ];
  }
  
}
