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
    // public function index()
    // {
    //     try {
    //         $activity = Activity::paginate(10);
    //         return  new ActivitesResource(($activity), 200);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return response()->json([
    //             'error' => 'Something went wrong!',
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }


public function index()
{
    try {
        $activities = Activity::with('likes')->paginate(10);

        return response()->json([
            'message' => 'Activities retrieved successfully',
            'data' => ActivityCollection::collection($activities),
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Something went wrong!',
            'message' => $th->getMessage(),
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


            $imgPath = null;

    if ($request->hasFile('img')) {
        $img = $request->file('img');
        $imgName = time() . '_' . $img->getClientOriginalName();
        $imgPath = $img->storeAs('activities', $imgName, 'public');
        $validatedData['img'] = $imgPath;
    }

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
    public function searchActivity($keyword) {
        $activtes = Activity::limit(10)->select(
            [
                'id',
                'name',
            ]
            );
            $activtes = $activtes->where('name', 'like', '%' . $keyword . '%')->get();
           if(!$activtes){
            return response()->json([
                'message'=> 'mentorships not found'
            ]);
           }
           return response()->json([
            'mentorships'=> $activtes,

           ],200);
    }


public function search ($keyword)
{
    $activities = Activity::limit(10)->select(
        [
            'img',
            "name",
            "id"
        ]
    )->where('name', 'like', '%' . $keyword . '%')->get();

    return response()->json([
        'activities' => $activities,
    ], 200);
}
    public function filter(Request $request)
    {
        $query = Activity::query();

        if ($request->has('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        if ($request->has('time')) {
            $query->whereTime('time', $request->input('time'));
        }

        $activities = $query->paginate(10);

        return response()->json([
            'message' => 'Activities retrieved successfully',
            'data' => ActivitesResource::collection($activities)
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new ActivitesResource(Activity::findOrFail($id));


    }


    // canceled 
    // public function joinActivity(){
    //     try {

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'error'=>'Something went wrong',
    //             'message'=> $th->getMessage()
    //         ],500);
    //     }
    // }

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


public function departmentAct($id){
    try{
        $activity = Activity::where('department_id', $id)->paginate(10);
        if (!$activity) {
            return response()->json([
                'message' => 'blogs not found'
            ]);
        }
        return new ActivityCollection($activity);
    } catch(\Throwable $th){
        return response()->json([
            'error' => 'something went wrong!',
        'message' => $th->getMessage()
        ]);
    
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
