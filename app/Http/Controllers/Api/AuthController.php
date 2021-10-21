<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    //
    public function registration(Request $request){
	
        $request->validate([
        'name'=>'required|string',
        'email'=>'required|string|email|unique:users',
        'password'=>'required|string|confirmed'
    
        ]);
    
        $user = new User([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>bcrypt($request->password)
    
        ]);
    
    
        $user->save();
    
        return response()->json([
        "message"=> "User has been registered successfully"
        ],201);
    
    }
    

    public function login(Request $request){
	
        $request -> validate([
    
        'email'=>'required|string',
        'password'=>'required|string'
    
        ]);
    
        $credentials = request(['email','password']);
    
        if(!Auth::attempt($credentials)){
    
        return response()->json([
    
        'message'=>'Invalid email or password'
        ], 401);
    }
    
        $user = $request->user();
    
        $token = $user-> createToken('Access Token');
    
        $user->access_token = $token->accessToken;
    
        return response()->json([
        "user"=>$user],200);
    
    }

    public function logout(Request $request){
        $token = $request -> user()->token();
        $token -> revoke();
        $response = ["message"=>"You have successfully logged out"];
        return response($response,200);
    }
}
