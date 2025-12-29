<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\LeaveDetails;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        try {
            $leaves = Leave::with(['staff.designation', 'staff.subsidiary'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leaves
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leaves',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'application_date' => 'required|date',
            'leave_from_date' => 'required|date',
            'leave_to_date' => 'required|date|after_or_equal:leave_from_date',
            'reason_of_leave' => 'nullable|string',
            'status' => 'required|in:PENDING,APPROVED,DECLINED'
        ]);

        $leave = new Leave;
        $leave->staff_id = $request->staff_id;
        $leave->application_date = $request->application_date;
        $leave->leave_from_date = $request->leave_from_date;
        $leave->leave_to_date = $request->leave_to_date;
        $leave->reason_of_leave = $request->reason_of_leave;
        $leave->status = $request->status;
        $leave->save();

        $begin = new \DateTime($request->leave_from_date);
        $end = new \DateTime($request->leave_to_date);
        $total = 0;
        
        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $leaveDetails = new LeaveDetails;
            $leaveDetails->leave_id = $leave->id;
            $leaveDetails->leave_date = $i->format("Y-m-d");
            $leaveDetails->save();
            $total++;
        }

        $leave->total_leave = $total;
        $leave->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully Save!',
            'data' => $leave->load(['staff.designation', 'staff.subsidiary'])
        ], 201);
    }

    public function show($id)
    {
        try {
            $leave = Leave::with(['staff.designation', 'staff.subsidiary', 'leaveDetails'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $leave
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Leave not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'application_date' => 'required|date',
            'leave_from_date' => 'required|date',
            'leave_to_date' => 'required|date|after_or_equal:leave_from_date',
            'reason_of_leave' => 'nullable|string',
            'status' => 'required|in:PENDING,APPROVED,DECLINED'
        ]);

        $leave = Leave::findOrFail($id);
        $leave->staff_id = $request->staff_id;
        $leave->application_date = $request->application_date;
        $leave->leave_from_date = $request->leave_from_date;
        $leave->leave_to_date = $request->leave_to_date;
        $leave->reason_of_leave = $request->reason_of_leave;
        $leave->status = $request->status;
        $leave->save();

        LeaveDetails::where('leave_id', $id)->delete();

        $begin = new \DateTime($request->leave_from_date);
        $end = new \DateTime($request->leave_to_date);
        $total = 0;
        
        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $leaveDetails = new LeaveDetails;
            $leaveDetails->leave_id = $leave->id;
            $leaveDetails->leave_date = $i->format("Y-m-d");
            $leaveDetails->save();
            $total++;
        }

        $leave->total_leave = $total;
        $leave->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully Update!',
            'data' => $leave->load(['staff.designation', 'staff.subsidiary'])
        ], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $leave = Leave::findOrFail($id);
            
            LeaveDetails::where('leave_id', $id)->delete();

            $leave->delete();

            return response()->json([
                'success' => true,
                'message' => 'Leave deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStaffList()
    {
        try {
            $staffs = Staff::where('status', 'active')
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function ($staff) {
                    return [
                        'id' => $staff->id,
                        'name' => $staff->first_name . ' ' . $staff->last_name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $staffs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff list',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getByStaff($staffId)
    {
        try {
            $leaves = Leave::with(['staff.designation', 'staff.subsidiary'])
                ->where('staff_id', $staffId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leaves
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leaves',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $leaves = Leave::with(['staff.designation', 'staff.subsidiary'])
                ->whereBetween('leave_from_date', [$request->start_date, $request->end_date])
                ->orWhereBetween('leave_to_date', [$request->start_date, $request->end_date])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leaves
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch leaves',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}