<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SubjectAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'version_id',
        'shift_id',
        'class_id',
        'section_id',
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
        return $this->belongsTo(SessionManagement::class, 'session_id');
    }
    
    public function version()
    {
        return $this->belongsTo(VersionManagement::class, 'version_id');
    }
    
    public function shift()
    {
        return $this->belongsTo(ShiftManagement::class, 'shift_id');
    }
    
    public function class()
    {
        return $this->belongsTo(ClassManagement::class, 'class_id');
    }
    
    public function section()
    {
        return $this->belongsTo(SectionManagement::class, 'section_id');
    }
    
    public function details()
    {
        return $this->hasMany(SubjectAssignDetail::class, 'assign_id');
    }
    
    public function students()
    {
        return $this->hasMany(SubjectAssignStudent::class, 'assign_id');
    }
}
