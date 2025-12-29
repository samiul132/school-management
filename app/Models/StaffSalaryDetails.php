<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StaffSalaryDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'salary_id',
        'staff_id',
        'gross_salary',
        'over_time_salary',
        'advance_payment',
        'payable_salary',
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

    public function salary()
    {
        return $this->belongsTo(StaffSalary::class, 'salary_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
