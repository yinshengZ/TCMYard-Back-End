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
    public function register(Request $request){
        return $request;
    }

    public function login(Request $request){
      

        $request->validate([
            'username'=>'required',
            'password'=>'required'
        ]);

        $user = User::where('username',$request->username)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'username'=>['The provided credentials are incorrect!']
            ]);
        }

        $access_token =$user->createToken($request->header('User-Agent'))->plainTextToken;

        return response()->json([
        
            'code'=>200,            
            "roles"=>['admin'],
            'ApiToken'=>$access_token]);



    }

    public function logout(Request $request){
       $request->user()->tokens()->delete();
       return response()->json(['message'=>'Logged out!','code'=>200]);
    }

    public function get_user_info(Request $request){
 
    
       
        $token = PersonalAccessToken::findToken($request->token);
        $user = $token->tokenable;

     
        return collect([

            
                "id"=> $user->id,
                "username"=> $user->username,
                "name"=> $user->name,
                "email"=> $user->email,         
                "code"=>200,
                "roles"=>['admin']
            
        ]);
    }
}
