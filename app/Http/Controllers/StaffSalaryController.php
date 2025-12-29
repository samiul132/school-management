<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffSalary;
use App\Models\AdvancePayment;
use App\Models\Attendance;
use App\Models\OverTime;
use App\Models\StaffSalaryDetails;
use App\Models\Voucher;
use App\Models\AccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StaffSalaryController extends Controller
{

    public function index()
    {
        try {
            $salaries = StaffSalary::with(['salaryDetails.staff.designation'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $salaries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch salary records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $salary = StaffSalary::with(['salaryDetails.staff.designation'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $salary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Salary sheet not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|string|max:2',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $month = $request->month;
        $year = $request->year;

        try {
            $staffList = Staff::where('status', 'ACTIVE')
                ->with('designation')
                ->get();
            
            $staffData = [];
            $totalPayableSalary = 0;
            $totalAdvance = 0;
            $totalOvertime = 0;

            $date = new \DateTime("$year-$month-01");
            $daysInMonth = (int) $date->format('t');

            foreach ($staffList as $staff) {
                $basicSalary = $staff->basic_salary;
                $staffWorkingHours = $staff->working_hour ?? 0;

                $advancePayment = AdvancePayment::where('staff_id', $staff->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('amount');

                $absentDays = Attendance::where('staff_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'Absent')
                    ->count();
                    
                $presentDays = Attendance::where('staff_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'Present')
                    ->count();
                    
                $lateDays = Attendance::where('staff_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'Late')
                    ->count();
                    
                $leaveDays = Attendance::where('staff_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('status', 'Leave')
                    ->count();

                $perDaySalary = ($basicSalary / $daysInMonth);
                $absentDeduction = $absentDays * $perDaySalary;

                $totalOvertimeHours = OverTime::where('staff_id', $staff->id)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('over_time_hour');

                $perHourRate = ($perDaySalary / $staffWorkingHours);
                
                $overtimeAmount = $totalOvertimeHours * $perHourRate;

                $netPayable = $basicSalary - $absentDeduction - $advancePayment + $overtimeAmount;
                $grossSalary = $basicSalary - $absentDeduction;

                $staffData[] = [
                    'staff_name' => $staff->first_name . ' ' . $staff->last_name,
                    'id' => $staff->id,
                    'basic_salary' => round($basicSalary, 2),
                    'absent_days' => $absentDays,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'leave_days' => $leaveDays,
                    'absent_deduction' => round($absentDeduction, 2),
                    'over_time_hour' => $totalOvertimeHours,
                    'overtime' => round($overtimeAmount, 2),
                    'gross_salary' => round($grossSalary, 2),
                    'advance_payment' => round($advancePayment, 2),
                    'total_payable_salary' => round($netPayable, 2),
                ];

                $totalPayableSalary += round($netPayable, 2);
                $totalAdvance += round($advancePayment, 2);
                $totalOvertime += round($overtimeAmount, 2);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'staffData' => $staffData,
                    'totalPayableSalary' => round($totalPayableSalary, 2),
                    'totalAdvance' => round($totalAdvance, 2),
                    'totalOvertime' => round($totalOvertime, 2),
                    'totalStaff' => count($staffData),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate salary sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'month' => 'required|string',
            'year' => 'required|integer',
            'staffData' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $existingSalary = StaffSalary::where('month', $request->month)
                ->where('year', $request->year)
                ->first();

            if ($existingSalary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salary sheet already exists for this month and year'
                ], 422);
            }

            $salary = new StaffSalary;
            $salary->title = $request->title;
            $salary->month = $request->month;
            $salary->year = $request->year;
            $salary->total_salary = array_sum(array_column($request->staffData, 'total_payable_salary'));
            $salary->total_advance = array_sum(array_column($request->staffData, 'advance_payment'));
            $salary->total_over_time = array_sum(array_column($request->staffData, 'overtime'));
            $salary->save();

            $accConfig = AccountSetting::first();

            foreach ($request->staffData as $staff) {
                $salaryDetail = new StaffSalaryDetails;
                $salaryDetail->salary_id = $salary->id;
                $salaryDetail->staff_id = $staff['id'];
                $salaryDetail->gross_salary = $staff['gross_salary'];
                $salaryDetail->advance_payment = $staff['advance_payment'];
                $salaryDetail->over_time_salary = $staff['overtime'];
                $salaryDetail->payable_salary = $staff['total_payable_salary'];
                $salaryDetail->save();

                $staffInfo = Staff::find($staff['id']);

                if ($staffInfo->subsidiaries_id != NULL) {
                    $AccVoucher = new Voucher;
                    $AccVoucher->date = $request->year . '-' . $request->month . '-01';
                    $AccVoucher->voucher_type = 'CREDIT';
                    $AccVoucher->module_name = 'SALARY';
                    $AccVoucher->head_id = $accConfig->salary_head_id;
                    $AccVoucher->subsidiaries_id = $staffInfo->subsidiaries_id;
                    $AccVoucher->ref_module_id = $salary->id;
                    $AccVoucher->amount = $staff['total_payable_salary'];
                    $AccVoucher->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary sheet saved successfully!',
                'data' => $salary
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save salary sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'month' => 'required|string',
            'year' => 'required|integer',
            'staffData' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $salary = StaffSalary::findOrFail($id);
            $salary->title = $request->title;
            $salary->month = $request->month;
            $salary->year = $request->year;
            $salary->total_salary = array_sum(array_column($request->staffData, 'total_payable_salary'));
            $salary->total_advance = array_sum(array_column($request->staffData, 'advance_payment'));
            $salary->total_over_time = array_sum(array_column($request->staffData, 'overtime'));
            $salary->save();

            $staffIds = array_column($request->staffData, 'id');

            if (!empty($staffIds)) {
                StaffSalaryDetails::where('salary_id', $id)
                    ->whereNotIn('staff_id', $staffIds)
                    ->delete();
            }

            Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY')
                ->delete();

            $accConfig = AccountSetting::first();

            foreach ($request->staffData as $staff) {
                $salaryDetail = StaffSalaryDetails::where('salary_id', $id)
                    ->where('staff_id', $staff['id'])
                    ->first();
                
                if ($salaryDetail == NULL) {
                    $salaryDetail = new StaffSalaryDetails;
                }
                
                $salaryDetail->salary_id = $salary->id;
                $salaryDetail->staff_id = $staff['id'];
                $salaryDetail->gross_salary = $staff['gross_salary'];
                $salaryDetail->advance_payment = $staff['advance_payment'];
                $salaryDetail->over_time_salary = $staff['overtime'];
                $salaryDetail->payable_salary = $staff['total_payable_salary'];
                $salaryDetail->save();

                $staffInfo = Staff::find($staff['id']);

                if ($staffInfo->subsidiaries_id != NULL) {
                    $AccVoucher = new Voucher;
                    $AccVoucher->date = $request->year . '-' . $request->month . '-01';
                    $AccVoucher->voucher_type = 'CREDIT';
                    $AccVoucher->module_name = 'SALARY';
                    $AccVoucher->head_id = $accConfig->salary_head_id;
                    $AccVoucher->subsidiaries_id = $staffInfo->subsidiaries_id;
                    $AccVoucher->ref_module_id = $salary->id;
                    $AccVoucher->amount = $staff['total_payable_salary'];
                    $AccVoucher->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary sheet updated successfully!',
                'data' => $salary
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update salary sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            StaffSalary::where('id', $id)->delete();
            StaffSalaryDetails::where('salary_id', $id)->delete();
            Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY')
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary sheet deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete salary sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}