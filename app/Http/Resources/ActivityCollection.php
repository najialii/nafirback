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
                'id' => $this->id,
                'name' => $this->name,
                'department_id' => $this->department_id,
                'location' => $this->location,
                'img' => $this->img,
                'time' => $this->time,
                'type' => $this->type,
                'extra' => [

                    'likes_count' => $this->likes_count,
                    'liked_by_user' => $this->liked_by_user,
                ]
                // 'liked_user_ids' => $activity->likes->pluck('user_id'), // IDs of users who liked the activity
            ];
        })->toArray();
    }
}