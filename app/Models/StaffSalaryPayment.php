<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StaffSalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'salary_head_id',
    ];

    protected static function boot()
    {
        parent::boot();

        
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->school_id = Auth::user()->school_id;
            }
        });

        
        static::addGlobalScope('school', function ($builder) {
            if (Auth::check()) {
                $builder->where('school_id', Auth::user()->school_id);
            }
        });
    }

    public function salarySheet()
    {
        return $this->belongsTo(StaffSalary::class, 'sheet_id');
    }

    public function account()
    {
        return $this->belongsTo(CashBank::class, 'acc_id');
    }

    public function paymentDetails()
    {
        return $this->hasMany(StaffSalaryPaymentDetails::class, 'payment_id');
    }

    public function staff()
    {
        return $this->hasOneThrough(
            Staff::class,
            StaffSalaryPaymentDetails::class,
            'payment_id', 
            'id', 
            'id', 
            'staff_id' 
        )->distinct();
    }
}
