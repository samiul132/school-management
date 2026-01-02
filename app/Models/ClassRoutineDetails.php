<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ClassRoutineDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_routine_id',
        'subject_id',
        'teacher_id',
        'period_number',
        'day_name',
        'time',
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

    public function classRoutine()
    {
        return $this->belongsTo(ClassRoutine::class, 'class_routine_id');
    }

    public function subject()
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'teacher_id');
    }
}
