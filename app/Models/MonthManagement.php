<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class MonthManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'order_number',
        'month_name',
        'status',
        
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
                $builder->where('month_management.school_id', Auth::user()->school_id);
            }
        });
    }
}
