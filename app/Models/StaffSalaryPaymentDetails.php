<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StaffSalaryPaymentDetails extends Model
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

    public function salaryPayment()
    {
        return $this->belongsTo(StaffSalaryPayment::class, 'payment_id');
    }

    public function salaryDetails()
    {
        return $this->belongsTo(StaffSalaryDetails::class, 'salary_details_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
