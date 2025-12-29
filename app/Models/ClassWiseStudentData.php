<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ClassWiseStudentData extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'version_id',
        'session_id',
        'section_id',
        'class_roll',
        'student_id',
        'shift_id',
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

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassManagement::class, 'class_id');
    }

    public function version()
    {
        return $this->belongsTo(VersionManagement::class, 'version_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionManagement::class, 'session_id');
    }

    public function section()
    {
        return $this->belongsTo(SectionManagement::class, 'section_id');
    }

    public function shift()
    {
        return $this->belongsTo(ShiftManagement::class, 'shift_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function studentWiseFeeAssigns()
    {
        return $this->hasMany(StudentWiseFeeAssign::class, 'class_wise_student_data_id');
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class, 'class_wise_student_id');
    }
}
