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

        $hashId = $request->hash_id;
        
        // Cari participant berdasarkan hash_id
        $participant = Participant::where('hash_id', $hashId)->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Hash ID'
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
                    'participant_type' => $participant->participant_type
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Register new participant (default: non_member)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date|before:today',
            'participant_type' => 'sometimes|in:member,non_member', // Optional, default non_member
            'blood_type' => 'nullable|in:A,B,AB,O',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:15',
            'allergy_history' => 'nullable|string',
            'identity_number' => 'nullable|string|max:50',
            'identity_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Default participant type = non_member
        $participantType = $request->participant_type ?? 'non_member';
        
        $data = [
            'participant_type' => $participantType,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'blood_type' => $request->blood_type,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'allergy_history' => $request->allergy_history,
            'identity_number' => $request->identity_number,
            'status' => 'active'
        ];

        // Handle identity photo upload
        if ($request->hasFile('identity_photo')) {
            $identityPhotoPath = $request->file('identity_photo')->store('identities', 'public');
            $data['identity_photo'] = $identityPhotoPath;
        }

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('participants', 'public');
            $data['photo'] = $photoPath;
        }

        $participant = Participant::create($data);
        
        // Non-member tetap hash_id = 0, member auto-generate
        if ($participantType === 'non_member') {
            $participant->hash_id = '0000';
            $participant->save();
        }
        
        $token = $participant->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'participant' => [
                    'id' => $participant->id,
                    'hash_id' => $participant->hash_id,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'participant_type' => $participant->participant_type
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Upgrade non-member to member
     */
    public function upgradeToMember(Request $request)
    {
        $participant = $request->user();
        
        if ($participant->participant_type === 'member') {
            return response()->json([
                'success' => false,
                'message' => 'Already a member'
            ], 400);
        }
        
        // Generate new hash_id for member
        $newHashId = Participant::generateHashId();
        
        $participant->update([
            'participant_type' => 'member',
            'hash_id' => $newHashId
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully upgraded to member',
            'data' => [
                'hash_id' => $newHashId,
                'participant_type' => 'member'
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