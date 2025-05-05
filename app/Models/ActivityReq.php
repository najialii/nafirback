<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivitiesParticpant;
use App\Models\User;
class ActivityReq extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'participant_id',

        'note'
    ];


    public function aciivity(){
        return $this->belongsTo(Activity::class, 'activity_id');
    }


    public function participants()
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_req_id');
    }
}


