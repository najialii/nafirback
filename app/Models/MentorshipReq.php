<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mentorship;
class MentorshipReq extends Model
{

    use HasFactory;
    protected $fillable = [
        //'mentorship_id',
        'mentorship_entry_id', 
        //'mentor_id',
        'mentee_id',
        'message',
        //'link',
        'status'
    ];
    
    // public function mentorship()
    // {
    //     return $this->belongsTo(Mentorship::class);
    // }
    
    public function mentorshipEntry()
    {
        return $this->belongsTo(MentorshipEntry::class, 'mentorship_entry_id');
    }
    
public function mentee_id()
{
    return $this->belongsTo(User::class,'user');
}

}

