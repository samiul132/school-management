<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StaffSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'month',
        'year',
        'total_salary',
        'total_advance',
        'total_over_time',
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

    public function salaryDetails()
    {
        return $this->hasMany(StaffSalaryDetails::class, 'salary_id');
    }
}
