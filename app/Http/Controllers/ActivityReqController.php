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
        try {

            $activityReq = ActivityReq::paginate(10);
            return new ActivityReqCollection(($activityReq), 200);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function store(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'participant_id' => 'required|exists:users,id',
                'activity_id' => 'required|exists:activities,id',
                'note' => 'nullable|string',
            ]);

            $activityreq = ActivityReq::create([
                'participant_id' => $validatedData['participant_id'],
                'activity_id' => $validatedData['activity_id'],
                'note' => $validatedData['note'] ?? null,
            ]);

            return new ActivityReqResource($activityreq);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $th->getMessage()
            ], 500);
        }


    }


    public function show($id)
    {
        return new ActivityReqResource(ActivityReq::findOrFail($id));

    }



}
