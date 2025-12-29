<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_date',
        'school_id',
        'account_id',
        'month_id',
        'class_wise_student_id',
        'total_paid_amount',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_paid_amount' => 'decimal:2',
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
                $builder->where('payments.school_id', Auth::user()->school_id);
            }
        });
    }

    public function classWiseStudent()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }

    public function account()
    {
        return $this->belongsTo(CashBank::class, 'account_id');
    }

    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetails::class);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeForMonth($query, $monthId)
    {
        return $query->where('month_id', $monthId);
    }

    public function getFormattedPaymentDateAttribute()
    {
        return $this->payment_date->format('d M Y');
    }
    
    public function month()
    {
        return $this->belongsTo(MonthManagement::class, 'month_id');
    }
}