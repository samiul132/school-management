<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class PaymentDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'payment_id',
        'head_id',
        'paid_amount',
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
                $builder->where('payment_details.school_id', Auth::user()->school_id);
            }
        });
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'head_id');
    }

}
