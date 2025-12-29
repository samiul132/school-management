<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class FeeHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'head_name',
        'order_number',
        'status',
        'head_type',
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

    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class, 'head_id');
    }
}
