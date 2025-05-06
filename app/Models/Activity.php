<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivitiesLikes;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [

        'name',
        'img',
        'description',
        'department_id',
        'eventsSchedule',   
        'date',
        'location',
        'time',
        'type',
        'user_id',
        'benifites',

    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function activityReq()
    {
        return $this->hasMany(ActivityReq::class);
    }

    public function likes()
    {
        return $this->hasMany(ActivitiesLikes::class);
    }

// todo:get the idate
public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getLikedByUserAttribute()
    {
        if (!auth()->check()) return false;

        return $this->likes()->where('user_id', auth()->id())->exists();
    }


}
