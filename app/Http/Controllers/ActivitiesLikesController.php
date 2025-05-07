<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivitiesLikes;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivitiesLikesController extends Controller
{
    public function toggle($activityId)
    {
        
        $user = Auth::user();
        $activity = Activity::findOrFail($activityId);

        $like = ActivitiesLikes::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'liked' => false,
            ]);
        } else {
            ActivitiesLikes::create([
                'user_id' => $user->id,
                'activity_id' => $activity->id,
            ]);
            return response()->json([
                'liked' => true,
            ]);
        }
    }
}
