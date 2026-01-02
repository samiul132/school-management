<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DesignationController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $designation = Designation::all();
            
            return response()->json([
                'success' => true,
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Designation'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            $designation = new Designation();
            $designation->name = $request->name;
            $designation->save();

            return response()->json([
                'success' => true,
                'data' => $designation,
                'message' => 'Designation created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create designation'
            ], 500);
        }
    }

    public function show(Designation $designation): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $designation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch designation'
            ], 500);
        }
    }

    public function update(Request $request, Designation $designation): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            $designation->name = $request->name;
            $designation->save();

            return response()->json([
                'success' => true,
                'data' => $designation,
                'message' => 'Designation updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update designation'
            ], 500);
        }
    }

    public function destroy(Designation $designation): JsonResponse
    {
        try {
            $designation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Designation deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete designation'
            ], 500);
        }
    }
}
