<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\User;
use App\Models\MentorshipReq;
use App\Models\MentorshipEntry;

class Mentorship extends Model
{ 
    /** @use HasFactory<\Database\Factories\MentorshipFactory> */
    use HasFactory;


    protected $fillable = [
        'name',
        'img',
        'description',
        'mentor_id',
        'department_id',
        'benefits',
        'start_Date',
        'end_date'
        //strDate start_date
        //endDate end_Date 
        
    ];


    // public function dpeartmnet(){
    //     return $this-> belongsTo(Department::class);
    // }


    protected $casts = [
        'benefits' => 'array', 
    ];
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function entries()
    {
        return $this->hasMany(MentorshipEntry::class);
    }

}
