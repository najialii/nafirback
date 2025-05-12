<?php

namespace App\Http\Resources;

use App\Helpers\ImageHelper;
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
            'profileImage' => $this->profile_pic ?? ImageHelper::generateImageUrl('person', ['name' => $this->name]),
            'basicInfo' => [
                'name' => $this->name,
                'email' => $this->email
            ],
            'phoneNumber' => $this->phone,
            'departmentId' => $this->department_id,
            'role' => $this->roles->pluck('name'),
            'skills' => $this->skills,
            'location' => json_encode($this->location),
            'experiences' => $this->experience,
            'educations' => $this->education,
            'cvFile' => $this->cv_file,
            'targetedLocations' => $this->targeted_locations,
            'targetedIndustries' => $this->targeted_industries,
            'targetedTitles' => $this->targeted_titles,
            'careerTasks' => $this->career_tasks,
            'completionPercentage' => $this->profileCompletionPercentage(),
            'isActive' => $this->is_active,
        ];
    }
}