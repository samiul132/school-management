<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentDetails;
use App\Models\ClassWiseStudentData;
use App\Models\CashBank;
use App\Models\MonthManagement;
use App\Models\AccountTransaction;
use App\Models\CommonModel;
use App\Models\StudentWiseFeeAssign;
use App\Models\FeeAssign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Payment::with([
                'classWiseStudent.student',
                'classWiseStudent.session',
                'classWiseStudent.class',
                'classWiseStudent.version',
                'classWiseStudent.section',
                'month',
                'account',
                'paymentDetails.feeHead'
            ]);

            if ($request->has('id_card_number') && $request->id_card_number) {
                $query->whereHas('classWiseStudent.student', function($q) use ($request) {
                    $q->where('id_card_number', $request->id_card_number);
                });
            }

            if ($request->has('session_id') && $request->session_id) {
                $query->whereHas('classWiseStudent', function($q) use ($request) {
                    $q->where('session_id', $request->session_id);
                });
            }

            if ($request->has('version_id') && $request->version_id) {
                $query->whereHas('classWiseStudent', function($q) use ($request) {
                    $q->where('version_id', $request->version_id);
                });
            }

            if ($request->has('class_id') && $request->class_id) {
                $query->whereHas('classWiseStudent', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }

            if ($request->has('section_id') && $request->section_id) {
                $query->whereHas('classWiseStudent', function($q) use ($request) {
                    $q->where('section_id', $request->section_id);
                });
            }

            if ($request->has('roll') && $request->roll) {
                $query->whereHas('classWiseStudent', function($q) use ($request) {
                    $q->where('class_roll', $request->roll);
                });
            }

            if ($request->has('month_id') && $request->month_id) {
                $query->where('month_id', $request->month_id);
            }

            if ($request->has('account_id') && $request->account_id) {
                $query->where('account_id', $request->account_id);
            }

            if ($request->has('start_date') && $request->start_date) {
                $query->where('payment_date', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->where('payment_date', '<=', $request->end_date);
            }

            if ($request->has('class_wise_student_id') && $request->class_wise_student_id) {
                $query->where('class_wise_student_id', $request->class_wise_student_id);
            }

            $sortBy = $request->get('sort_by', 'payment_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $payments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $payments
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentFees(Request $request)
    {
        $request->validate([
            'month_id' => 'required|exists:month_management,id',
        ]);
        $monthInfo = MonthManagement::find($request->month_id);
        
        $dueMonths = MonthManagement::where('order_number', '<=', $monthInfo->order_number)
          ->orderBy('order_number','asc')
          ->pluck('month_name', 'id');
        
        $dueMonthIds = $dueMonths->keys()->toArray();

        
        
        $query = ClassWiseStudentData::with([
            'student',
            'session',
            'class',
            'version',
            'section'
        ]);

        if ($request->has('id_card_number') && $request->id_card_number) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('id_card_number', $request->id_card_number);
            });
        } else {
            if ($request->has('session_id')) {
                $query->where('session_id', $request->session_id);
            }
            if ($request->has('version_id')) {
                $query->where('version_id', $request->version_id);
            }
            if ($request->has('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            if ($request->has('section_id')) {
                $query->where('section_id', $request->section_id);
            }
            if ($request->has('roll')) {
                $query->where('class_roll', $request->roll);
            }
        }

        $classWiseStudent = $query->first();
        //CommonModel::updateFeePayment($classWiseStudent->id);

        if (!$classWiseStudent) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $studentDueAmount = StudentWiseFeeAssign::where('due_amount', '>', 0)
          ->where('class_wise_student_id', $classWiseStudent->id) 
          ->whereIn('month_id', $dueMonthIds) 
          ->get();
        
        $dueFees = [];
        $headIds = [];
        $headWiseTotal = [];
        foreach($studentDueAmount as $fees){
            $dueFees[$fees->month_id][$fees->head_id] = $fees;
            $headIds[] = $fees->head_id;

            if(isset($headWiseTotal[$fees->head_id])){
                $headWiseTotal[$fees->head_id]+=$fees->due_amount;
            } else {
                $headWiseTotal[$fees->head_id]=$fees->due_amount;
            }
        }
        $headIds = array_values(array_unique($headIds));

        

        $feeHeads = DB::table('fee_heads')
            ->whereIn('id', $headIds)
            ->get()
            ->keyBy('id');


        $studentData = [
            'class_wise_student_id' => $classWiseStudent->id,
            'student_name' => $classWiseStudent->student->student_name ?? 'Unknown',
            'id_card_number' => $classWiseStudent->student->id_card_number ?? null,
            'mobile_number' => $classWiseStudent->student->mobile_number ?? null,
            'roll' => $classWiseStudent->class_roll,
            'student_image' => $classWiseStudent->student->student_image ?? null,
            'session_id' => $classWiseStudent->session_id,
            'class_id' => $classWiseStudent->class_id,
            'version_id' => $classWiseStudent->version_id,
            'section_id' => $classWiseStudent->section_id,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $studentData,
                'dueFees' => $dueFees,
                'feeHeads' => $feeHeads,
                'dueMonths' => $dueMonths,
                'headWiseTotal' => $headWiseTotal 
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'account_id' => 'required|exists:cash_banks,id',
            'class_wise_student_id' => 'required|exists:class_wise_student_data,id',
            'month_id' => 'required|exists:month_management,id',
            'total_paid_amount' => 'required|numeric|min:0',
            'payment_details' => 'required|array|min:1',
            'payment_details.*.head_id' => 'required|exists:fee_heads,id',
            'payment_details.*.paid_amount' => 'required|numeric|min:0',
        ]);

        

        try {
            DB::beginTransaction();

            $payment = new Payment;
            $payment->payment_date = $request->payment_date;
            $payment->account_id = $request->account_id;
            $payment->class_wise_student_id = $request->class_wise_student_id;
            $payment->month_id = $request->month_id;
            $payment->total_paid_amount = $request->total_paid_amount;
            $payment->save();

            foreach ($request->payment_details as $detail) {
                if ($detail['paid_amount'] > 0) {
                    $paymentDetail = new PaymentDetails;
                    $paymentDetail->payment_id = $payment->id;
                    $paymentDetail->head_id = $detail['head_id'];
                    $paymentDetail->paid_amount = $detail['paid_amount'];
                    $paymentDetail->save();
                }
            }

            CommonModel::updateFeePayment($request->class_wise_student_id);

            $accountTransaction = new AccountTransaction;
            $accountTransaction->transaction_type = 'Fee Payment';
            $accountTransaction->voucher_type = 'CREDIT';
            $accountTransaction->date = $request->payment_date;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->reference_id = $payment->id;
            $accountTransaction->description = 'Fee payment received';
            $accountTransaction->amount = $request->total_paid_amount;
            $accountTransaction->validity = 1;
            $accountTransaction->save();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            $payment->load([
                'classWiseStudent.student',
                'month',
                'account',
                'paymentDetails.feeHead'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => $payment
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $payment = Payment::with([
                'classWiseStudent.student',
                'classWiseStudent.session',
                'classWiseStudent.class',
                'classWiseStudent.version',
                'classWiseStudent.section',
                'month',
                'account'
            ])->findOrFail($id);

            $groupedDetails = PaymentDetails::where('payment_id', $id)
                ->join('fee_heads', 'payment_details.head_id', '=', 'fee_heads.id')
                ->selectRaw('
                    payment_details.head_id,
                    fee_heads.head_name,
                    SUM(payment_details.paid_amount) as paid_amount
                ')
                ->groupBy('payment_details.head_id', 'fee_heads.head_name')
                ->get();

            $payment->payment_details = $groupedDetails->map(function($detail) {
                return [
                    'head_id' => $detail->head_id,
                    'paid_amount' => $detail->paid_amount,
                    'fee_head' => [
                        'head_name' => $detail->head_name
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $payment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $payment = Payment::with([
    //             'classWiseStudent.student',
    //             'classWiseStudent.session',
    //             'classWiseStudent.class',
    //             'classWiseStudent.version',
    //             'classWiseStudent.section',
    //             'month',
    //             'account',
    //             'paymentDetails.feeHead'
    //         ])->findOrFail($id);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $payment
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment not found',
    //             'error' => $e->getMessage()
    //         ], 404);
    //     }
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'account_id' => 'required|exists:cash_banks,id',
            'total_paid_amount' => 'required|numeric|min:0',
            'payment_details' => 'required|array|min:1',
            'payment_details.*.head_id' => 'required|exists:fee_heads,id',
            'payment_details.*.paid_amount' => 'required|numeric|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            $payment = Payment::findOrFail($id);
            
            $existingTransaction = AccountTransaction::where('reference_id', $payment->id)
                ->where('transaction_type', 'Fee Payment')
                ->where('validity', 1)
                ->first();
            
            $payment->payment_date = $request->payment_date;
            $payment->account_id = $request->account_id;
            $payment->total_paid_amount = $request->total_paid_amount;
            $payment->save();
            
            PaymentDetails::where('payment_id', $payment->id)->delete();
            
            foreach ($request->payment_details as $detail) {
                if ($detail['paid_amount'] > 0) {
                    $paymentDetail = new PaymentDetails;
                    $paymentDetail->payment_id = $payment->id;
                    $paymentDetail->head_id = $detail['head_id'];
                    $paymentDetail->paid_amount = $detail['paid_amount'];
                    $paymentDetail->save();
                }
            }
            
            CommonModel::updateFeePayment($payment->class_wise_student_id);
            
            if ($existingTransaction) {
                $existingTransaction->date = $request->payment_date;
                $existingTransaction->account_id = $request->account_id;
                $existingTransaction->description = 'Fee payment received (updated)';
                $existingTransaction->amount = $request->total_paid_amount;
                $existingTransaction->updated_at = now();
                $existingTransaction->save();
            } else {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->transaction_type = 'Fee Payment';
                $accountTransaction->voucher_type = 'CREDIT';
                $accountTransaction->date = $request->payment_date;
                $accountTransaction->account_id = $request->account_id;
                $accountTransaction->reference_id = $payment->id;
                $accountTransaction->description = 'Fee payment received (updated)';
                $accountTransaction->amount = $request->total_paid_amount;
                $accountTransaction->validity = 1;
                $accountTransaction->save();
            }
            
            CommonModel::updateAccountCurrentBalance();
            
            DB::commit();
            
            $payment->load([
                'classWiseStudent.student',
                'month',
                'account',
                'paymentDetails.feeHead'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);

            AccountTransaction::where('reference_id', $payment->id)
                ->where('transaction_type', 'Fee Payment')
                ->update([
                    'validity' => 0,
                    'deleted_at' => now(),
                    'deleted_by' => auth()->id(),
                ]);

            PaymentDetails::where('payment_id', $payment->id)->delete();
            
            $payment->delete();

            CommonModel::updateAccountCurrentBalance();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // For React App
    public function getStudentFeeSummary($classWiseStudentId)
    {
        try {
            $classWiseStudent = ClassWiseStudentData::findOrFail($classWiseStudentId);
            
            $feeData = StudentWiseFeeAssign::where('class_wise_student_id', $classWiseStudentId)
                ->whereHas('feeAssign', function($query) use ($classWiseStudent) {
                    $query->where('session_id', $classWiseStudent->session_id);
                })
                ->selectRaw('
                    SUM(payble_amount) as total_amount,
                    SUM(paid_amount) as total_paid,
                    SUM(due_amount) as total_due
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_amount' => floatval($feeData->total_amount ?? 0),
                    'total_paid' => floatval($feeData->total_paid ?? 0),
                    'total_due' => floatval($feeData->total_due ?? 0),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fee summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}