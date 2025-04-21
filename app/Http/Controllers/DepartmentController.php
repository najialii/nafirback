<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDepartmentsRequest;
use App\Models\Department;
use App\Http\Resources\DepartmentCollection;
use App\Http\Resources\DepartmentResource;

class DepartmentController
{



    public function index()
    {

        try {
            $department = Department::paginate(10);
            return  new DepartmentCollection(($department), 200);


        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);      
          }

    }

    public function show($id)
    {
        // $user = User::find($id);

        // if (!$user) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

    return new DepartmentResource(User::findOrFail($id));

}


    public function store(StoreDepartmentsRequest $request)
    {
        try {

            $validatedData = $request->validated();
            $department = Department::create($validatedData);
            return new DepartmentResource($department);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
