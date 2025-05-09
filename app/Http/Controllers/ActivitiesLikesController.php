<?php
namespace App\Http\Controllers;

use App\Models\ActivitiesLikes;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitiesLikesController extends Controller
{
    public function fav_activity(Request $request, $activityId)
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
            if (!$like) {

        if ($request->newStatus) {
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


    
    public function getfav_activites(Request $request)
    {
        $user = Auth::user();
    
        $fav = ActivitiesLikes::where('user_id', $user->id)
            ->where('liked', true)
            ->with('activity')
            ->get()
            ->map(function ($fav) {
                if ($fav->activity) { 
                    return [
                        'id' => $fav->activity->id,
                        'name' => $fav->activity->name,
                        'img' => $fav->activity->img,
                        'description' => $fav->activity->description,
                    ];
                }
                return null; 
            })
            ->filter(); 
    
        return response()->json([
            'message' => 'Favorite activities retrieved',
            'favorite' => $fav->toArray(),
        ]);
    }
}