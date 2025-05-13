<?php

namespace Modules\Zoom\Console;

use Modules\Zoom\Enums\ZoomMeetingStatus;
use  Modules\Zoom\Models\ZoomMeeting;
use Modules\Zoom\Services\ZoomMeetingService;
use Illuminate\Console\Command;

class SyncZoomMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:sync-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync status of all active Zoom meetings';

    /**
     * Execute the console command.
     */
    public function handle(ZoomMeetingService $zoomService)
    {
        $meetings = ZoomMeeting::query()
            ->whereIn('status', [
                ZoomMeetingStatus::SCHEDULED,
                ZoomMeetingStatus::STARTED,
            ])
            ->get();

        foreach ($meetings as $meeting) {
            try {
                $zoomService->syncMeetingStatus($meeting);
            } catch (\Exception $e) {
                $this->error("Failed to sync meeting {$meeting->id}: {$e->getMessage()}");
            }
        }

        $this->info('Meeting sync completed');
    }
}
