<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'date',
        'class_wise_student_id',
        'status',
        'in_time',
        'out_time',
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

    public function student()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassManagement::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(SectionManagement::class, 'section_id');
    }

    public function version()
    {
        return $this->belongsTo(VersionManagement::class, 'version_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionManagement::class, 'session_id');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftManagement::class, 'shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'class_wise_student_id');
    }
}
