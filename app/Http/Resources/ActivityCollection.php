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
                'id' => $activity->id,
                'name' => $activity->name,
                'department_id' => $activity->department_id,
                'location' => $activity->location,
                'img' => $activity->img,
                'time' => $activity->time,
                'type' => $activity->type,
                
                'extra' => [
                    'likes_count' => $activity->likes_count ?? $activity->likes->count(),
                    'liked_by_user' => $activity->liked_by_user ?? false,
                ],
            ];
        })->toArray();
    }
}
