<?php

namespace App\Http\Controllers;

use App\Models\ClassManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $classes = ClassManagement::orderBy('created_at', 'desc')->get();
            return response()->json($classes);
        } catch (\Exception $e) {
            Log::error('Error fetching classes: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch classes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'class_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $class = ClassManagement::create([
                'class_name' => $request->class_name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Class created successfully',
                'data' => $class
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating class: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create class',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'class_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $class = ClassManagement::findOrFail($id);
            
            $class->update([
                'class_name' => $request->class_name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Class updated successfully',
                'data' => $class
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Class not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update class',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $class = ClassManagement::findOrFail($id);
            $class->delete();

            return response()->json([
                'message' => 'Class deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Class not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting class: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete class',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}