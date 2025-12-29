<?php

namespace App\Http\Controllers;

use App\Models\ShiftManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShiftManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $shifts = ShiftManagement::orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($shifts);
        } catch (\Exception $e) {
            Log::error('Error fetching shifts: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch shifts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $existing = ShiftManagement::where('shift_name', $request->shift_name)
                ->exists();
                
            if ($existing) {
                return response()->json([
                    'error' => 'Shift name already exists'
                ], 422);
            }

            if (strtotime($request->end_time) <= strtotime($request->start_time)) {
                return response()->json([
                    'error' => 'End time must be after start time'
                ], 422);
            }

            $shift = ShiftManagement::create([
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Shift created successfully',
                'data' => $shift
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating shift: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create shift',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required',
                'end_time' => 'required',
                'status' => 'required|in:active,inactive',
            ]);

            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $request->start_time)) {
                $validator->errors()->add('start_time', 'The start time field must match the format H:i (e.g., 09:00, 14:30)');
            }
            
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $request->end_time)) {
                $validator->errors()->add('end_time', 'The end time field must match the format H:i (e.g., 17:00, 18:30)');
            }

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $shift = ShiftManagement::findOrFail($id);

            $existing = ShiftManagement::where('shift_name', $request->shift_name)
                ->where('id', '!=', $id)
                ->exists();
                
            if ($existing) {
                return response()->json([
                    'error' => 'Shift name already exists'
                ], 422);
            }

            if (strtotime($request->end_time) <= strtotime($request->start_time)) {
                return response()->json([
                    'error' => 'End time must be after start time'
                ], 422);
            }

            $shift->update([
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Shift updated successfully',
                'data' => $shift
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Shift not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating shift: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update shift',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $shift = ShiftManagement::findOrFail($id);
            
            $shift->delete();

            return response()->json([
                'message' => 'Shift deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Shift not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting shift: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete shift',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}