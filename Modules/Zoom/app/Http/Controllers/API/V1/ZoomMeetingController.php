<?php

namespace Modules\Zoom\Http\Controllers\API\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Zoom\Http\Requests\ZoomMeeting\CreateZoomMeetingRequest;
use Modules\Zoom\Http\Requests\ZoomMeeting\UpdateZoomMeetingRequest;
use Modules\Zoom\Http\Resources\Zoom\ZoomMeetingResource;
use Modules\Zoom\Models\ZoomMeeting;
use Modules\Zoom\Services\ZoomMeetingService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ZoomMeetingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected readonly ZoomMeetingService $zoomMeetingService
    ) {}

    /**
     * Display a listing of zoom meetings.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ZoomMeeting::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        if ($request->has('model_type') && $request->has('model_id')) {
            $query->where('model_type', $request->model_type)
                  ->where('model_id', $request->model_id);
        }

        // Date range filter
        if ($request->has('start_date')) {
            $query->where('start_time', '>=', Carbon::parse($request->start_date));
        }

        if ($request->has('end_date')) {
            $query->where('start_time', '<=', Carbon::parse($request->end_date));
        }

        // Load relationships
        $query->with(['supervisor']);

        // Sort
        $sortBy = $request->input('sort_by', 'start_time');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate
        $perPage = $request->input('per_page', 15);
        $meetings = $query->paginate($perPage);

        return ZoomMeetingResource::collection($meetings);
    }

    /**
     * Store a newly created zoom meeting.
     */
    public function store(CreateZoomMeetingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            // Get model to attach meeting to
            $model = $this->getModelFromRequest($request);

            // Get supervisor if provided
            $supervisor = null;
            if ($request->has('supervisor_id')) {
                $supervisor = User::findOrFail($request->supervisor_id);
            }

            // Validate meeting time
            $startTime = Carbon::parse($validated['preferred_start_time']);
            $duration = $validated['duration'];
            $this->zoomMeetingService->validateMeetingTime($startTime, $duration);

            // Create meeting
            $meeting = $this->zoomMeetingService->createMeeting($model, $validated, $supervisor);

            if (!$meeting) {
                return response()->json([
                    'message' => 'Failed to create Zoom meeting',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Send notifications if auto-approve is enabled
            if ($request->boolean('auto_approve', false) && $supervisor) {
                $this->zoomMeetingService->approveMeeting($meeting, $supervisor);
                $this->zoomMeetingService->sendNotifications($meeting);
            }

            return (new ZoomMeetingResource($meeting))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified zoom meeting.
     */
    public function show(ZoomMeeting $zoomMeeting): ZoomMeetingResource
    {
        $zoomMeeting->load(['supervisor']);

        // Optionally sync meeting status with Zoom
        $this->zoomMeetingService->syncMeetingStatus($zoomMeeting);

        return new ZoomMeetingResource($zoomMeeting);
    }

    /**
     * Update the specified zoom meeting.
     */
    public function update(UpdateZoomMeetingRequest $request, ZoomMeeting $zoomMeeting)
    {
        $validated = $request->validated();

        try {
            // Check if meeting can be updated
            if ($zoomMeeting->isCompleted() || $zoomMeeting->isCancelled()) {
                return response()->json([
                    'message' => 'Cannot update completed or cancelled meetings',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate meeting time if being updated
            if (isset($validated['preferred_start_time'])) {
                $startTime = Carbon::parse($validated['preferred_start_time']);
                $duration = $validated['duration'] ?? $zoomMeeting->duration;
                $this->zoomMeetingService->validateMeetingTime($startTime, $duration);
            }

            $meeting = $this->zoomMeetingService->updateMeeting($zoomMeeting, $validated);

            return new ZoomMeetingResource($meeting);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified zoom meeting.
     */
    public function destroy(ZoomMeeting $zoomMeeting): JsonResponse
    {
        try {
            if (!$zoomMeeting->canBeCancelled()) {
                return response()->json([
                    'message' => 'Meeting cannot be cancelled in its current state',
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->zoomMeetingService->cancelMeeting($zoomMeeting);

            if (!$result) {
                return response()->json([
                    'message' => 'Failed to cancel Zoom meeting',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Start a zoom meeting.
     */
    public function start(ZoomMeeting $zoomMeeting): JsonResponse
    {
        try {
            if (!$zoomMeeting->canBeStarted()) {
                return response()->json([
                    'message' => 'Meeting cannot be started in its current state',
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->zoomMeetingService->startMeeting($zoomMeeting);

            if (!$result) {
                return response()->json([
                    'message' => 'Failed to start Zoom meeting',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'message' => 'Meeting started successfully',
                'data' => new ZoomMeetingResource($zoomMeeting->fresh()),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Complete a zoom meeting.
     */
    public function complete(ZoomMeeting $zoomMeeting): JsonResponse
    {
        try {
            if (!$zoomMeeting->canBeCompleted()) {
                return response()->json([
                    'message' => 'Meeting cannot be completed in its current state',
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->zoomMeetingService->completeMeeting($zoomMeeting);

            if (!$result) {
                return response()->json([
                    'message' => 'Failed to complete Zoom meeting',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'message' => 'Meeting completed successfully',
                'data' => new ZoomMeetingResource($zoomMeeting->fresh()),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to complete Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Approve a zoom meeting.
     */
    public function approve(ZoomMeeting $zoomMeeting, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supervisor_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // Get supervisor (either from request or current user)
            $supervisorId = $request->input('supervisor_id');
            $supervisor = $supervisorId
                ? User::findOrFail($supervisorId)
                : Auth::user();

            if (!$supervisor) {
                return response()->json([
                    'message' => 'No supervisor found',
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($zoomMeeting->is_approved) {
                return response()->json([
                    'message' => 'Meeting is already approved',
                    'data' => new ZoomMeetingResource($zoomMeeting),
                ]);
            }

            $result = $this->zoomMeetingService->approveMeeting($zoomMeeting, $supervisor);

            if (!$result) {
                return response()->json([
                    'message' => 'Failed to approve Zoom meeting',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Send notifications
            $this->zoomMeetingService->sendNotifications($zoomMeeting->fresh());

            return response()->json([
                'message' => 'Meeting approved successfully',
                'data' => new ZoomMeetingResource($zoomMeeting->fresh()),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Reschedule a zoom meeting.
     */
    public function reschedule(Request $request, ZoomMeeting $zoomMeeting): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date',
            'duration' => 'sometimes|integer|min:15|max:1440',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (!$zoomMeeting->canBeCancelled()) {
                return response()->json([
                    'message' => 'Meeting cannot be rescheduled in its current state',
                ], Response::HTTP_BAD_REQUEST);
            }

            $startTime = Carbon::parse($request->start_time);
            $duration = $request->input('duration', $zoomMeeting->duration);

            // Validate new meeting time
            $this->zoomMeetingService->validateMeetingTime($startTime, $duration);

            $meeting = $this->zoomMeetingService->reschedule($zoomMeeting, $startTime, $duration);

            return response()->json([
                'message' => 'Meeting rescheduled successfully',
                'data' => new ZoomMeetingResource($meeting),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reschedule Zoom meeting: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get the model instance to attach the meeting to.
     */
    protected function getModelFromRequest(Request $request): Model
    {
        $modelType = $request->input('model_type');
        $modelId = $request->input('model_id');

        if (!$modelType || !$modelId) {
            throw new \Exception('Model type and ID are required');
        }

        $modelClass = '\\' . $modelType;
        if (!class_exists($modelClass)) {
            throw new \Exception('Invalid model type: ' . $modelType);
        }

        $model = $modelClass::find($modelId);
        if (!$model) {
            throw new \Exception('Model not found with ID: ' . $modelId);
        }

        return $model;
    }
}
