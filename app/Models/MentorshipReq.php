<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mentorship;
class MentorshipReq extends Model
{

    use HasFactory;

    protected $fillable =[
        'mentorship_id',
        'mentor_id',
        'mentee_id',
        'selecteday',
        'selectedtime',
        'message',
        'status'
    ];


    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }


        public function mentorship(){
            return $this->belongsTo(Mentorship::class);
        }


}

