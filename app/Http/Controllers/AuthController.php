<?php

namespace App\Http\Controllers;

use App\Constants\Role;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    // ✅ REGISTER
    public function register(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole(Role::CUSTOMER);
            Customer::create([
                'user_id' => $user->id,
                'phone' => '123456',
                'address' => 'address',
            ]);


            $token = $user->createToken('auth_token')->plainTextToken;
            $user->roles = $user->getRoleNames();
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message'=> $e->errors(),

            ], 422);
        }
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'message' => 'Invalid credentials!',
                ]);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->roles = $user->getRoleNames();
            return response()->json([
                'message' => 'Login successful!',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
            ], 422);
        }
    }
    


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
