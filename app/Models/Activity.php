<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Department;
use App\Models\Activity_instructor;
use Illuminate\Support\Facades\Log;

use App\Models\ActivitiesLikes;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [

        'name',
        'img',
        'instructors',
        'description',
        'department_id',
        'eventsSchedule',
        'date',
        'location',
        'time',
        'type',
        'user_id',
        'benifites',
        'link',
        'passcode',
        'instructions',

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

    public function instructors()
    {
        return $this->hasMany(Activity_instructor::class);
    }
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getLikedByUserAttribute()
    {
        if (!Auth::user()) {
            return false; 
        }

        $user = Auth::user();
    
        $isLiked = $this->likes()->where('user_id', $user->id)->exists();
    
        return $isLiked;
    }


}
