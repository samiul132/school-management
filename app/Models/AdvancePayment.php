<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'date',
        'staff_id',
        'month',
        'year',
        'amount',
        'remarks',
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

    public function transaction()
    {
        return $this->hasOne(AccountTransaction::class, 'reference_id')
            ->where('transaction_type', 'SALARY_ADVANCE');
    }
}
