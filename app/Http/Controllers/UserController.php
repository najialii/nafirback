<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Models\User;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function index()
    {
        $user = User::paginate(10);
        return  new UserCollection($user, 200);
    }

    public function show($id)
    {
        // $user = User::find($id);

        // if (!$user) {
        //     return response()->json(['message' => 'User not found'], 404);
        // }

        return new UserResource(User::findOrFail($id));

    }

    public function store(StoreUserRequest $request){
        // return new UserResource(User::Create($request->all()));


    $validatedData = $request->validated();
    $validatedData['password'] = Hash::make($validatedData['password']);
    $validatedData['isActive'] = false;
    $user = User::create($validatedData);

    return new UserResource($user);
    }



    public function approveUsers($id){
        $user = User::find($id);

        if (!$user){
            return response()->json([
                'error'=> 'User not found',
                'message'=>'User not found'
            ],404);

            $user->update([
                'isActive'=> true
            ]);

            return response()->json([
                'message'=> 'User Approved Successfully',
                'user'=> new UserResource($user)
            ],200);
        }

    }





    public function update(UpdateUserRequest $request, User $user){
        // $user->update($request->all());

        $user->update($request->validated());


        return response()->json($user, 200);

    }


}


