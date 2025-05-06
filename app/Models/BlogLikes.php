<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogLikes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_id',
        'liked',
    ];

    public function blog() {
        return $this->belongsTo(Blog::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
