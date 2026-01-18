<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_name',
        'id_card_number',
        'student_image',
        'date_of_birth',
        'gender',
        'religion',
        'birth_certificate_number',
        'nationality',
        'blood_group',
        'mobile_number',
        'email',
        'father_name',
        'father_profession',
        'father_mobile_number',
        'mother_name',
        'mother_profession',
        'mother_mobile_number',
        'local_guardian_name',
        'local_guardian_profession',
        'local_guardian_mobile_number',
        'present_village',
        'present_post_office',
        'present_upazila_id',
        'present_district_id',
        'permanent_village',
        'permanent_post_office',
        'permanent_upazila_id',
        'permanent_district_id',
        'previous_institute_name',
        'previous_institute_class',
        'previous_institute_section',
        'previous_institute_roll',
        'previous_institute_result',
        'tc_image',
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

    public function user()
    {
        return $this->hasOne(User::class, 'student_id');
    }

    public function classWiseData()
    {
        return $this->hasMany(ClassWiseStudentData::class, 'student_id');
    }

    public function scopeHasActiveSessionData($query)
    {
        $activeSession = SessionManagement::where('status', 'active')
            ->where('school_id', auth()->user()->school_id)
            ->first();
        
        if (!$activeSession) {
            return $query->whereNull('id'); 
        }
        
        return $query->whereHas('classWiseData', function($q) use ($activeSession) {
            $q->where('session_id', $activeSession->id);
        });
    }

    public function presentDistrict()
    {
        return $this->belongsTo(District::class, 'present_district_id');
    }

    public function presentUpazila()
    {
        return $this->belongsTo(Upazila::class, 'present_upazila_id');
    }

    public function permanentDistrict()
    {
        return $this->belongsTo(District::class, 'permanent_district_id');
    }

    public function permanentUpazila()
    {
        return $this->belongsTo(Upazila::class, 'permanent_upazila_id');
    }
}
