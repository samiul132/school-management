<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Attendance::with(['staff.designation', 'staff.subsidiary']);

            if ($request->has('month') && $request->month != '') {
                $query->whereMonth('date', $request->month);
            }

            if ($request->has('year') && $request->year != '') {
                $query->whereYear('date', $request->year);
            }

            if ($request->has('staff_id') && $request->staff_id != '') {
                $query->where('staff_id', $request->staff_id);
            }

            if ($request->has('from_date') && $request->from_date != '') {
                $query->whereDate('date', '>=', $request->from_date);
            }

            if ($request->has('to_date') && $request->to_date != '') {
                $query->whereDate('date', '<=', $request->to_date);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $query->orderBy('date', 'desc');

            $perPage = $request->get('per_page', 15);
            $attendances = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Attendance records retrieved successfully',
                'data' => $attendances
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            $staffId = $request->get('staff_id');

            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $staffQuery = Staff::where('status', 'active')
                              ->with('designation');

            if ($staffId) {
                $staffQuery->where('id', $staffId);
            }

            $staffList = $staffQuery->get();

            $attendanceQuery = Attendance::whereBetween('date', [$startDate, $endDate]);
            
            if ($staffId) {
                $attendanceQuery->where('staff_id', $staffId);
            }

            $attendances = $attendanceQuery->get();

            $attendanceArray = [];
            $totalAttendance = [];

            foreach ($attendances as $record) {
                $attendanceArray[$record->staff_id][$record->date] = [
                    'status' => $record->status,
                    'in_time' => $record->in_time,
                    'out_time' => $record->out_time
                ];
                
                if (!isset($totalAttendance[$record->staff_id][$record->status])) {
                    $totalAttendance[$record->staff_id][$record->status] = 0;
                }
                $totalAttendance[$record->staff_id][$record->status]++;
            }

            $monthDays = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $monthDays[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }

            $staffData = [];
            foreach ($staffList as $staff) {
                $staffAttendance = [];
                foreach ($monthDays as $date) {
                    $staffAttendance[$date] = $attendanceArray[$staff->id][$date] ?? [
                        'status' => null,
                        'in_time' => null,
                        'out_time' => null
                    ];
                }

                $staffData[] = [
                    'id' => $staff->id,
                    'name' => $staff->first_name . ' ' . $staff->last_name,
                    'designation' => $staff->designation->name ?? 'N/A',
                    'photo' => $staff->photo,
                    'total_present' => $totalAttendance[$staff->id]['Present'] ?? 0,
                    'total_absent' => $totalAttendance[$staff->id]['Absent'] ?? 0,
                    'total_late' => $totalAttendance[$staff->id]['Late'] ?? 0,
                    'total_leave' => $totalAttendance[$staff->id]['Leave'] ?? 0,
                    'attendance' => $staffAttendance
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance summary retrieved successfully',
                'data' => [
                    'staff_list' => $staffData,
                    'month_days' => $monthDays,
                    'month' => $month,
                    'year' => $year
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'attendance.*.staff_id' => 'required|exists:staff,id',
                'attendance.*.status' => 'required|in:Present,Absent,Late,Leave',
                'attendance.*.in_time' => 'nullable|date_format:H:i:s,H:i',
                'attendance.*.out_time' => 'nullable|date_format:H:i:s,H:i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {

                Attendance::where('date', $request->date)->delete();

                $attendanceData = [];
                foreach ($request->attendance as $record) {
                    $inTime = isset($record['in_time']) && $record['in_time'] 
                        ? (strlen($record['in_time']) === 5 ? $record['in_time'] . ':00' : $record['in_time'])
                        : null;
                    
                    $outTime = isset($record['out_time']) && $record['out_time']
                        ? (strlen($record['out_time']) === 5 ? $record['out_time'] . ':00' : $record['out_time'])
                        : null;

                    $attendance = New Attendance;
                    $attendance->date = $request->date;
                    $attendance->staff_id = $record['staff_id'];
                    $attendance->status = $record['status'];
                    $attendance->in_time = $inTime;
                    $attendance->out_time = $outTime;
                    //dd($attendance);
                    $attendance->save();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance saved successfully',
                    'data' => [
                        'date' => $request->date,
                        'total_records' => count($attendanceData)
                    ]
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $attendance = Attendance::with(['staff.designation', 'staff.subsidiary'])->find($id);

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance record retrieved successfully',
                'data' => $attendance
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByDate($date)
    {
        try {
            $attendances = Attendance::with(['staff.designation'])
                ->where('date', $date)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Attendance records retrieved successfully',
                'data' => $attendances
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStaffList(Request $request)
    {
        try {
            $date = $request->get('date', date('Y-m-d'));

            $staffList = Staff::where('status', 'active')
                ->with(['designation', 'subsidiary'])
                ->orderBy('first_name')
                ->get();

            $existingAttendance = Attendance::where('date', $date)
                ->get()
                ->keyBy('staff_id');

            $staffData = $staffList->map(function ($staff) use ($existingAttendance) {
                $attendance = $existingAttendance->get($staff->id);
                
                return [
                    'id' => $staff->id,
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'photo' => $staff->photo,
                    'designation' => $staff->designation->name ?? 'N/A',
                    'subsidiary' => $staff->subsidiary->name ?? 'N/A',
                    'attendance' => $attendance ? [
                        'status' => $attendance->status,
                        'in_time' => $attendance->in_time ? substr($attendance->in_time, 0, 5) : null,
                        'out_time' => $attendance->out_time ? substr($attendance->out_time, 0, 5) : null,
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Staff list retrieved successfully',
                'data' => $staffData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}