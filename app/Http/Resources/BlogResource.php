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
            'author' => $this->author = [
                'img'  => $this->author->profile_pic,
                'id'   => $this->author->id,
                'name' => $this->author->name,
            ],
            'title' => $this->title,
            'department_id' => $this->department_id,
            'content' => $this->content,
            'featured' => $this->featured,
            'slug' => $this->slug,
            'extra' => [
                'liked_by_user' => $this->liked_by_user,
            ],
        ];
    }
}