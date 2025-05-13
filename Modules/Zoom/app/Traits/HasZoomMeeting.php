<?php

namespace Modules\Zoom\Traits;

use  Modules\Zoom\Models\ZoomMeeting;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasZoomMeeting
{    /**
     * Get zoomMeeting for the HasZoomMeeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function zoomMeeting(): MorphOne
    {
        return $this->morphOne(ZoomMeeting::class, 'model')->latest();
    }

    public function hasZoomMeeting(): bool
    {
        return $this->zoomMeeting()->exists();
    }

    public function zoomMeetingHostLink(): Attribute
    {
        return Attribute::make(
            get: function() {
                return $this->zoomMeeting()->exists() ? $this->zoomMeeting()->first()->start_url : null;
            },
        );
    }

    public function zoomMeetingJoinLink(): Attribute
    {
        return Attribute::make(
            get: function() {
                return $this->zoomMeeting()->exists() ? $this->zoomMeeting()->first()->join_url : null;
            },
        );
    }
}
