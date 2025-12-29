<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'voucher_type',
        'account_id',
        'subsidiaries_id',
        'head_id',
        'amount',
        'description',
        'module_name',
        'ref_module_id',
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

    public function account()
    {
        return $this->belongsTo(CashBank::class, 'account_id');
    }

    public function subsidiary()
    {
        return $this->belongsTo(Subsidiary::class, 'subsidiaries_id');
    }

    public function head()
    {
        return $this->belongsTo(AccountHead::class, 'head_id');
    }
}