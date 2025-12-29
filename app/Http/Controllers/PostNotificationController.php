<?php

namespace App\Http\Controllers;

use App\Models\PostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostNotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = PostNotification::latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $postNotification = PostNotification::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $this->sendPushNotification(
                $postNotification->name,
                $postNotification->description ?? 'New notification posted'
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification posted successfully and sent to mobile apps',
                'data' => $postNotification,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $notification = PostNotification::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $notification = PostNotification::findOrFail($id);
            
            $notification->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification updated successfully',
                'data' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = PostNotification::findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function sendPushNotification($title, $body)
    {
        try {
            $tokens = DB::table('expo_push_tokens')
                ->where('is_active', true)
                ->pluck('token')
                ->toArray();

            if (empty($tokens)) {
                \Log::info('No push tokens found');
                return;
            }

            $response = Http::post('https://exp.host/--/api/v2/push/send', [
                'to' => $tokens, 
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => [
                    'type' => 'post_notification',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            \Log::info('Push notification sent', [
                'title' => $title,
                'tokens_count' => count($tokens),
                'response' => $response->json(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send push notification: ' . $e->getMessage());
        }
    }
}