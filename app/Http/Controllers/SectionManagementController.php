<?php

namespace App\Http\Controllers;

use App\Models\SectionManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SectionManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $sections = SectionManagement::orderBy('created_at', 'desc')->get();
            
            $sections = $sections->map(function($section) {
                return [
                    'id' => $section->id,
                    'section_name' => $section->section_name,
                    'class_id' => $section->class_id,
                    'class_name' => $section->class ? $section->class->class_name : 'N/A',
                    'status' => $section->status,
                    'created_at' => $section->created_at,
                    'updated_at' => $section->updated_at,
                ];
            });

            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error('Error fetching sections: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch sections',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'class_id' => 'required|exists:class_management,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $section = SectionManagement::create([
                'section_name' => $request->section_name,
                'class_id' => $request->class_id,
                'status' => $request->status,
            ]);

            $section->load('class');

            return response()->json([
                'message' => 'Section created successfully',
                'data' => $section
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating section: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create section',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
                'class_id' => 'required|exists:class_management,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $section = SectionManagement::findOrFail($id);
            
            $section->update([
                'section_name' => $request->section_name,
                'status' => $request->status,
                'class_id' => $request->class_id,
            ]);

            $section->load('class');

            return response()->json([
                'message' => 'Section updated successfully',
                'data' => $section
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Section not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating section: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update section',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $section = SectionManagement::findOrFail($id);
            $section->delete();

            return response()->json([
                'message' => 'Section deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Section not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting section: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete section',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}