<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request){
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email'=>'required|email|unique:users,email',
                'password'=>'required',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'errors'=>$validateUser->errors()->all()
            ],401);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
        ]);
        return response()->json([
            'status'=>false,
            'message'=>'User Created Successfully',
            'user'=>$user,        
        ],200);
    }
    public function login(Request $request){
        $validateUser = Validator::make(
            $request->all(),[
                'email'=>'required|email',
                'password'=>'required',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Authentication Fails',
                'errors'=>$validateUser->errors()->all()
            ],404);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password ])){
            $authUser = Auth::user();
            return response()->json([
                'status'=>true,
                'message'=>'User loggedin successfully',
                'token'=> $authUser -> createToken("API Token")-> plainTextToken,
                // error is shoing on crateToken but it is not any error
                'token_type'=>'bearer',
            ],200);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Email Or Password does not matched.',
            ],401);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        $user = tokens()->delete();
        return response()->json([
            'status'=>true,
            'user'=>$user,
            'message'=>'Yourlogged out successfully',
        ],200);
    }
}
