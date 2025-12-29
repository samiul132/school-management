<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class CashTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'from_account',
        'to_account',
        'amount',
        'description',
        'school_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
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

    public function fromAccount()
    {
        return $this->belongsTo(CashBank::class, 'from_account');
    }

    public function toAccount()
    {
        return $this->belongsTo(CashBank::class, 'to_account');
    }
}
