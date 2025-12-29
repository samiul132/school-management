<?php

namespace App\Http\Controllers;

use App\Models\SessionManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SessionManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $sessions = SessionManagement::orderBy('order_number')
                ->get();
                
            return response()->json($sessions);
        } catch (\Exception $e) {
            Log::error('Error fetching sessions: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch sessions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'session_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer',
                'status' => 'required|in:active,inactive',
            ]);

            $existing = SessionManagement::where('session_name', $request->session_name)
                ->exists();
                
            if ($existing) {
                return response()->json([
                    'error' => 'Session name already exists'
                ], 422);
            }

            $orderNumber = $request->order_number;
            if (!$orderNumber) {
                $lastOrder = SessionManagement::max('order_number');
                $orderNumber = $lastOrder ? $lastOrder + 1 : 1;
            }

            $session = SessionManagement::create([
                'session_name' => $request->session_name,
                'order_number' => $orderNumber,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Session created successfully',
                'data' => $session
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating session: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create session',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'session_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer',
                'status' => 'required|in:active,inactive',
            ]);

            $session = SessionManagement::findOrFail($id);

            $existing = SessionManagement::where('session_name', $request->session_name)
                ->where('id', '!=', $id)
                ->exists();
                
            if ($existing) {
                return response()->json([
                    'error' => 'Session name already exists'
                ], 422);
            }

            $session->session_name = $request->session_name;
            $session->order_number = $request->order_number ?? $session->order_number;
            $session->status = $request->status;
            $session->save();

            return response()->json([
                'message' => 'Session updated successfully',
                'data' => $session
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Session not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating session: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update session',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $session = SessionManagement::findOrFail($id);
            
            $session->delete();

            return response()->json([
                'message' => 'Session deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Session not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting session: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete session',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}