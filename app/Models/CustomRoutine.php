<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CustomRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'date',
        'periods',
        'teacher_wise_data',
        'class_wise_data',
    ];

    protected $casts = [
        'periods' => 'array',
        'teacher_wise_data' => 'array',
        'class_wise_data' => 'array',
        'date' => 'date',
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
}
