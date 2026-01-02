<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use Illuminate\Http\Request;

class AccountHeadController extends Controller
{
    public function index()
    {
        try {
            $accountHeads = AccountHead::latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => $accountHeads
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch account heads'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'head_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $accountHead = new AccountHead();
            $accountHead->head_name = $request->head_name;
            $accountHead->description = $request->description;
            $accountHead->status = $request->status;
            $accountHead->save();

            return response()->json([
                'success' => true,
                'message' => 'Account head created successfully',
                'data' => $accountHead
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account head'
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $accountHead = AccountHead::find($id);

            if (!$accountHead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account head not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $accountHead
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch account head'
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $accountHead = AccountHead::find($id);

        if (!$accountHead) {
            return response()->json([
                'success' => false,
                'message' => 'Account head not found'
            ], 404);
        }

        $request->validate([
            'head_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $accountHead->head_name = $request->head_name;
            $accountHead->description = $request->description;
            $accountHead->status = $request->status;
            $accountHead->save();

            return response()->json([
                'success' => true,
                'message' => 'Account head updated successfully',
                'data' => $accountHead
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update account head'
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $accountHead = AccountHead::find($id);

        if (!$accountHead) {
            return response()->json([
                'success' => false,
                'message' => 'Account head not found'
            ], 404);
        }

        try {
            $accountHead->delete();

            return response()->json([
                'success' => true,
                'message' => 'Account head deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account head'
            ], 500);
        }
    }
    public function getDropdown()
    {
        try {
            $accountHeads = AccountHead::where('status', 'active')
                ->select('id', 'head_name')
                ->orderBy('head_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $accountHeads
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch account heads'
            ], 500);
        }
    }
}