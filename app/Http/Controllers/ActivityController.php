<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Http\Resources\ActivitesResource;
use App\Http\Resources\ActivityCollection;

class ActivityController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $department = Activity::paginate(10);
            return  new ActivitesResource(($department), 200);
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
    public function store(Request $request)
    {
        //
        try {

            $activity = Activity::paginate(10);
            return  new ActivityCollection(($activity), 200);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new ActivitesResource(Activity::findOrFail($id));


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
