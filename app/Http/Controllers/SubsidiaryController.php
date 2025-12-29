<?php

namespace App\Http\Controllers;

use App\Models\Subsidiary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubsidiaryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $subsidiaries = Subsidiary::all();

            $totalCommission = $subsidiaries->sum('commission');
            
            return response()->json([
                'success' => true,
                'data' => $subsidiaries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subsidiaries'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subsidiaries,name',
            'status' => 'sometimes|in:active,inactive'
        ]);

        try {
            $subsidiary = new Subsidiary();
            $subsidiary->name = $request->name;
            $subsidiary->status = $request->status ?? 'active';
            $subsidiary->save();

            return response()->json([
                'success' => true,
                'data' => $subsidiary,
                'message' => 'Subsidiary created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subsidiary'
            ], 500);
        }
    }

    public function show(Subsidiary $subsidiary): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $subsidiary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subsidiary'
            ], 500);
        }
    }

    public function update(Request $request, Subsidiary $subsidiary): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subsidiaries,name,' . $subsidiary->id,
            'status' => 'sometimes|in:active,inactive'
        ]);

        try {
            $subsidiary->name = $request->name;
            if ($request->has('status')) {
                $subsidiary->status = $request->status;
            }
            $subsidiary->save();

            return response()->json([
                'success' => true,
                'data' => $subsidiary,
                'message' => 'Subsidiary updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subsidiary'
            ], 500);
        }
    }

    public function destroy(Subsidiary $subsidiary): JsonResponse
    {
        try {
            $subsidiary->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subsidiary deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subsidiary'
            ], 500);
        }
    }
}