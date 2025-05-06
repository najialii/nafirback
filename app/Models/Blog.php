<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\BlogLikes;


class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'img',
        'author_id',
        'title',
        'department_id',
        'content',
        'slug',
        'featured'
    ];



    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }


    public function likes()
    {
        return $this->hasMany(BlogLikes::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getLikedByUserAttribute()
    {
        if (!auth()->check())
            return false;

        return $this->likes()->where('user_id', auth()->id())->exists();
    }


}
