<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function userRegister(Request $request){
        // Validate the request data
        $request->validate([
            'name' =>'required|string',
            'email' =>'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'user';
        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ]);
    }

    public function userLogin(Request $request){
        // Validate the request data
        $request->validate([
            'email' =>'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user ||!Hash::check($request->password, $user->password)) {
            return response()->json([
               'status' => 'failed',
               'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
           'status' =>'success',
           'message' => 'User logged in successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
           'status' =>'success',
           'message' => 'User logged out successfully'
        ]);
    }
    #restaurant_register
    public function restaurantRegister(Request $request){
        // Validate the request data
        $request->validate([
            'name' =>'required|string',
            'email' =>'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'restaurant_name' =>'required|string',
            'restaurant_address' =>'required|string',
            'photo' =>'required|image',
            'latlong' =>'required|string',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] ='restaurant';
        $user = User::create($data);

        #check if photo uploaded successfully
        if($request->hasFile('photo')){
            $photo = $request->file('photo');
            $photoName = time(). '.'. $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photoName);
            $user->photo = $photoName;
            $user->save();
        } else {
            return response()->json([
               'status' => 'failed',
               'message' => 'Failed to upload photo'
            ], 400);
        }

        $user->latlong = $request->latlong;
        $user->save();

        return response()->json([
           'status' =>'success',
           'message' => 'Restaurant registered successfully',
            'data' => $user
        ]);

        return response()->json([
            'status' =>'success',
            'message' => 'Restaurant registered successfully',
            'data' => $user
        ]);
    }
    #driver_register
    public function driverRegister(Request $request){
        // Validate the request data
        $request->validate([
            'name' =>'required|string',
            'email' =>'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'photo' =>'required|image',
            'license_plate' =>'required|string',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'driver';
        $user = User::create($data);
        #check if photo uploaded successfully
        if($request->hasFile('photo')){
            $photo = $request->file('photo');
            $photoName = time(). '.'. $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photoName);
            $user->photo = $photoName;
            $user->save();
        } else {
            return response()->json([
               'status' => 'failed',
               'message' => 'Failed to upload photo'
            ], 400);
        }
        return response()->json([
           'status' =>'success',
           'message' => 'Driver registered successfully',
            'data' => $user
        ]);
    }
    #update latlong user
    public function updateLatlong(Request $request){
        $request->validate([
            'latlong' =>'required|string'
        ]);

        $user = $request->user();
        $user->latlong = $request->latlong;
        $user->save();
        return response()->json([
           'status' =>'success',
           'message' => 'User latlong updated successfully',
            'data' => $user
        ]);
    }

    #get all restaurants
    public function getAllRestaurants(){
        $restaurants = User::where('roles','restaurant')->get();
        return response()->json([
           'status' =>'success',
           'message' => 'All restaurants retrieved successfully',
            'data' => $restaurants
        ]);
    }
}
