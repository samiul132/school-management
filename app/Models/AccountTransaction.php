<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class AccountTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_type',
        'voucher_type',
        'date',
        'school_id',
        'account_id',
        'reference_id',
        'description',
        'amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'validity',
    ];

    protected $attributes = [
        'validity' => 1,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->school_id = auth()->user()->school_id;
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->saveQuietly();
            }
        });

        static::addGlobalScope('school', function ($builder) {
            if (auth()->check()) {
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
    }


    public static function invalidateTransactions($referenceId, $transactionType = 'Cash Transfer')
    {
        return static::where('reference_id', $referenceId)
            ->where('transaction_type', $transactionType)
            ->update([
                'validity' => 0,
                'deleted_at' => now(),
                'deleted_by' => auth()->id(), 
            ]);
    }

    public function cashTransfer()
    {
        return $this->belongsTo(CashTransfer::class, 'reference_id');
    }

    public function account()
    {
        return $this->belongsTo(CashBank::class, 'account_id');
    }

    // public function createdBy()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // public function updatedBy()
    // {
    //     return $this->belongsTo(User::class, 'updated_by');
    // }

    // public function deletedBy()
    // {
    //     return $this->belongsTo(User::class, 'deleted_by');
    // }
}