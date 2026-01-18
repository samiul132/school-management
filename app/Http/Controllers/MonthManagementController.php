<?php

namespace App\Http\Controllers;

use App\Models\MonthManagement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MonthManagementController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $months = MonthManagement::orderByRaw('COALESCE(order_number, 999999)')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json($months);
        } catch (\Exception $e) {
            Log::error('Error fetching months: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch months',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'month_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer|min:1',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $orderNumber = $request->order_number;
            if (!$orderNumber) {
                $maxOrder = MonthManagement::max('order_number');
                $orderNumber = $maxOrder ? $maxOrder + 1 : 1;
            }

            $month = new MonthManagement();
            $month->month_name = $request->month_name;
            $month->order_number = $request->order_number;
            $month->status = $request->status;
            $user->save();

            // $month = MonthManagement::create([
            //     'month_name' => $request->month_name,
            //     'order_number' => $orderNumber,
            //     'status' => $request->status,
            // ]);

            return response()->json([
                'message' => 'Month created successfully',
                'data' => $month
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating month: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create month',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'month_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer|min:1',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $month = MonthManagement::findOrFail($id);
            
            $oldOrderNumber = $month->order_number;
            $newOrderNumber = $request->order_number;
            
            if (!$newOrderNumber) {
                $newOrderNumber = $oldOrderNumber;
            }
            
            if ($newOrderNumber != $oldOrderNumber) {
                if ($newOrderNumber > $oldOrderNumber) {
                    MonthManagement::where('order_number', '>', $oldOrderNumber)
                        ->where('order_number', '<=', $newOrderNumber)
                        ->where('id', '!=', $id)
                        ->decrement('order_number');
                } else {
                    MonthManagement::where('order_number', '>=', $newOrderNumber)
                        ->where('order_number', '<', $oldOrderNumber)
                        ->where('id', '!=', $id)
                        ->increment('order_number');
                }
            }
            
            $month->update([
                'month_name' => $request->month_name,
                'order_number' => $newOrderNumber,
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Month updated successfully',
                'data' => $month
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Month not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating month: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update month',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $month = MonthManagement::findOrFail($id);
            
            $deletedOrderNumber = $month->order_number;
            
            $month->delete();

            MonthManagement::where('order_number', '>', $deletedOrderNumber)
                ->decrement('order_number');

            return response()->json([
                'message' => 'Month deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Month not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting month: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete month',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}