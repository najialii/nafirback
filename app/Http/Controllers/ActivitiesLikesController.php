<?php
namespace App\Http\Controllers;

use App\Models\ActivitiesLikes;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivitiesLikesController extends Controller
{
 public function toggle($activityId)
 {

  
        try {
    $user = Auth::user();
    // if ($user === null) 
    //     return null;


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
    'user_id'     => $user->id,
    'activity_id' => $activity->id,
   ]);
   return response()->json([
    'liked' => true,
   ]);
  }
} catch (\Throwable $th) {
    return response()->json([
        'error' => 'Something went wrong!',
        'message' => $th->getMessage(),
    ], 500);
}
 }
}
