<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login participant using Hash ID
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hash ID is required',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ensure hash_id is 4 digits (support input like "1" -> "0001")
        $hashId = str_pad($request->hash_id, 4, '0', STR_PAD_LEFT);
        
        $participant = Participant::where('hash_id', $hashId)->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Hash ID. Please enter 4-digit code (e.g., 0001, 0002)'
            ], 401);
        }

        if ($participant->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        // Update last login
        $participant->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        $token = $participant->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'participant' => [
                    'id' => $participant->id,
                    'hash_id' => $participant->hash_id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Register new participant
     */
   public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'status' => 'active'
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('participants', 'public');
            $data['photo'] = $photoPath;
        }

        $participant = Participant::create($data);
        $token = $participant->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'participant' => $participant,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Logout participant
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get authenticated participant profile
     */
    public function profile(Request $request)
    {
        $participant = $request->user();
        $participant->load('orders.event');

        return response()->json([
            'success' => true,
            'data' => $participant
        ]);
    }
}   