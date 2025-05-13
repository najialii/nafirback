<?php

namespace Modules\Zoom\Http\Requests\ZoomMeeting;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateZoomMeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Use policies for more granular control
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'topic' => 'sometimes|string|max:200',
            'agenda' => 'sometimes|nullable|string|max:2000',
            'preferred_start_time' => 'sometimes|date|after:now',
            'duration' => 'sometimes|integer|min:15|max:1440', // 15 minutes to 24 hours
            'settings' => 'sometimes|array',
            'settings.host_video' => 'sometimes|boolean',
            'settings.participant_video' => 'sometimes|boolean',
            'settings.join_before_host' => 'sometimes|boolean',
            'settings.mute_upon_entry' => 'sometimes|boolean',
            'settings.waiting_room' => 'sometimes|boolean',
            'settings.meeting_authentication' => 'sometimes|boolean',
            'settings.auto_recording' => 'sometimes|in:none,local,cloud',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'preferred_start_time.after' => 'Meeting start time must be in the future',
            'duration.min' => 'Meeting duration must be at least 15 minutes',
            'duration.max' => 'Meeting duration cannot exceed 24 hours',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
