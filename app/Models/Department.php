<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Department extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
        'description',

    ];

    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }

    // public function activiy()
    // {
    //     return $this->hasMany(Activity::class);
    // }
}
