<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Activity;

class ActivitiesLikes extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'liked',
         'user_id',
        'activity_id'];

    public function activity() {
        return $this->belongsTo(Activity::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    



}
