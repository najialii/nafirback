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
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'profile_pic' => $this->profile_pic,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'departmnet_id' => $this->department_id,
            'role' => $this->role,
            'skills' => $this->skills,
            'country' => $this->country,
            'expertise' => $this->expertise,
            'education' => $this->education,
            'certificates' => $this->certificates,
            'completionPercentage' => $this->profileComplePercentage(),
            'isActive' => $this->isActive,
        ];
    }
}
