<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdvancePayment;
use App\Models\Staff;
use App\Models\CashBank;
use App\Models\AccountTransaction;
use App\Models\Voucher;
use App\Models\CommonModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $advancePayments = AdvancePayment::with(['staff.designation'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $advancePayments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch advance payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStaffList()
    {
        try {
            $staffList = Staff::select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function($staff) {
                    return [
                        'id' => $staff->id,
                        'name' => $staff->first_name . ' ' . $staff->last_name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $staffList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff list'
            ], 500);
        }
    }

    public function getAccountsList()
    {
        try {
            $accounts = CashBank::select('id', 'account_name')
                ->orderBy('account_name')
                ->get()
                ->map(function($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->account_name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $accounts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch accounts list'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'staff_id' => 'required|exists:staff,id',
            'account_id' => 'required|exists:cash_banks,id',
            'month' => 'nullable|string|max:2',
            'year' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string'
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
            $advancePayment = new AdvancePayment();
            $advancePayment->date = $request->date;
            $advancePayment->staff_id = $request->staff_id;
            $advancePayment->month = $request->month;
            $advancePayment->year = $request->year;
            $advancePayment->amount = $request->amount;
            $advancePayment->remarks = $request->remarks;
            $advancePayment->save();

            $staff = Staff::find($request->staff_id);
            
            $voucher = new Voucher();
            $voucher->date = $request->date;
            $voucher->voucher_type = 'DEBIT';
            $voucher->account_id = $request->account_id;
            $voucher->subsidiaries_id = $staff->subsidiaries_id ?? null;
            $voucher->head_id = null;
            $voucher->amount = $request->amount;
            $voucher->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
            $voucher->module_name = 'SALARY_ADVANCE';
            $voucher->ref_module_id = $advancePayment->id;
            $voucher->save();

            $transaction = new AccountTransaction();
            $transaction->transaction_type = 'SALARY_ADVANCE';
            $transaction->voucher_type = 'DEBIT';
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->reference_id = $advancePayment->id;
            $transaction->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
            $transaction->amount = $request->amount;
            $transaction->validity = 1;
            $transaction->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            $advancePayment->load('staff');

            return response()->json([
                'success' => true,
                'message' => 'Advance payment created successfully',
                'data' => $advancePayment
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create advance payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $advancePayment = AdvancePayment::with(['staff.designation'])->findOrFail($id);

            $transaction = AccountTransaction::where('reference_id', $id)
                ->where('transaction_type', 'SALARY_ADVANCE')
                ->where('validity', 1)
                ->first();

            $voucher = Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY_ADVANCE')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'advance_payment' => $advancePayment,
                    'transaction' => $transaction,
                    'voucher' => $voucher
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Advance payment not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'staff_id' => 'required|exists:staff,id',
            'account_id' => 'required|exists:cash_banks,id',
            'month' => 'nullable|string|max:2',
            'year' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string'
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
            $advancePayment = AdvancePayment::findOrFail($id);
            $advancePayment->date = $request->date;
            $advancePayment->staff_id = $request->staff_id;
            $advancePayment->month = $request->month;
            $advancePayment->year = $request->year;
            $advancePayment->amount = $request->amount;
            $advancePayment->remarks = $request->remarks;
            $advancePayment->save();

            $staff = Staff::find($request->staff_id);

            $voucher = Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY_ADVANCE')
                ->first();

            if ($voucher) {
                $voucher->date = $request->date;
                $voucher->account_id = $request->account_id;
                $voucher->subsidiaries_id = $staff->subsidiaries_id ?? null;
                $voucher->amount = $request->amount;
                $voucher->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
                $voucher->save();
            } else {
                $voucher = new Voucher();
                $voucher->date = $request->date;
                $voucher->voucher_type = 'DEBIT';
                $voucher->account_id = $request->account_id;
                $voucher->subsidiaries_id = $staff->subsidiaries_id ?? null;
                $voucher->head_id = null;
                $voucher->amount = $request->amount;
                $voucher->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
                $voucher->module_name = 'SALARY_ADVANCE';
                $voucher->ref_module_id = $advancePayment->id;
                $voucher->save();
            }

            $transaction = AccountTransaction::where('reference_id', $id)
                ->where('transaction_type', 'SALARY_ADVANCE')
                ->where('validity', 1)
                ->first();

            if ($transaction) {
                $transaction->date = $request->date;
                $transaction->account_id = $request->account_id;
                $transaction->amount = $request->amount;
                $transaction->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
                $transaction->save();
            } else {
                $transaction = new AccountTransaction();
                $transaction->transaction_type = 'SALARY_ADVANCE';
                $transaction->voucher_type = 'DEBIT';
                $transaction->date = $request->date;
                $transaction->account_id = $request->account_id;
                $transaction->reference_id = $advancePayment->id;
                $transaction->description = 'Advance Salary Payment - ' . $staff->first_name . ' ' . $staff->last_name;
                $transaction->amount = $request->amount;
                $transaction->validity = 1;
                $transaction->save();
            }

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            $advancePayment->load('staff');

            return response()->json([
                'success' => true,
                'message' => 'Advance payment updated successfully',
                'data' => $advancePayment
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update advance payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $advancePayment = AdvancePayment::findOrFail($id);

            $transWhere = ['reference_id'=>$id, 'transaction_type'=>'SALARY_ADVANCE'];
            AccountTransaction::softDeleteRecord($transWhere);

            Voucher::where('ref_module_id', $id)
                ->where('module_name', 'SALARY_ADVANCE')
                ->delete();
            
            $advancePayment->delete();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Advance payment deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete advance payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByStaff($staffId)
    {
        try {
            $advancePayments = AdvancePayment::with(['staff'])
                ->where('staff_id', $staffId)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $advancePayments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff advance payments'
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
            $advancePayments = AdvancePayment::with(['staff'])
                ->whereBetween('date', [$request->start_date, $request->end_date])
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $advancePayments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch advance payments'
            ], 500);
        }
    }
}