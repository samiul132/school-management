<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ClassRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'version_id',
        'shift_id',
        'class_id',
        'section_id',
        'number_of_periods',
        'off_day',
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

    public function session()
    {
        return $this->belongsTo(\App\Models\SessionManagement::class, 'session_id');
    }

    public function version()
    {
        return $this->belongsTo(\App\Models\VersionManagement::class, 'version_id');
    }

    public function shift()
    {
        return $this->belongsTo(\App\Models\ShiftManagement::class, 'shift_id');
    }

    public function class()
    {
        return $this->belongsTo(\App\Models\ClassManagement::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\SectionManagement::class, 'section_id');
    }

    public function details()
    {
        return $this->hasMany(ClassRoutineDetails::class, 'class_routine_id');
    }
}
