<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_pic' => $this->profile_pic,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department_id' => $this->department_id,
            'role' => $this->roles->pluck('name'),
            'skills' => $this->skills,
            'location' => $this->location,
            'experience' => $this->experience,
            'education' => $this->education,
            'cv_file' => $this->cv_file,
            'targeted_locations' => $this->targeted_locations,
            'targeted_industries' => $this->targeted_industries,
            'targeted_titles' => $this->targeted_titles,
            'career_tasks' => $this->career_tasks,
            'completionPercentage' => $this->profileComplePercentage(),
            'isActive' => $this->is_active,
        ];
    }
}