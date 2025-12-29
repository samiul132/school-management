<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SectionManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_name',
        'status',
        'school_id',
        'class_id',
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

    public function class()
    {
        return $this->belongsTo(ClassManagement::class, 'class_id');
    }
}
