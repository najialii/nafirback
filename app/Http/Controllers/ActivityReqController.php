<?php
namespace App\Http\Controllers;

use App\Http\Resources\ActivityReqCollection;
use App\Http\Resources\ActivityReqResource;
use App\Models\ActivityReq;
use Illuminate\Http\Request;

class ActivityReqController extends Controller
{
 //
 public function index()
 {
     $activityReqs = ActivityReq::with('activity')->paginate(10);
     return new ActivityReqCollection($activityReqs);
 }


public function store(Request $request)
{
    $user = auth()->user();

    try {
        $validatedData = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'note' => 'nullable|string',
        ]);

        $activityReq = ActivityReq::create([
            'participant_id' => $user->id,
            'activity_id' => $validatedData['activity_id'],
            'note' => $validatedData['note'] ?? null,
        ]);

        $activityReq->load('activity');

        return new ActivityReqResource($activityReq);

    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $th->getMessage(),
        ], 500);
    }
}
 public function show($id)
 {
    
  return new ActivityReqResource(ActivityReq::findOrFail($id));

 }

}
