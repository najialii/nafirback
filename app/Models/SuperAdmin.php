<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'skills',
        'country',
        'exp_years',
        'phone',
        'department_id',
        'expertise',
        'education',
        'certificates',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\SuperAdminFactory::new();
    }

}


