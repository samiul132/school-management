<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SubjectAssignStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'assign_id',
        'class_wise_student_id',
        'subject_id',
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

    public function assign()
    {
        return $this->belongsTo(SubjectAssign::class, 'assign_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classWiseStudent()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }
}
