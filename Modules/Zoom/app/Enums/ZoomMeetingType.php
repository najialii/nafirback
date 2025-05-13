<?php

namespace Modules\Zoom\Enums;

enum ZoomMeetingType: int
{
    case INSTANT = 1;
    case SCHEDULE = 2;
    case RECURRING = 3;
    case FIXED_RECURRING_FIXED = 8;
}
