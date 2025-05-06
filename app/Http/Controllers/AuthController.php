<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {

        $isEmailExist = User::where('email', $request->email)->exists();

        if ($isEmailExist) {
            return response()->json([
                'error' => 'Email alrady exists',
                'message' => 'Email already exists. Please login to your account.'
            ], 400);
        }

        $role = $request->role;


        if (!in_array($role, ['mentor', 'mentee'])) {
            return response([
                'error' => 'invalid role',
                'message' => 'invalid role, role must either be "mentor","mentee"',
            ]);
        }

        $imgPath = null;

        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $imgName = time() . '_' . $img->getClientOriginalName();
            $imgPath = $img->storeAs('users/profile_imgs', $imgName, 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'role' => $request->role,
            'department_id' => $request->department_id,
            'skills' => $request->skills,
            'phone' => $request->phone,
            'exp_years' => $request->exp_years,
            'country' => $request->country,
            // 'role' => 'required|in:mentor,mentees',
            'profile_pic' => $imgPath,
            'isActive' => $role === 'mentor' ? false : true,
        ]);

        $token = $user->createToken($request->name);

        $user->assignRole($role);

        return response()->json(['message' => 'user registered successfully', 'user' => $user, 'token' => $token->plainTextToken], 201);

    }
    public function login(Request $request)
    {



        Log::info($request->all());
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',

        ]);


        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'invalid credentials'], 401);
        }



        $token = $user->createToken($user->name);

        return response()->json(['user' => $user, 'token' => $token->plainTextToken]);


    }
    /* 
    public function sauth(Request $request)
    {
    try {
        $request->validate([
            //provider example(google, fb wa keda )
            'provider' => 'required|string|in:google,linkedin',
            'googele_token'=> 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'profile_pic'=>'nullable|url',
        ]);
    //send the provider with the request
    //validate providers with the actual service provider with a switch
    // $user  = Socialite::driver('google')->userFromToken($token);

    $payload = null;

    switch ($request->provider) {
        case 'google':
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->googele_token);
            break;
        case 'linkedin':
            // Handle ver
    //othercases too
        }


        // $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
        // $payload = $client->verifyIdToken($request->googele_token);



        if(!$payload){
            return response()->json(['message' => 'Invalid token'], 401);
        }


        $user= User::where('email', $payload['email'])->first();

        // $user = User::where('email', $request->email)->first();



        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(uniqid()),
                'isActive' => true,
                'profile_pic' => $request->image,
                // 'googele_token'=> 'required|string',
                ]);

                switch ($request->provider) {
                    case 'google':
                        $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
                        $payload = $client->verifyIdToken($request->googele_token);
                        break;
                    case 'linkedin':
                    // Handle ver
    //othercases too
                }


                // $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
                // $payload = $client->verifyIdToken($request->googele_token);



                if (!$payload) {
                    return response()->json(['message' => 'Invalid token'], 401);
                }


                $user = User::where('email', $payload['email'])->first();

                // $user = User::where('email', $request->email)->first();



                if (!$user) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make(uniqid()),
                        'isActive' => true,
                        'profile_pic' => $request->image
                    ]);

                }

                $token = $user->createToken($user->name)->plainTextToken;

                return response()->json([
                    'message' => 'user was successfully authenticated',
                    'user' => $user,
                    'token' => $token,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Token verification failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        }


        public function logout(Request $request)
        {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorizes'], 401);
            }

            $request->user()->tokens()->delete();


            return response()->json(['message' => 'You have been logged out'], 200);

        }
        public function getMeData(Request $request)
        {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }


           return response()->json(['message' => 'You have been logged out'], 200);

    }
    }
     */
}