<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CashBank;
use App\Models\AccountTransaction;
use App\Models\StudentWiseFeeAssign;
use App\Models\Payment;
use App\Models\PaymentDetails;
use App\Models\MonthManagement;


class CommonModel extends Model
{
    use HasFactory;

    static function updateAccountCurrentBalance($id=null){
        
        $accList = CashBank::pluck('account_name','id');
        foreach ($accList as $accId => $account_name) {
            $totalCredit = AccountTransaction::where('account_id', $accId)->where('voucher_type', 'CREDIT')->where('validity', 1)->sum('amount');
            $totalDabit = AccountTransaction::where('account_id', $accId)->where('voucher_type', 'DEBIT')->where('validity', 1)->sum('amount');

            $acc = CashBank::find($accId);
            $acc->current_balance = $acc->opening_balance+$totalCredit-$totalDabit;
            $acc->save();
        }

        
        return true;
    }
    

    static function updateFeePayment($classWiseId){
        $studentAssignedFees = StudentWiseFeeAssign::select('student_wise_fee_assigns.*')
            ->where('class_wise_student_id', $classWiseId)
            ->leftJoin('month_management', 'month_management.id', '=', 'student_wise_fee_assigns.month_id')
            ->orderBy('month_management.order_number', 'asc')
            ->get();
        
        
        $studentPaymentDetails = PaymentDetails::select('payment_details.*', \DB::raw('SUM(payment_details.paid_amount) as total_paid_amount'))
            ->where('payments.class_wise_student_id', $classWiseId)
            ->leftJoin('payments', 'payments.id', '=', 'payment_details.payment_id')
            ->groupBy('payment_details.head_id')
            ->get();

        foreach($studentPaymentDetails as $paymentData){
            $paidAmount = $paymentData->total_paid_amount;
            foreach($studentAssignedFees as $assignedFees){
                if($paymentData->head_id==$assignedFees->head_id){
                    if($paidAmount>$assignedFees->payble_amount){
                        $paymentAmount = $assignedFees->payble_amount;
                    } else {
                        $paymentAmount = $paidAmount;
                    }
                    
                    
                    $assignedFees->paid_amount = $paymentAmount;
                    $assignedFees->due_amount = $assignedFees->payble_amount-$assignedFees->paid_amount;
                    $assignedFees->save();

                    $paidAmount -= $paymentAmount;
                }
            }
        }
        
    }

    static function studentWiseSubjectAssign($classWiseId){
        $studentInfo = ClassWiseStudentData::find($classWiseId);

        $subjectAssign = SubjectAssign::where('session_id', $studentInfo->session_id)
            ->where('version_id', $studentInfo->version_id)
            ->where('shift_id', $studentInfo->shift_id)
            ->where('class_id', $studentInfo->class_id)
            ->where('section_id', $studentInfo->section_id)
            ->first();


        if ($subjectAssign != NULL){
            $assignDetails = SubjectAssignDetail::where('assign_id', $subjectAssign->id)->get();

            foreach ($assignDetails as $assign) {
                $studentAssignDetails = new SubjectAssignStudent;
                $studentAssignDetails->assign_id = $subjectAssign->id;
                $studentAssignDetails->class_wise_student_id = $classWiseId;
                $studentAssignDetails->subject_id = $assign->subject_id;
                $studentAssignDetails->save();
            }
        }       


    }
    

}
