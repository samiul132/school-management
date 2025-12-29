<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SessionManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_name',
        'order_number',
        'status',
        'school_id',
    ];
    
    protected $casts = [
        'order_number' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->school_id = Auth::user()->school_id;
            }
            
            if ($model->status === 'active') {
                self::where('school_id', $model->school_id)
                    ->update(['status' => 'inactive']);
            }
        });

        static::addGlobalScope('school', function ($builder) {
            if (Auth::check()) {
                $builder->where('school_id', Auth::user()->school_id);
            }
        });

        static::updating(function ($model) {
            if ($model->status === 'active') {
                self::where('school_id', $model->school_id)
                    ->where('id', '!=', $model->id)
                    ->update(['status' => 'inactive']);
            }
        });
    }

    public function save(array $options = [])
    {
        if ($this->status === 'active') {
            $query = self::where('school_id', $this->school_id);
            
            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }
            
            $query->update(['status' => 'inactive']);
        }
        
        return parent::save($options);
    }
}