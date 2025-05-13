<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', // timestamp
        'duration', // unsigned integer - duration in minutes
        //'mentor_id',
        'mentorship_id',
        // 'booked_by', // this refers to the user who was ACCEPTED
        // but the user was only accepted because they made a REQUEST
        // we can instead reference the request and not the user.
        'accepted_request_id',
        'link',
        'status',
    ];


    
    // public function mentor()
    // {
    //     return $this->belongsTo(User::class, 'mentor_id');
    // }

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function request()
    {
        return $this->hasOne(MentorshipReq::class, 'accepted_request_id');
    }
    // public function Booked_by()
    // {
    //     return $this->belongsTo(User::class,'mentee_id');
    // }
}


// mentee can book ono session 
// mentee CANNOT book a session if there is a mentorship entry (with status = completed) in the last 30 days
// that references a request that they made 

// at most 5 REQUESTS per one mentorship entry
