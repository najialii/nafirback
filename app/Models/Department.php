<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Blog;

class Department extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
        'description',

    ];



    public function blogs()
    {
        return $this->hasMany(Blog::class, 'department_id');
    }
}
