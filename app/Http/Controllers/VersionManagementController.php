<?php

namespace App\Http\Controllers;

use App\Models\VersionManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VersionManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $versions = VersionManagement::orderBy('created_at', 'desc')->get();
            return response()->json($versions);
        } catch (\Exception $e) {
            Log::error('Error fetching versions: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch versions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'version_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $version = VersionManagement::create([
                'version_name' => $request->version_name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Version created successfully',
                'data' => $version
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating version: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create version',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'version_name' => 'required|string|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $version = VersionManagement::findOrFail($id);
            
            $version->update([
                'version_name' => $request->version_name,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Version updated successfully',
                'data' => $version
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Version not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating version: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update version',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $version = VersionManagement::findOrFail($id);
            $version->delete();

            return response()->json([
                'message' => 'Version deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Version not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting version: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete version',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}