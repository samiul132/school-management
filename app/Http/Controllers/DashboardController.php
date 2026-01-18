<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\AccountTransaction;
use App\Models\Staff;
use App\Models\SessionManagement;
use App\Models\StudentAttendance;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\ClassWiseStudentData;
use App\Models\StaffSalaryPayment;
use App\Models\ShiftManagement;
use App\Models\Subject;
use App\Models\StaffSalaryPaymentDetails;
use App\Models\ClassRoutineDetails;
use App\Models\ClassRoutine;
use App\Models\CustomRoutine;
use App\Models\VersionManagement;
use App\Models\ClassManagement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {  
        $today = Carbon::today()->toDateString();
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;
        $currentMonth = Carbon::now()->month;
        $todayDayName = Carbon::now()->format('l');

        $totalStudent = StudentProfile::count();
        
        $todayStudentAttendance = StudentAttendance::whereDate('date', $today)
            ->where('status', 'Present')
            ->count();
        
        $yesterdayStudentAttendance = StudentAttendance::whereDate('date', Carbon::yesterday()->toDateString())
            ->where('status', 'Present')
            ->count();
        
        $studentAttendanceChange = $totalStudent > 0 
            ? (($todayStudentAttendance - $yesterdayStudentAttendance) / $totalStudent) * 100 
            : 0;
        
        $studentAttendancePercentage = $totalStudent > 0 
            ? ($todayStudentAttendance / $totalStudent) * 100 
            : 0;

        $totalTeacher = Staff::where('is_teachers', 1)->count();
        
        $todayTeacherAttendance = Attendance::whereDate('date', $today)
            ->where('status', 'Present')
            ->whereHas('staff', function($query) {
                $query->where('is_teachers', 1);
            })
            ->count();
        
        $yesterdayTeacherAttendance = Attendance::whereDate('date', Carbon::yesterday()->toDateString())
            ->where('status', 'Present')
            ->whereHas('staff', function($query) {
                $query->where('is_teachers', 1);
            })
            ->count();
        
        $teacherAttendanceChange = $totalTeacher > 0 
            ? (($todayTeacherAttendance - $yesterdayTeacherAttendance) / $totalTeacher) * 100 
            : 0;
        
        $teacherAttendancePercentage = $totalTeacher > 0 
            ? ($todayTeacherAttendance / $totalTeacher) * 100 
            : 0;

        $totalOtherStaff = Staff::where('is_teachers', 0)->count();
        
        $todayOtherStaffAttendance = Attendance::whereDate('date', $today)
            ->where('status', 'Present')
            ->whereHas('staff', function($query) {
                $query->where('is_teachers', 0);
            })
            ->count();
        
        $yesterdayOtherStaffAttendance = Attendance::whereDate('date', Carbon::yesterday()->toDateString())
            ->where('status', 'Present')
            ->whereHas('staff', function($query) {
                $query->where('is_teachers', 0);
            })
            ->count();
        
        $otherStaffAttendanceChange = $totalOtherStaff > 0 
            ? (($todayOtherStaffAttendance - $yesterdayOtherStaffAttendance) / $totalOtherStaff) * 100 
            : 0;
        
        $otherStaffAttendancePercentage = $totalOtherStaff > 0 
            ? ($todayOtherStaffAttendance / $totalOtherStaff) * 100 
            : 0;

        $currentSession = SessionManagement::where('status', 'active')->first();

        $currentSessionProfit = 0;
        $lastSessionProfit = 0;
        $profitChange = 0;

        if ($currentSession) {
            $currentYearTransactions = AccountTransaction::where('school_id', $currentSession->school_id)
                ->where('validity', 1)
                ->whereYear('date', $currentYear)
                ->select(
                    DB::raw("SUM(CASE WHEN voucher_type = 'CREDIT' THEN amount ELSE 0 END) as total_revenue"),
                    DB::raw("SUM(CASE WHEN voucher_type = 'DEBIT' THEN amount ELSE 0 END) as total_expense")
                )
                ->first();

            $currentSessionProfit = ($currentYearTransactions->total_revenue ?? 0) - ($currentYearTransactions->total_expense ?? 0);

            $lastYearTransactions = AccountTransaction::where('school_id', $currentSession->school_id)
                ->where('validity', 1)
                ->whereYear('date', $lastYear)
                ->select(
                    DB::raw("SUM(CASE WHEN voucher_type = 'CREDIT' THEN amount ELSE 0 END) as total_revenue"),
                    DB::raw("SUM(CASE WHEN voucher_type = 'DEBIT' THEN amount ELSE 0 END) as total_expense")
                )
                ->first();

            $lastSessionProfit = ($lastYearTransactions->total_revenue ?? 0) - ($lastYearTransactions->total_expense ?? 0);

            if ($lastSessionProfit != 0) {
                $profitChange = (($currentSessionProfit - $lastSessionProfit) / abs($lastSessionProfit)) * 100;
            } else if ($currentSessionProfit > 0) {
                $profitChange = 100;
            }
        }

        $profitProgressPercentage = 0;
            if ($lastSessionProfit > 0) {
                $profitProgressPercentage = min(100, ($currentSessionProfit / $lastSessionProfit) * 100);
            } else if ($currentSessionProfit > 0) {
                $profitProgressPercentage = 100;
            }
        
            $thisSessionTotalReceivedFees = 0;
            $thisMonthTotalReceivedFees = 0;
            $thisSessionTotalAssignFees = 0;
            $thisSessionTotalDueFees = 0;
            $thisSessionTotalExpense = 0;
            $thisMonthTotalExpense = 0;

            if ($currentSession) {
                $thisSessionTotalReceivedFees = AccountTransaction::where('school_id', $currentSession->school_id)
                    ->where('validity', 1)
                    ->where('voucher_type', 'CREDIT')
                    ->where('transaction_type', 'Fee Payment')
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                $thisMonthTotalReceivedFees = AccountTransaction::where('school_id', $currentSession->school_id)
                    ->where('validity', 1)
                    ->where('voucher_type', 'CREDIT')
                    ->where('transaction_type', 'Fee Payment')
                    ->whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->sum('amount');

                $thisSessionTotalAssignFees = DB::table('fee_assigns')
                    ->where('school_id', $currentSession->school_id)
                    ->where('session_id', $currentSession->id)
                    ->sum('total_amount');

                $thisSessionTotalDueFees = $thisSessionTotalAssignFees - $thisSessionTotalReceivedFees;

                $thisSessionTotalExpense = AccountTransaction::where('school_id', $currentSession->school_id)
                    ->where('validity', 1)
                    ->where('voucher_type', 'DEBIT')
                    ->whereYear('date', $currentYear)
                    ->sum('amount');

                $thisMonthTotalExpense = AccountTransaction::where('school_id', $currentSession->school_id)
                    ->where('validity', 1)
                    ->where('voucher_type', 'DEBIT')
                    ->whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth)
                    ->sum('amount');
            }

        $recentTransactions = AccountTransaction::with(['account'])
            ->where('validity', 1)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(function ($transaction) {
                $fromTo = '';
                
                if ($transaction->transaction_type === 'Fee Payment') {
                    $payment = Payment::find($transaction->reference_id);
                    if ($payment) {
                        $classWiseStudent = ClassWiseStudentData::find($payment->class_wise_student_id);
                        if ($classWiseStudent) {
                            $student = StudentProfile::find($classWiseStudent->student_id);
                            if ($student) {
                                $fromTo = "From: {$student->student_name}";
                            }
                        }
                    }
                } else if ($transaction->transaction_type === 'SALARY_PAYMENT' || $transaction->transaction_type === 'SALARY_ADVANCE') {
                    $salaryPayment = StaffSalaryPayment::find($transaction->reference_id);
                    if ($salaryPayment) {
                        $paymentDetail = StaffSalaryPaymentDetails::where('payment_id', $salaryPayment->id)->first();
                        if ($paymentDetail) {
                            $staff = Staff::find($paymentDetail->staff_id);
                            if ($staff) {
                                $fullName = trim($staff->first_name . ' ' . $staff->last_name);
                                $fromTo = "To: {$fullName}";
                            }
                        }
                    }
                }

                return [
                    'id' => $transaction->id,
                    'transaction_type' => $transaction->transaction_type,
                    'voucher_type' => $transaction->voucher_type,
                    'amount' => $transaction->amount,
                    'date' => Carbon::parse($transaction->date)->format('M d, Y'),
                    'reference_id' => $transaction->reference_id,
                    'description' => $transaction->description,
                    'account_name' => $transaction->account ? $transaction->account->account_name : 'N/A',
                    'from_to' => $fromTo,
                ];
            });

        $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $income = AccountTransaction::whereYear('date', now()->year)
                    ->whereMonth('date', $month)
                    ->where('voucher_type', 'CREDIT')
                    ->where('transaction_type', '!=', 'Cash Transfer')
                    ->where('validity', 1)
                    ->sum('amount');
                    
                $expense = AccountTransaction::whereYear('date', now()->year)
                    ->whereMonth('date', $month)
                    ->where('voucher_type', 'DEBIT')
                    ->where('transaction_type', '!=', 'Cash Transfer')
                    ->where('validity', 1)
                    ->sum('amount');
                    
                $monthlyData[] = [
                    'month' => $month,
                    'income' => $income,
                    'expense' => $expense
                ];
            } 
            
            
        $studentAttendanceBreakdown = [
            'present' => StudentAttendance::whereDate('date', $today)
                ->where('status', 'Present')
                ->count(),
            'absent' => StudentAttendance::whereDate('date', $today)
                ->where('status', 'Absent')
                ->count(),
            'late' => StudentAttendance::whereDate('date', $today)
                ->where('status', 'Late')
                ->count(),
            'leave' => StudentAttendance::whereDate('date', $today)
                ->where('status', 'Leave')
                ->count(),
        ];

        $teacherAttendanceBreakdown = [
            'present' => Attendance::whereDate('date', $today)
                ->where('status', 'Present')
                ->whereHas('staff', function($query) {
                    $query->where('is_teachers', 1);
                })
                ->count(),
            'absent' => Attendance::whereDate('date', $today)
                ->where('status', 'Absent')
                ->whereHas('staff', function($query) {
                    $query->where('is_teachers', 1);
                })
                ->count(),
            'late' => Attendance::whereDate('date', $today)
                ->where('status', 'Late')
                ->whereHas('staff', function($query) {
                    $query->where('is_teachers', 1);
                })
                ->count(),
            'leave' => Attendance::whereDate('date', $today)
                ->where('status', 'Leave')
                ->whereHas('staff', function($query) {
                    $query->where('is_teachers', 1);
                })
                ->count(),
        ];  
        

        return response()->json([
            'success' => true,
            'data' => [
                'totalStudent' => $totalStudent,
                'todayStudentAttendance' => $todayStudentAttendance,
                'studentAttendanceChange' => round($studentAttendanceChange, 1),
                'studentAttendancePercentage' => round($studentAttendancePercentage, 0),
                
                'totalTeacher' => $totalTeacher,
                'todayTeacherAttendance' => $todayTeacherAttendance,
                'teacherAttendanceChange' => round($teacherAttendanceChange, 1),
                'teacherAttendancePercentage' => round($teacherAttendancePercentage, 0),
                
                'totalOtherStaff' => $totalOtherStaff,
                'todayOtherStaffAttendance' => $todayOtherStaffAttendance,
                'otherStaffAttendanceChange' => round($otherStaffAttendanceChange, 1),
                'otherStaffAttendancePercentage' => round($otherStaffAttendancePercentage, 0),
                
                'currentSessionProfit' => round($currentSessionProfit, 2),
                'lastSessionProfit' => round($lastSessionProfit, 2),
                'profitChange' => round($profitChange, 1),
                'profitProgressPercentage' => round($profitProgressPercentage, 0),

                'thisSessionTotalReceivedFees' => round($thisSessionTotalReceivedFees, 2),
                'thisMonthTotalReceivedFees' => round($thisMonthTotalReceivedFees, 2),
                'thisSessionTotalAssignFees' => round($thisSessionTotalAssignFees, 2),
                'thisSessionTotalDueFees' => round($thisSessionTotalDueFees, 2),
                'thisSessionTotalExpense' => round($thisSessionTotalExpense, 2),
                'thisMonthTotalExpense' => round($thisMonthTotalExpense, 2),

                'recentTransactions' => $recentTransactions,
                'monthlyData' => $monthlyData,

                'studentAttendanceBreakdown' => $studentAttendanceBreakdown,
                'teacherAttendanceBreakdown' => $teacherAttendanceBreakdown,
            ]
        ]);
    }

    public function customRoutine(Request $request): JsonResponse
    {
        try {
            $date = date('Y-m-d');
            $todayName = date('l', strtotime($date));
            $existingRoutine = CustomRoutine::where('date', $date)->first();

            if($existingRoutine == NULL){
                $day = date('l', strtotime($date));
                $currentSession = SessionManagement::where('status', 'active')->first();
                
                $classRoutines = ClassRoutineDetails::select(
                    'class_routine_details.*',
                    'class_routines.version_id',
                    'class_routines.class_id',
                    'class_routines.section_id',
                    'class_routines.shift_id',
                    'subjects.subject_name',
                    'staff.first_name as teacher_first_name',
                    'staff.last_name as teacher_last_name',
                    'class_management.class_name',
                    'version_management.version_name',
                    'section_management.section_name'
                )
                ->leftJoin('class_routines', 'class_routines.id', '=', 'class_routine_details.class_routine_id')
                ->leftJoin('subjects', 'subjects.id', '=', 'class_routine_details.subject_id')
                ->leftJoin('staff', 'staff.id', '=', 'class_routine_details.teacher_id')
                ->leftJoin('class_management', 'class_management.id', '=', 'class_routines.class_id')
                ->leftJoin('version_management', 'version_management.id', '=', 'class_routines.version_id')
                ->leftJoin('section_management', 'section_management.id', '=', 'class_routines.section_id')
                ->where('class_routines.session_id', $currentSession->id)
                ->where('class_routine_details.day_name', $day)
                ->get();
                
                $teacherWiseData = [];
                $classWiseData = [];
                $shiftWisePeriods = [];
                
                foreach($classRoutines as $routine){
                    $shiftWisePeriods[$routine->shift_id][$routine->period_number] = $routine->time;
                    
                    $teacherWiseData[$routine->teacher_id][$routine->shift_id][$routine->period_number] = [
                        'version_name' => $routine->version_name,
                        'class_name' => $routine->class_name,
                        'section_name' => $routine->section_name,
                        'subject_name' => $routine->subject_name,
                    ];

                    $classWiseData[$routine->version_id][$routine->class_id][$routine->section_id][$routine->shift_id][$routine->period_number] = [
                        'teacher_id' => $routine->teacher_id,
                        'class_name' => $routine->class_name,
                        'section_name' => $routine->section_name,
                        'subject_name' => $routine->subject_name,
                    ];
                }

                $routine = new CustomRoutine();
                $routine->date = $date;
                $routine->periods = $shiftWisePeriods;
                $routine->teacher_wise_data = $teacherWiseData;
                $routine->class_wise_data = $classWiseData;
                $routine->save();

            } else {
                $routine = $existingRoutine;
            }

            $data['routine'] = $routine;
            $data['day_name'] = $todayName;
            $data['teachers'] = Staff::where('is_teachers', 1)->with('designation')->get()->keyBy('id');
            $data['shifts'] = ShiftManagement::where('status', 'active')->get()->keyBy('id');
            $data['subjects'] = Subject::where('status', 'active')->get()->keyBy('id');
            $data['versions'] = VersionManagement::where('status', 'active')->get()->keyBy('id');
            $data['classes'] = ClassManagement::where('status', 'active')->get()->keyBy('id');

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Routine retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve routine: ' . $e->getMessage()
            ], 500);
        }
    }
}