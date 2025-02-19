<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //$user = User::where('email', $request->email)->first();
        if (User::where('email', $request->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['An user has already registered with that email!']
            ]);
        } else {
            $user = new User;
            $user->username = $request->username;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->password = Hash::make($request->password);
            //$user->save();
            return response()->json([
                'code' => 200,
                'data' => "user has been created successfully!"
            ]);
        }
    }

    public function index()
    {
        $users = User::all();
        return response()->json([
            'code' => 200,
            'data' => $users,
        ]);
    }

    public function login(Request $request)
    {


        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect!']
            ]);
        }

        $access_token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return response()->json([

            'code' => 200,
            "roles" => ['admin'],
            'ApiToken' => $access_token
        ]);
    }

    public function logout(Request $request)
    {

        // print_r($request);
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out!', 'code' => 200]);
    }

    public function get_user_info(Request $request)
    {



        $token = PersonalAccessToken::findToken($request->token);
        $user = $token->tokenable;


        return collect([


            "id" => $user->id,
            "username" => $user->username,
            "name" => $user->name,
            "email" => $user->email,
            "code" => 200,
            "roles" => ['admin']

        ]);
    }
}
