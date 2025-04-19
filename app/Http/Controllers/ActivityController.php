<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Http\Resources\ActivitesResource;
use App\Http\Resources\ActivityCollection;
use App\Http\Requests\ActivityStoreRequest;
use App\Http\Requests\ActivityUpdateRequest;


class ActivityController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $activity = Activity::paginate(10);
            return  new ActivitesResource(($activity), 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     //

    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(ActivityStoreRequest $request)
    {
        try {
            $validatedData = $request->validated();
    
            if (isset($validatedData['eventsSchedule'])) {
                $validatedData['eventsSchedule'] = json_encode($validatedData['eventsSchedule']);
            }
    
            $activity = Activity::create($validatedData);
    
            return response()->json([
                'message' => 'Activity created successfully',
                'data' => new ActivitesResource($activity)
            ], 201);
    
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new ActivitesResource(Activity::findOrFail($id));


    }

    public function joinActivity(){
        try {
            
        } catch (\Throwable $th) {
            return response()->json([
                'error'=>'Something went wrong',
                'message'=> $th->getMessage()
            ],500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(ActivityUpdateRequest $request, string $id)
{
    try {
        $activity = Activity::findOrFail($id);
        $data = $request->validated();

        if (isset($data['eventsSchedule'])) {
            $data['eventsSchedule'] = json_encode($data['eventsSchedule']);
        }

        $activity->update($data);

        return response()->json([
            'message' => 'Activity updated successfully',
            'data' => new ActivitesResource($activity)
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Update failed',
            'message' => $th->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $activity->delete();
    
            return response()->json([
                'message' => 'Activity deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Delete failed',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
}
