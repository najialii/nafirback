<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'img',
        'author_id',
        'title',
        'department_id',
        'content',
        'slug',
        'featured'

    ];



    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

}
