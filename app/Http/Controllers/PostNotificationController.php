<?php

namespace App\Http\Controllers;

use App\Models\PostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostNotificationController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            $notifications = PostNotification::latest()
                ->get()
                ->map(function ($notification) use ($user) {
                    $isRead = DB::table('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_id', $notification->id)
                        ->exists();
                    
                    $notification->is_read = $isRead;
                    return $notification;
                });
            
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
                'school_id' => Auth::user()->school_id,
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
            $user = Auth::user();
            
            $isRead = DB::table('notification_reads')
                ->where('user_id', $user->id)
                ->where('notification_id', $notification->id)
                ->exists();
            
            $notification->is_read = $isRead;
            
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
            $user = Auth::user();
            
            DB::table('notification_reads')
                ->where('user_id', $user->id)
                ->where('notification_id', $id)
                ->delete();
            
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification removed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = PostNotification::findOrFail($id);
            $user = Auth::user();
            
            DB::table('notification_reads')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'notification_id' => $id,
                ],
                [
                    'read_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $notificationIds = PostNotification::pluck('id');
            
            foreach ($notificationIds as $notificationId) {
                DB::table('notification_reads')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'notification_id' => $notificationId,
                    ],
                    [
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all as read',
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