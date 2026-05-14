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
            'hash_id' => 'required|string|exists:participants,hash_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Hash ID',
                'errors' => $validator->errors()
            ], 422);
        }

        $participant = Participant::where('hash_id', $request->hash_id)->first();

        if ($participant->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Please contact admin.'
            ], 403);
        }

        // Update last login
        $participant->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        // Generate token
        $token = $participant->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'participant' => $participant,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
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