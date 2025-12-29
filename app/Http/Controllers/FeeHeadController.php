<?php

namespace App\Http\Controllers;

use App\Models\FeeHead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeeHeadController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $feeHeads = FeeHead::orderByRaw('COALESCE(order_number, 999999)')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json($feeHeads);
        } catch (\Exception $e) {
            Log::error('Error fetching fee heads: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch fee heads',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'head_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer|min:1',
                'status' => 'required|in:active,inactive',
                'head_type' => 'required|in:general,transport',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $orderNumber = $request->order_number;
            if (!$orderNumber) {
                $maxOrder = FeeHead::max('order_number');
                $orderNumber = $maxOrder ? $maxOrder + 1 : 1;
            }

            $feeHead = FeeHead::create([
                'head_name' => $request->head_name,
                'order_number' => $orderNumber,
                'status' => $request->status,
                'head_type' => $request->head_type,
            ]);

            return response()->json([
                'message' => 'Fee head created successfully',
                'data' => $feeHead
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating fee head: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create fee head',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'head_name' => 'required|string|max:255',
                'order_number' => 'nullable|integer|min:1',
                'status' => 'required|in:active,inactive',
                'head_type' => 'required|in:general,transport',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $feeHead = FeeHead::findOrFail($id);
            
            $oldOrderNumber = $feeHead->order_number;
            $newOrderNumber = $request->order_number;
            
            if (!$newOrderNumber) {
                $newOrderNumber = $oldOrderNumber;
            }
            
            if ($newOrderNumber != $oldOrderNumber) {
                if ($newOrderNumber > $oldOrderNumber) {
                    FeeHead::where('order_number', '>', $oldOrderNumber)
                        ->where('order_number', '<=', $newOrderNumber)
                        ->where('id', '!=', $id)
                        ->decrement('order_number');
                } else {
                    FeeHead::where('order_number', '>=', $newOrderNumber)
                        ->where('order_number', '<', $oldOrderNumber)
                        ->where('id', '!=', $id)
                        ->increment('order_number');
                }
            }

            $feeHead->update([
                'head_name' => $request->head_name,
                'order_number' => $newOrderNumber,
                'status' => $request->status,
                'head_type' => $request->head_type,
            ]);

            return response()->json([
                'message' => 'Fee head updated successfully',
                'data' => $feeHead
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Fee head not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating fee head: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update fee head',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $feeHead = FeeHead::findOrFail($id);
            
            $deletedOrderNumber = $feeHead->order_number;
            
            $feeHead->delete();

            FeeHead::where('order_number', '>', $deletedOrderNumber)
                ->decrement('order_number');

            return response()->json([
                'message' => 'Fee head deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Fee head not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting fee head: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete fee head',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}