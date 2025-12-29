<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'staff_id',
        'application_date',
        'leave_from_date',
        'leave_to_date',
        'reason_of_leave',
        'total_leave',
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
                $builder->where('school_id', Auth::user()->school_id);
            }
        });
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function leaveDetails()
    {
        return $this->hasMany(LeaveDetails::class, 'leave_id');
    }
}