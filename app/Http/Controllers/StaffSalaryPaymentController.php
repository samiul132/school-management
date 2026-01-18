<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffSalary;
use App\Models\StaffSalaryDetails;
use App\Models\StaffSalaryPayment;
use App\Models\StaffSalaryPaymentDetails;
use App\Models\AdvancePayment;
use App\Models\Voucher;
use App\Models\AccountSetting;
use App\Models\AccountTransaction;
use App\Models\CashBank;
use App\Models\CommonModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StaffSalaryPaymentController extends Controller
{

    public function index()
    {
        try {
            $payments = StaffSalaryPayment::with('salarySheet:id,title')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch salary payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function prepare(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sheet_id' => 'required|exists:staff_salaries,id',
                'date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sheetId = $request->sheet_id;

            $salaryDetails = StaffSalaryDetails::where('salary_id', $sheetId)
                ->with('staff:id,first_name,last_name')
                ->get()
                ->map(function ($detail) {

                    $totalPaid = StaffSalaryPaymentDetails::where('salary_details_id', $detail->id)
                        ->sum('paid_amount');

                    $due = $detail->payable_salary - $totalPaid;

                    return [
                        'id' => $detail->id,
                        'staff_id' => $detail->staff_id,
                        'staff_name' => $detail->staff->first_name . ' ' . $detail->staff->last_name,
                        'staff_code' => $detail->staff->staff_id,
                        'gross_salary' => $detail->gross_salary,
                        'payable_salary' => $detail->payable_salary,
                        'advance_payment' => $detail->advance_payment,
                        'total_paid' => $totalPaid,
                        'due' => $due,
                        'current_due' => $due,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'staffData' => $salaryDetails
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to prepare salary payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sheet_id' => 'required|exists:staff_salaries,id',
                'date' => 'required|date',
                'acc_id' => 'required|exists:cash_banks,id',
                'payment_amount' => 'required|array',
                'payment_amount.*' => 'numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $salaryPayment = new StaffSalaryPayment;
            $salaryPayment->sheet_id = $request->sheet_id;
            $salaryPayment->acc_id = $request->acc_id;
            $salaryPayment->date = $request->date;
            $salaryPayment->total_paid = array_sum($request->payment_amount);
            $salaryPayment->save();

            $accConfig = AccountSetting::first();

            foreach ($request->payment_amount as $detailsId => $paidAmount) {
                if ($paidAmount > 0) {
                    $salaryDetails = StaffSalaryDetails::find($detailsId);
                    //dd($salaryDetails);
                    $salaryPaymentDetail = new StaffSalaryPaymentDetails;
                    $salaryPaymentDetail->payment_id = $salaryPayment->id;
                    $salaryPaymentDetail->salary_details_id = $detailsId;
                    $salaryPaymentDetail->staff_id = $salaryDetails->staff_id;
                    $salaryPaymentDetail->paid_amount = $paidAmount;
                    //dd($salaryPaymentDetail);
                    $salaryPaymentDetail->save();

                    $stafInfo = Staff::find($salaryDetails->staff_id);

                    if ($stafInfo->subsidiaries_id!=NULL) {
                        $AccVoucher = new Voucher;
                        $AccVoucher->date = $request->date;
                        $AccVoucher->voucher_type = 'DEBIT';
                        $AccVoucher->module_name = 'SALARY_PAYMENT';
                        $AccVoucher->head_id = $accConfig->salary_head_id;
                        $AccVoucher->subsidiaries_id = $stafInfo->subsidiaries_id;
                        $AccVoucher->ref_module_id = $salaryPayment->id;
                        $AccVoucher->amount = $paidAmount;
                        $AccVoucher->account_id = $request->acc_id;
                        $AccVoucher->save();
                    }
                }
            }

            $transation = new AccountTransaction;
            $transation->date = $request->date;
            $transation->transaction_type = 'SALARY_PAYMENT';
            $transation->voucher_type = 'DEBIT';
            $transation->account_id = $request->acc_id;
            $transation->description = 'Salary Payment';
            $transation->amount = $salaryPayment->total_paid;
            $transation->reference_id = $salaryPayment->id;
            $transation->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary payment saved successfully!',
                'data' => $salaryPayment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save salary payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $payment = StaffSalaryPayment::with([
                'salarySheet:id,title',
                'account:id,account_name',
                'paymentDetails.salaryDetails.staff:id,first_name,last_name',
                'paymentDetails.salaryDetails'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Salary payment not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sheet_id' => 'required|exists:staff_salaries,id',
                'date' => 'required|date',
                'acc_id' => 'required|exists:cash_banks,id',
                'payment_amount' => 'required|array',
                'payment_amount.*' => 'numeric|min:0',
            ]);
            //dd($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $salaryPayment = StaffSalaryPayment::findOrFail($id);

            $salaryPayment->sheet_id = $request->sheet_id;
            $salaryPayment->acc_id = $request->acc_id;
            $salaryPayment->date = $request->date;
            $salaryPayment->total_paid = array_sum($request->payment_amount);
            $salaryPayment->save();

            StaffSalaryPaymentDetails::where('payment_id', $id)->delete();
            Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY_PAYMENT')
                ->delete();

            $accConfig = AccountSetting::first();

            foreach ($request->payment_amount as $staffId => $paidAmount) {
                if ($paidAmount > 0) {
                    $salaryDetails = StaffSalaryDetails::where('staff_id', $staffId)->first();
                    //dd($salaryDetails);
                    $salaryPaymentDetail = new StaffSalaryPaymentDetails;
                    $salaryPaymentDetail->payment_id = $salaryPayment->id;
                    $salaryPaymentDetail->salary_details_id = $salaryDetails->id;
                    $salaryPaymentDetail->staff_id = $staffId;
                    $salaryPaymentDetail->paid_amount = $paidAmount;
                    //dd($salaryPaymentDetail);
                    $salaryPaymentDetail->save();

                    $stafInfo = Staff::find($staffId);

                    if ($stafInfo->subsidiaries_id!=NULL) {
                        $AccVoucher = new Voucher;
                        $AccVoucher->date = $request->date;
                        $AccVoucher->voucher_type = 'DEBIT';
                        $AccVoucher->module_name = 'SALARY_PAYMENT';
                        $AccVoucher->head_id = $accConfig->salary_head_id;
                        $AccVoucher->subsidiaries_id = $stafInfo->subsidiaries_id;
                        $AccVoucher->ref_module_id = $salaryPayment->id;
                        $AccVoucher->amount = $paidAmount;
                        $AccVoucher->account_id = $request->acc_id;
                        $AccVoucher->save();
                    }
                }
            }

            // Update account transaction
            $transation = AccountTransaction::where('transaction_type', 'SALARY_PAYMENT')
                ->where('reference_id', $id)
                ->first();
            $transation->date = $request->date;
            $transation->transaction_type = 'SALARY_PAYMENT';
            $transation->voucher_type = 'DEBIT';
            $transation->account_id = $request->acc_id;
            $transation->description = 'Salary Payment';
            $transation->amount = $salaryPayment->total_paid;
            $transation->reference_id = $salaryPayment->id;
            $transation->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salary payment updated successfully!',
                'data' => $salaryPayment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update salary payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            StaffSalaryPayment::where('id', $id)->delete();
            StaffSalaryPaymentDetails::where('payment_id', $id)->delete();

            Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY_PAYMENT')
                ->delete();

            $transWhere = ['reference_id'=>$id, 'transaction_type'=>'SALARY_PAYMENT'];
            AccountTransaction::softDeleteRecord($transWhere);

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Salary payment deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete salary payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}