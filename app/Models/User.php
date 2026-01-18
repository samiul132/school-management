<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function schoolSettings()
    {
        return $this->belongsTo(SchoolSettings::class, 'school_id');
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    /* ===================== BOOT ===================== */

    protected static function booted()
    {
        // Assign school_id ONLY when a user already exists in request
        static::creating(function ($model) {

            // Skip console, seeding, migration, queue
            if (app()->runningInConsole()) {
                return;
            }

            $authUser = request()->user();

            if ($authUser && empty($model->school_id)) {
                $model->school_id = $authUser->school_id;
            }
        });
    }
}
