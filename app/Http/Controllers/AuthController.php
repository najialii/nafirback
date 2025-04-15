<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
public function register(StoreUserRequest $request){

    $isEmailExist = User::where('email', $request->email)->exists();

    if($isEmailExist){
        return response()->json([
            'error'=>'Email alrady exists',
            'message' => 'Email already exists. Please login to your account.'
        ],400);
    }

    $user= User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'department_id' => $request->department_id,
        'phone' => $request->phone,
        'skills' => $request->skills,
        'exp_years' => $request->exp_years,
        'country' => $request->country,
    ]);

    $token = $user->createToken($request->name);

    return response()->json(['message'=> 'user registered successfully','user'=>$user , 'token' => $token->plainTextToken], 201);

}
public function login(Request $request){


    Log::info($request->all());
    //validate the user
    $request->validate([
        'email' => 'required|email|exists:users,email',
'password' => 'required',

    ]);


    $user = User::where('email', $request->email)->first();
    if (!$user ||!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'invalid credentials'], 401);
    }


    $token = $user->createToken($user->name);

    return response()->json(['user'=>$user , 'token' => $token->plainTextToken]);


}



public function logout(Request $request ){
$user= $request->user();

if(!$user){
    return response()->json(['message' => 'Unauthorizes'], 401);
}

$request->user()->tokens()->delete();


       return response()->json(['message' => 'You have been logged out'], 200);

}
}
