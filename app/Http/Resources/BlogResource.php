<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'img' => $this->img,
            'author_id' => $this->author_id,
            'title' => $this->title,
            'department_id' => $this->department_id,
            'content' => $this->content,
            'featured' => $this->featured,
            'slug' => $this->slug,
            'extra' => [
                'likes_count' => $this->likes_count,
                'liked_by_user' => $this->liked_by_user,
            ],
        ];
    }
}