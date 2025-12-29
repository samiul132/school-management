<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class AccountHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_name',
        'description',
        'status'
    ];
    protected $table = 'account_heads';

    public function orderAddItems()
    {
        return $this->hasMany(OrderAddItem::class, 'account_head_id');
    }

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
}
