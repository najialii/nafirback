<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityReq;

class ActivityParticipant extends Model
{
    
    
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_req_id',
    ];


    public function activityReq()
    {
        return $this->belongsTo(ActivityReq::class, 'activity_req_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
