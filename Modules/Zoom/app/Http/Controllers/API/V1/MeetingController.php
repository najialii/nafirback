<?php

namespace Modules\Zoom\Http\Controllers\API\V1;

use Modules\Zoom\Enums\ZoomMeetingType;
use Modules\Zoom\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    protected ZoomService $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    /**
     * Create a new Zoom meeting
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'topic' => 'required|string|max:200',
                'start_time' => 'required|date',
                'duration' => 'required|integer|min:15',
                'timezone' => 'required|string',
                'password' => 'nullable|string|min:6|max:10',
                'agenda' => 'nullable|string|max:2000',
                'settings' => 'nullable|array',
            ]);

            // Find available host for the meeting
            $userId = $this->zoomService->assignUserToMeeting([
                'start_time' => $validated['start_time'],
                'duration' => $validated['duration']
            ]);

            $meetingData = array_merge($validated, [
                'type' => ZoomMeetingType::SCHEDULE->value,
                'settings' => array_merge([
                    'join_before_host' => true,
                    'waiting_room' => true,
                    'host_video' => true,
                    'participant_video' => true,
                    'mute_upon_entry' => true,
                    'audio' => 'both'
                ], $validated['settings'] ?? [])
            ]);

            $meeting = $this->zoomService->createMeeting($userId, $meetingData);

            DB::commit();

            return response()->json([
                'message' => 'Meeting created successfully',
                'data' => $meeting
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create meeting', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to create meeting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get meeting details
     */
    public function show(string $meetingId): JsonResponse
    {
        try {
            $meeting = $this->zoomService->getMeeting($meetingId);

            return response()->json([
                'data' => $meeting
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get meeting', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to get meeting details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update meeting details
     */
    public function update(Request $request, string $meetingId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'topic' => 'nullable|string|max:200',
                'start_time' => 'nullable|date',
                'duration' => 'nullable|integer|min:15',
                'timezone' => 'nullable|string',
                'password' => 'nullable|string|min:6|max:10',
                'agenda' => 'nullable|string|max:2000',
                'settings' => 'nullable|array',
            ]);

            $meeting = $this->zoomService->updateMeeting($meetingId, $validated);

            DB::commit();

            return response()->json([
                'message' => 'Meeting updated successfully',
                'data' => $meeting
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update meeting', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to update meeting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a meeting
     */
    public function destroy(string $meetingId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $deleted = $this->zoomService->deleteMeeting($meetingId);

            if (!$deleted) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to delete meeting'
                ], 500);
            }

            DB::commit();
            return response()->json([
                'message' => 'Meeting deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete meeting', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to delete meeting',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List available users for a time slot
     */
    public function availableUsers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_time' => 'required|date',
                'duration' => 'required|integer|min:15'
            ]);

            $availableUsers = $this->zoomService->getAvailableUsers(
                Carbon::parse($validated['start_time']),
                $validated['duration']
            );

            return response()->json([
                'data' => $availableUsers
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to get available users', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to get available users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List meetings for a specific user
     */
    public function userMeetings(string $userId, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'nullable|in:scheduled,live,upcoming,previous',
                'page_size' => 'nullable|integer|min:1|max:300'
            ]);

            $meetings = $this->zoomService->getUserMeetings($userId, $validated);

            return response()->json([
                'data' => $meetings
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to get user meetings', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to get user meetings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
