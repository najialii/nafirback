<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {

        $isEmailExist = User::where('email', $request->email)->exists();

        if ($isEmailExist) {
            return response()->json([
                'error' => 'Email alrady exists',
                'message' => 'Email already exists. Please login to your account.',
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
        $provider = $request->provider;

        $request->validate([
            'access_token' => 'required_if:provider,google|string',
            'email' => 'required_if:provider,credentials|email',
            'password' => 'required_if:provider,credentials|string',
            'provider' => 'required|string|in:google,credentials',
        ]);

        switch ($provider) {
            case 'google':
                $access_token = $request->access_token;
                $response = file_get_contents("https://www.googleapis.com/oauth2/v3/tokeninfo?access_token={$access_token}");
                $data = json_decode($response, true);

                if (!isset($data['aud']) || $data['aud'] !== config('services.google.client_id')) {
                    abort(401, 'Invalid token audience.');
                }
                try {
                    $authUser = Socialite::driver('google')->stateless()->userFromToken($access_token);
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => 'Token verification failed',
                        'message' => $e->getMessage(),
                    ], 500);
                }

                \Log::info('Google User:', [
                    'name' => $authUser->getName(),
                    'email' => $authUser->getEmail(),
                    'id' => $authUser->getId(),
                ]);

                $user = User::where('email', $authUser->getEmail())->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $authUser->getName(),
                        'email' => $authUser->getEmail(),
                        'password' => Hash::make('testtesttest'),
                        'is_active' => false,
                        'profile_pic' => $authUser->getAvatar() ?? null,
                    ]);
                }

                $token = $user->createToken('google-token')->plainTextToken;

                return response()->json([
                    'authToken' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'profile_pic' => $user->profile_pic,
                        'is_active' => $user->is_active,
                    ],
                ]);

            case 'credentials':
                $user = User::where('email', $request->email)->first();

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json(['message' => 'Invalid credentials'], 401);
                }

                $token = $user->createToken('credentials-token')->plainTextToken;

                return response()->json([
                    'authToken' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'is_active' => $user->is_active,
                        'profile_pic' => $user->profile_pic,
                    ],
                ]);

            default:
                return response()->json(['error' => 'Invalid provider'], 400);
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
}
