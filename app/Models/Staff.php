<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Staff extends Model
{

    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'school_id',
        'first_name',
        'last_name',
        'fathers_name',
        'mothers_name',
        'is_teachers',
        'designation',
        'maritial_status',
        'date_of_birth',
        'joining_date',
        'phone',
        'email',
        'qualification',
        'address',
        'nid',
        'subsidiaries_id',
        'basic_salary',
        'working_hour',
        'status',
        'photo',
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

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiaries_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'staff_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'staff_id');
    }
}
