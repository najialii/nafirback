<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Department;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [

        'name',
        'img',
        'description',
        'department_id',
        'location',
        'eventsSchedule',
        'date',
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
    


}
