<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAttendance;
use App\Models\ClassWiseStudentData;
use App\Models\StudentProfile;
use App\Models\ClassManagement;
use App\Models\SectionManagement;
use App\Models\SessionManagement;
use App\Models\VersionManagement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = StudentAttendance::with(['student.studentProfile', 'student.class', 'student.section']);

            if ($request->has('search') && $request->search != '') {
                $searchTerm = $request->search;
                $query->whereHas('student.studentProfile', function($q) use ($searchTerm) {
                    $q->where('student_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('id_card_number', 'LIKE', "%{$searchTerm}%");
                })->orWhereHas('student', function($q) use ($searchTerm) {
                    $q->where('class_roll', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($request->has('class_wise_student_id') && $request->class_wise_student_id != '') {
                $query->where('class_wise_student_id', $request->class_wise_student_id);
            }

            if ($request->has('month') && $request->month != '') {
                $query->whereMonth('date', $request->month);
            }

            if ($request->has('year') && $request->year != '') {
                $query->whereYear('date', $request->year);
            }

            if ($request->has('class_id') && $request->class_id != '') {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }

            if ($request->has('section_id') && $request->section_id != '') {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('section_id', $request->section_id);
                });
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
                'message' => 'Student attendance records retrieved successfully',
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
            $classId = $request->get('class_id');
            $sectionId = $request->get('section_id');
            $sessionId = $request->get('session_id');

            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            $studentQuery = ClassWiseStudentData::with([
                'studentProfile',
                'class',
                'section',
                'version',
                'session'
            ]);

            if ($classId) {
                $studentQuery->where('class_id', $classId);
            }

            if ($sectionId) {
                $studentQuery->where('section_id', $sectionId);
            }

            if ($sessionId) {
                $studentQuery->where('session_id', $sessionId);
            }

            $studentList = $studentQuery->orderBy('class_roll')->get();

            $attendanceQuery = StudentAttendance::whereBetween('date', [$startDate, $endDate]);
            
            if ($classId || $sectionId || $sessionId) {
                $studentIds = $studentList->pluck('id')->toArray();
                $attendanceQuery->whereIn('class_wise_student_id', $studentIds);
            }

            $attendances = $attendanceQuery->get();

            $attendanceArray = [];
            $totalAttendance = [];

            foreach ($attendances as $record) {
                $attendanceArray[$record->class_wise_student_id][$record->date] = [
                    'status' => $record->status,
                    'in_time' => $record->in_time,
                    'out_time' => $record->out_time
                ];
                
                if (!isset($totalAttendance[$record->class_wise_student_id][$record->status])) {
                    $totalAttendance[$record->class_wise_student_id][$record->status] = 0;
                }
                $totalAttendance[$record->class_wise_student_id][$record->status]++;
            }

            $monthDays = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $monthDays[] = $currentDate;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
            $studentData = [];
            foreach ($studentList as $student) {
                $studentAttendance = [];
                foreach ($monthDays as $date) {
                    $studentAttendance[$date] = $attendanceArray[$student->id][$date] ?? [
                        'status' => null,
                        'in_time' => null,
                        'out_time' => null
                    ];
                }

                $studentData[] = [
                    'id' => $student->id,
                    'name' => $student->studentProfile->student_name,
                    'roll' => $student->class_roll,
                    'id_card_number' => $student->studentProfile->id_card_number,
                    'class' => $student->class->class_name ?? 'N/A',
                    'section' => $student->section->section_name ?? 'N/A',
                    'photo' => $student->studentProfile->student_image,
                    'total_present' => $totalAttendance[$student->id]['Present'] ?? 0,
                    'total_absent' => $totalAttendance[$student->id]['Absent'] ?? 0,
                    'total_late' => $totalAttendance[$student->id]['Late'] ?? 0,
                    'total_leave' => $totalAttendance[$student->id]['Leave'] ?? 0,
                    'attendance' => $studentAttendance
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Student attendance summary retrieved successfully',
                'data' => [
                    'student_list' => $studentData,
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
                'attendance' => 'required|array',
                'attendance.*.class_wise_student_id' => 'required|exists:class_wise_student_data,id',
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
                StudentAttendance::where('date', $request->date)->delete();

                foreach ($request->attendance as $record) {
                    $inTime = isset($record['in_time']) && $record['in_time'] 
                        ? (strlen($record['in_time']) === 5 ? $record['in_time'] . ':00' : $record['in_time'])
                        : null;
                    
                    $outTime = isset($record['out_time']) && $record['out_time']
                        ? (strlen($record['out_time']) === 5 ? $record['out_time'] . ':00' : $record['out_time'])
                        : null;

                    $attendance = new StudentAttendance();
                    $attendance->date = $request->date;
                    $attendance->class_wise_student_id = $record['class_wise_student_id'];
                    $attendance->status = $record['status'];
                    $attendance->in_time = $inTime;
                    $attendance->out_time = $outTime;
                    $attendance->save();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Student attendance saved successfully',
                    'data' => [
                        'date' => $request->date,
                        'total_records' => count($request->attendance)
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
            $attendance = StudentAttendance::with([
                'student.studentProfile',
                'student.class',
                'student.section'
            ])->find($id);

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
            $attendances = StudentAttendance::with([
                'student.studentProfile',
                'student.class',
                'student.section'
            ])
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

    public function getStudentList(Request $request)
    {
        try {
            $date = $request->get('date', date('Y-m-d'));
            $classId = $request->get('class_id');
            $sectionId = $request->get('section_id');
            $sessionId = $request->get('session_id');

            $query = ClassWiseStudentData::with([
                'studentProfile',
                'class',
                'section',
                'version',
                'session'
            ]);

            if ($classId) {
                $query->where('class_id', $classId);
            }

            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }

            if ($sessionId) {
                $query->where('session_id', $sessionId);
            }

            $studentList = $query->orderBy('class_roll')->get();

            $existingAttendance = StudentAttendance::where('date', $date)
                ->get()
                ->keyBy('class_wise_student_id');

            $studentData = $studentList->map(function ($student) use ($existingAttendance) {
                $attendance = $existingAttendance->get($student->id);
                
                return [
                    'id' => $student->id,
                    'student_name' => $student->studentProfile->student_name,
                    'id_card_number' => $student->studentProfile->id_card_number,
                    'roll' => $student->class_roll,
                    'photo' => $student->studentProfile->student_image,
                    'class' => $student->class->class_name ?? 'N/A',
                    'section' => $student->section->section_name ?? 'N/A',
                    'version' => $student->version->version_name ?? 'N/A',
                    'session' => $student->session->session_name ?? 'N/A',
                    'attendance' => $attendance ? [
                        'status' => $attendance->status,
                        'in_time' => $attendance->in_time ? substr($attendance->in_time, 0, 5) : null,
                        'out_time' => $attendance->out_time ? substr($attendance->out_time, 0, 5) : null,
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Student list retrieved successfully',
                'data' => $studentData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = StudentAttendance::findOrFail($id);
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

    public function getDropdownData()
    {
        try {
            $classes = ClassManagement::where('status', 'active')->get();
            $sections = SectionManagement::where('status', 'active')->get();
            $sessions = SessionManagement::where('status', 'active')->get();
            $versions = VersionManagement::where('status', 'active')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classes,
                    'sections' => $sections,
                    'sessions' => $sessions,
                    'versions' => $versions
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dropdown data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}