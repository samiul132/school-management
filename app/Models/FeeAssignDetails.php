<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FeeAssignDetails extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'school_id',
        'fee_assign_id',
        'head_id',
        'amount',
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


    public function Assign()
    {
        return $this->belongsTo(FeeAssign::class, 'fee_assign_id');
    }

    public function head()
    {
        return $this->belongsTo(FeeHead::class, 'head_id');
    }
}
