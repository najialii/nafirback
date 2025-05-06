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
          
            'id' => $this->id,
            'name' => $this->name,
            'img' => $this->img,
            'description' => $this->description,
            'department_id' => $this->department_id,
            'date' => $this->date,
            'location' => $this->location,
            'time' => $this->time,
            'type' => $this->type,
            'user_id' => $this->user_id,
            'benifites' => $this->benifites,
            'extra'=>[  
                'likes_count' => $this->likes_count, 
                'liked_by_user' => $this->liked_by_user,  
                ]
        ];
    }
}
