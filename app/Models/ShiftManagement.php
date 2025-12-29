<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class ShiftManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'start_time',
        'end_time',
        'status',
        'school_id',
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
