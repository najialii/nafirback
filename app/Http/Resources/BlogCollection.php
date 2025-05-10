<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'img' => $blog->img,
                    'author' => $blog->author = [
                        'img'  => $blog->author->profile_pic,
                        'id'   => $blog->author->id,
                        'name' => $blog->author->name,
                    ],
                    'title' => $blog->title,
                    'department_id' => $blog->department_id,
                    'content' => $blog->content,
                    'featured' => $blog->featured,
                    'slug' => $blog->slug,
                    'extra' => [
                        'liked_by_user' => $blog->liked_by_user,
                    ]

                ];
            }),
        ];
    }
}