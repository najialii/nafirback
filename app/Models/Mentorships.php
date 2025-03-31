<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentorships extends Model
{
    /** @use HasFactory<\Database\Factories\MentorshipsFactory> */
    use HasFactory;

    public function Deoartment(){
        return $this-> belongsTo(Departments::class);
    }
}
