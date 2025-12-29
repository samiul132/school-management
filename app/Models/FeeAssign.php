<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FeeAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'version_id',
        'class_id',
        'month_id',
        'total_amount',
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

    public function details()
    {
        return $this->hasMany(FeeAssignDetails::class, 'fee_assign_id');
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

    public function month()
    {
        return $this->belongsTo(MonthManagement::class, 'month_id');
    }
}
