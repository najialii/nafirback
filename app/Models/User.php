<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

// implements MustVerifyEmail
class User extends Authenticatable 
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    public const ROLE_MENTOR = 'mentor';
    public const ROLE_MENTEE = 'mentee';
    public const ROLE_ADMIN = 'admin';
    public const ROLES = [self::ROLE_MENTOR, self::ROLE_MENTEE, self::ROLE_ADMIN];

    protected $fillable = [
        'name',
        'email',
        'profile_pic',
        'phone',
        'title',
        'skills',
        'education',
        'experience',
        'location',
        'cv_file',
        'targeted_locations',
        'targeted_industries',
        'targeted_titles',
        'career_tasks',
        'completion_percentage',
    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'location' => 'array',
        'targeted_locations' => 'array',
        'targeted_industries' => 'array',
        'targeted_titles' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function mentorships()
    {
        return $this->hasMany(Mentorship::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'author_id');
    }

    public function profileCompletionPercentage(): int
    {
        $fillableFields = $this->fillable;
        $filled = 0;

        foreach ($fillableFields as $field) {
            if (!empty($this->{$field})) {
                $filled++;
            }
        }

        return intval(($filled / count($fillableFields)) * 100);
    }
}