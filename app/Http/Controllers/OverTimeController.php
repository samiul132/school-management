<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\OverTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OverTimeController extends Controller
{
    public function index()
    {
        try {
            $overtimes = OverTime::with(['staff.designation'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $overtimes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch overtime records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'over_time_hour' => 'required|numeric|min:0.5|max:24',
        ], [
            'date.required' => 'Date is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $overtime = new OverTime;
            $overtime->staff_id       = $request->staff_id;
            $overtime->date     = $request->date;
            $overtime->over_time_hour     = $request->over_time_hour;
            $overtime->save();

            $overtime->load(['staff.designation']);

            return response()->json([
                'success' => true,
                'message' => 'Overtime record created successfully',
                'data' => $overtime
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create overtime record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $overtime = OverTime::with(['staff.designation'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $overtime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'over_time_hour' => 'required|numeric|min:0.5|max:24',
        ], [
            'date.required' => 'Date is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $overtime = OverTime::findOrFail($id);
            $overtime->staff_id       = $request->staff_id;
            $overtime->date     = $request->date;
            $overtime->over_time_hour     = $request->over_time_hour;
            $overtime->save();

            $overtime->load(['staff.designation']);

            return response()->json([
                'success' => true,
                'message' => 'Overtime record updated successfully',
                'data' => $overtime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update overtime record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $overtime = OverTime::findOrFail($id);
            $overtime->delete();

            return response()->json([
                'success' => true,
                'message' => 'Overtime record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete overtime record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}