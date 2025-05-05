<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\User;
use App\Models\MentorshipReq;

class Mentorship extends Model
{
    /** @use HasFactory<\Database\Factories\MentorshipFactory> */
    use HasFactory;


    protected $fillable = [
         'name',
         'mentor_id',
         'department_id',
         'date',
         'av_time',
        ];

        
    // public function dpeartmnet(){
    //     return $this-> belongsTo(Department::class);
    // }

    public function mentor(){
        return $this->belongsTo(User::class, 'mentor_id');
    }
    

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // public function mentorshipreqs(){
    //     return $this-> hasMany(MentorshipReq::class);
    // }

    // public function mentorshipreq(){
    //     return $this-> hasMany(MentorshipReq::class);
    // }


}
