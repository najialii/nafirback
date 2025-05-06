<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Department;
 use App\Models\Metorship;
 use App\Models\Blog;
 use App\Models\MentorshipReq;
use App\Models\Activity;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens , HasRoles;




    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile_pic',
        'email',
        'password',
        'department_id',
        'phone',
        'activity_id',
        'skills',
        'exp_years',
        'country',
        'expertise',
        'education',
        'certificates',
        'isActive',
    ];



    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function Mentorship()
    {
        return $this->hasMany(Mentorship::class);
    }

    // public function mentorshipRequests()
    // {
    //     return $this->hasMany(MentorshipReq::class, 'mentee_id');
    // }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    public function superAdmin()
{
    return $this->hasOne(SuperAdmin::class);
}


}
