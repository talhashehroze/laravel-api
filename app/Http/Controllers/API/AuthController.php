<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthController extends Controller
{
    
    public function signup(Request $request)
    {
        $validateuser = Validator::make(
            $request->all(),
            [
                'name'=>'required',
                'email'=>'required|email|unique:users,email',
                'password'=>'required'
            ]
        );

        if($validateuser->fails())
        {
            return response()->JSON([
                'status'=>false,
                'message'=> 'Validation Error',
                'error' => $validateuser->errors()->all()
            ],401);
        }

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password
        ]);

        return response()->JSON([
            'status'=>true,
            'message'=> 'User created successfully',
            'user' => $user
        ],200);
    }

    public function login(Request $request)
    {
        $validateuser = Validator::make(
            $request->all(),
            [
                'email'=>'required|email',
                'password'=>'required'
            ]
        );
        
        if($validateuser->fails())
        {
            return response()->JSON([
                'status'=>false,
                'message'=> 'Authentication Fail',
                'error' => $validateuser->errors()->all()
            ],404);
        }

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password]))
        {
            $authuser = Auth::user();
            return response()->JSON([
                'status'=>true,
                'message'=> 'User Logged in successfully',
                'user' => $authuser->createToken('token')->plainTextToken,
                'token_type'=> 'bearer'
            ],200);
        }
        else{
            return response()->JSON([
                'status'=>false,
                'message'=> 'Email and password do not match',
            ],401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->JSON([
            'status'=>true,
            'message'=> 'User Logged out successfully',
        ],200);
    }
}
