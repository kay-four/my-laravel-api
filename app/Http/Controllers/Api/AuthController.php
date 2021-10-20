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

    public function registration(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
            'confirm_password'=>'required|same:password',


        ]);

        if($validation->fails()){
            return response()-> json($validation->errors(),202);
        }
        $allData = $request->all();
        $allData['password']= bcrypt($allData['password']);

        $user = User::create($allData);

        $resArray = [];
        $resArray['token']=$user -> createToken('api-application')->accessToken;
        $resArray['name']=$user->name;

        return response()-> json($resArray,200);

    }

    public function login(Request $request)
    {
        if(Auth::attempt([
            'email'=>$request->email,
            'password'=>$request->password
        ])){

            $user = Auth::user();
            $resArray = [];
            $resArray['token']=$user-> createToken('api-application')->accessToken;
            $resArray['name']=$user->name;

            return response()-> json($resArray,200);

        } else{
            return response()->json(['error'=>'Unauthorized Access'],203);
        }

    }

    public function logout(Request $request){
        $token = $request -> user()->token();
        $token -> revoke();
        $response = ["message"=>"You have successfully logged out"];
        return response($response,200);
    }
}
