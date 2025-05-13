<?php
namespace Modules\Zoom\Facades;
use Illuminate\Support\Facades\Facade;
use Modules\Zoom\Services\ZoomMeetingService;
use Modules\Zoom\Models\ZoomMeeting;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
/**
 * @method static ZoomMeeting|null createMeeting(Model $model, array $attributes, ?User $supervisor = null)
 * @method static ZoomMeeting updateMeeting(ZoomMeeting $meeting, array $attributes)
 * @method static bool completeMeeting(ZoomMeeting $meeting)
 * @method static bool startMeeting(ZoomMeeting $meeting)
 * @method static bool cancelMeeting(ZoomMeeting $meeting)
 * @method static bool approveMeeting(ZoomMeeting $meeting, User $supervisor)
 * @method static void sendNotifications(ZoomMeeting $meeting)
 * @method static void syncMeetingStatus(ZoomMeeting $meeting)
 * @method static void validateMeetingTime(Carbon $startTime, int $duration)
 * @method static ZoomMeeting reschedule(ZoomMeeting $meeting, Carbon $newStartTime, ?int $newDuration = null)
 * @see \Modules\Financial\Services\BillService
 */
 class ZoomMeetingFacade extends Facade {
    protected static function getFacadeClass() {
        return ZoomMeetingService::class;
    }
}
