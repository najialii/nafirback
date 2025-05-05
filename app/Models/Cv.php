<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $fillable = [
        'fname',
        'lname',
        'img',
        'email',
        'phone',
        'activity_id',
        'skills',
        'exp_years',
        'country',
        'expertise',
        'education',
        'certificates',
        'linkden_profile'
];

protected $casts = [
    'skills'       => 'array',
    'expertise'    => 'array',
    'education'    => 'array',
    'certificates' => 'array',
];  
}
