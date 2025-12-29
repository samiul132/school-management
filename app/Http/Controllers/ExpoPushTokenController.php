<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpoPushTokenController extends Controller
{
    public function saveToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expo_push_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            DB::table('expo_push_tokens')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'token' => $request->expo_push_token,
                ],
                [
                    'school_id' => $user->school_id,
                    'is_active' => true,
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Push token saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save push token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expo_push_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            DB::table('expo_push_tokens')
                ->where('user_id', $user->id)
                ->where('token', $request->expo_push_token)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Push token removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove push token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}