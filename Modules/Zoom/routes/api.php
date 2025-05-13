<?php

use Illuminate\Support\Facades\Route;
use Modules\Zoom\Http\Controllers\API\V1\ZoomMeetingController;

/*
|--------------------------------------------------------------------------
| Zoom Module API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Zoom module.
|
*/

Route::middleware(['api', 'auth:passport'])->prefix('api/zoom')->group(function () {
    // Zoom Meetings
    Route::apiResource('meetings', ZoomMeetingController::class);

    // Additional meeting actions
    Route::post('meetings/{zoomMeeting}/start', [ZoomMeetingController::class, 'start'])
        ->name('zoom.meetings.start');

    Route::post('meetings/{zoomMeeting}/complete', [ZoomMeetingController::class, 'complete'])
        ->name('zoom.meetings.complete');

    Route::post('meetings/{zoomMeeting}/approve', [ZoomMeetingController::class, 'approve'])
        ->name('zoom.meetings.approve');

    Route::post('meetings/{zoomMeeting}/reschedule', [ZoomMeetingController::class, 'reschedule'])
        ->name('zoom.meetings.reschedule');
});
