<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update participant profile
     */
    public function update(Request $request)
    {
        $participant = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15',
            'gender' => 'sometimes|in:male,female',
            'birthdate' => 'sometimes|date|before:today',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:15',
            'allergy_history' => 'nullable|string',
            'identity_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $participant->update($request->only([
            'name', 'phone', 'gender', 'birthdate',
            'blood_type', 'emergency_contact', 'emergency_phone',
            'allergy_history', 'identity_number'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $participant
        ]);
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $participant = $request->user();
        
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old photo if exists
        if ($participant->photo) {
            \Storage::disk('public')->delete($participant->photo);
        }

        $photoPath = $request->file('photo')->store('participants', 'public');
        $participant->update(['photo' => $photoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'data' => [
                'photo_url' => asset('storage/' . $photoPath)
            ]
        ]);
    }

    /**
     * Upload identity photo (KTP/Passport)
     */
    public function uploadIdentityPhoto(Request $request)
    {
        $participant = $request->user();
        
        $validator = Validator::make($request->all(), [
            'identity_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old identity photo if exists
        if ($participant->identity_photo) {
            \Storage::disk('public')->delete($participant->identity_photo);
        }

        $identityPhotoPath = $request->file('identity_photo')->store('identities', 'public');
        $participant->update(['identity_photo' => $identityPhotoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Identity photo uploaded successfully',
            'data' => [
                'identity_photo_url' => asset('storage/' . $identityPhotoPath)
            ]
        ]);
    }
}