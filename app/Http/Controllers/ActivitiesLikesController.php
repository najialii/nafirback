<?php
namespace App\Http\Controllers;

use App\Models\ActivitiesLikes;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitiesLikesController extends Controller
{
    public function toggle(Request $request, $activityId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'newStatus' => 'required|boolean',
        ]);

        $activity = Activity::findOrFail($activityId);

        $like = ActivitiesLikes::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();

        if ($request->newStatus) {
            if (!$like) {
                ActivitiesLikes::create([
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                ]);
            }
        } else {
            if ($like) {
                $like->delete();
            }
        }

        return response()->json([
            'liked' => $request->newStatus,
        ]);
    }
}