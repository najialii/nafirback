<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipEntry extends Model
{
    use HasFactory, SoftDeletes;
    use \Modules\Zoom\Traits\HasZoomMeeting;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUSES = [self::STATUS_PENDING, self::STATUS_SCHEDULED, self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED];

    protected $fillable = [
        'start_at', // timestamp
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
        return $this->hasOne(MentorshipReq::class, 'id', 'accepted_request_id');
    }

    // public function Booked_by()
    // {
    //     return $this->belongsTo(User::class,'mentee_id');
    // }
}


// mentee can book ono session 
// mentee CANNOT book a session if there is a mentorship entry (with status = completed) in the last 30 days
// that references a request that they made 
// mentee can book ono session
// mentee CANNOT book a session if there is a mentorship entry (with status = completed) in the last 30 days
// that references a request that they made

// at most 5 REQUESTS per one mentorship entry
