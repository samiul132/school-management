<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Waver extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_wise_student_id',
        'session_id',
        'version_id',
        'class_id',
        'section_id',
        'month_id',
        'head_id',
        'roll',
        'waver_type',
        'waver_amount',
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

    public function studentData()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }

    public function session()
    {
        return $this->belongsTo(SessionManagement::class, 'session_id');
    }

    public function version()
    {
        return $this->belongsTo(VersionManagement::class, 'version_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassManagement::class, 'class_id');
    }

    public function student() 
    {
        return $this->through('studentData')->has('student');
    }

    public function section()
    {
        return $this->belongsTo(SectionManagement::class, 'section_id');
    }

    public function month()
    {
        return $this->belongsTo(MonthManagement::class, 'month_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'head_id');
    }
}
