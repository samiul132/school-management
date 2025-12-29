<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StudentWiseFeeAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'assign_id',
        'head_id',
        'class_wise_student_id',
        'amount',
        'waver_amount',
        'paid_amount',
        'due_amount',
        'payble_amount',
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
                $builder->where('student_wise_fee_assigns.school_id', Auth::user()->school_id);
            }
        });
    }

    public static function bulkInsert(array $data)
    {
        if (Auth::check()) {
            $schoolId = Auth::user()->school_id;
            $timestamp = now();
            
            $data = array_map(function($item) use ($schoolId, $timestamp) {
                $item['school_id'] = $schoolId;
                $item['created_at'] = $item['created_at'] ?? $timestamp;
                $item['updated_at'] = $item['updated_at'] ?? $timestamp;
                return $item;
            }, $data);
        }
        
        return self::insert($data);
    }

    public function student()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }

    public function FeeAssign()
    {
        return $this->belongsTo(FeeAssign::class, 'assign_id');
    }
    
    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'head_id');
    }

    public function classWiseStudentData()
    {
        return $this->belongsTo(ClassWiseStudentData::class, 'class_wise_student_id');
    }   
}
