<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mentorship;
class MentorshipReq extends Model
{

    use HasFactory;
    protected $fillable = [
        'mentorship_entry_id', 
        'mentee_id',
        'message',
        'status' 
    ];
    
    // public function mentorship()
    // {
    //     return $this->belongsTo(Mentorship::class);
    // }
    
    public function mentorship_entry()
    {
        return $this->belongsTo(MentorshipEntry::class, 'mentorship_entry_id');
    }
    
public function mentee()
{
    return $this->belongsTo(User::class,'mentee_id');
}


}

