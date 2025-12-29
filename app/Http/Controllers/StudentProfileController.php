<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\ClassWiseStudentData;
use App\Models\ClassManagement;
use App\Models\SectionManagement;
use App\Models\VersionManagement;
use App\Models\ShiftManagement;
use App\Models\SessionManagement;
use App\Models\CommonModel;
use App\Models\User;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Validation\Rule;

class StudentProfileController extends Controller
{
    private $imageManager;
    
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }
    
    public function index(Request $request)
    {
        try {
            $query = StudentProfile::hasActiveSessionData()
                ->with(['classWiseData' => function($query) {
                    $activeSession = SessionManagement::where('status', 'active')
                        //->where('school_id', auth()->user()->school_id)
                        ->first();
                    
                    if ($activeSession) {
                        $query->where('session_id', $activeSession->id)
                            ->with(['class', 'version', 'session', 'section', 'shift']);
                    }
                }]);
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('id_card_number', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('father_mobile_number', 'like', "%{$search}%");
                });
            }
            
            $perPage = $request->get('per_page', 15);
            
            $students = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            foreach ($students as $student) {
                if ($student->student_image) {
                    $student->student_image_url = asset('assets/images/student_images/' . $student->student_image);
                }
                if ($student->tc_image) {
                    $student->tc_image_url = asset('assets/images/tc_images/' . $student->tc_image);
                }
                
                $student->active_session_data = $student->active_session_class_data;
            }
            
            return response()->json([
                'success' => true,
                'data' => $students,
                'message' => 'Students with active session data retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validator = Validator::make($request->all(), [
                'student_name' => 'required|string|max:255', 
                'id_card_number' => [
                    'nullable',
                    'string',
                    Rule::unique('student_profiles', 'id_card_number')
                        ->where('school_id', auth()->user()->school_id)
                ],
                'student_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'religion' => 'nullable|string',
                'birth_certificate_number' => 'nullable|string',
                'nationality' => 'nullable|string',
                'blood_group' => 'nullable|string',
                'mobile_number' => 'nullable|string',
                'email' => 'nullable|email|unique:users,email',
                'father_name' => 'nullable|string|max:255',
                'father_profession' => 'nullable|string',
                'father_mobile_number' => 'nullable|string',
                'mother_name' => 'nullable|string|max:255',
                'mother_profession' => 'nullable|string',
                'mother_mobile_number' => 'nullable|string',
                'local_guardian_name' => 'nullable|string|max:255',
                'local_guardian_profession' => 'nullable|string',
                'local_guardian_mobile_number' => 'nullable|string',
                'present_village' => 'nullable|string',
                'present_post_office' => 'nullable|string',
                'present_upazila_id' => 'nullable|string',
                'present_district_id' => 'nullable|string',
                'permanent_village' => 'nullable|string',
                'permanent_post_office' => 'nullable|string',
                'permanent_upazila_id' => 'nullable|string',
                'permanent_district_id' => 'nullable|string',
                'previous_institute_name' => 'nullable|string',
                'previous_institute_class' => 'nullable|string',
                'previous_institute_section' => 'nullable|string',
                'previous_institute_roll' => 'nullable|string',
                'previous_institute_result' => 'nullable|string',
                'tc_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

                'academic_data.class_id' => 'nullable|exists:class_management,id',
                'academic_data.version_id' => 'nullable|exists:version_management,id',
                'academic_data.session_id' => 'nullable|exists:session_management,id',
                'academic_data.section_id' => 'nullable|exists:section_management,id',
                'academic_data.shift_id' => 'nullable|exists:shift_management,id',
                'academic_data.class_roll' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $studentImageName = null;
            if ($request->hasFile('student_image')) {
                $studentImage = $request->file('student_image');
                $studentImageName = 'student_image_' . time() . '_' . Str::random(10) . '.' . $studentImage->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/student_images');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $studentImage->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $studentImageName);
            }
            
            $tcImageName = null;
            if ($request->hasFile('tc_image')) {
                $tcImage = $request->file('tc_image');
                $tcImageName = 'tc_image_' . time() . '_' . Str::random(10) . '.' . $tcImage->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/tc_images');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $tcImage->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $tcImageName);
            }
            
            $schoolId = auth()->check() ? auth()->user()->school_id : null;
            
            $student = new StudentProfile();
            $student->school_id = $schoolId;
            $student->student_name = $request->student_name;
            $student->id_card_number = $request->id_card_number;
            $student->student_image = $studentImageName;
            $student->date_of_birth = $request->date_of_birth;
            $student->gender = $request->gender;
            $student->religion = $request->religion;
            $student->birth_certificate_number = $request->birth_certificate_number;
            $student->nationality = $request->nationality;
            $student->blood_group = $request->blood_group;
            $student->mobile_number = $request->mobile_number;
            $student->email = $request->email;
            $student->father_name = $request->father_name;
            $student->father_profession = $request->father_profession;
            $student->father_mobile_number = $request->father_mobile_number;
            $student->mother_name = $request->mother_name;
            $student->mother_profession = $request->mother_profession;
            $student->mother_mobile_number = $request->mother_mobile_number;
            $student->local_guardian_name = $request->local_guardian_name;
            $student->local_guardian_profession = $request->local_guardian_profession;
            $student->local_guardian_mobile_number = $request->local_guardian_mobile_number;
            $student->present_village = $request->present_village;
            $student->present_post_office = $request->present_post_office;
            $student->present_upazila_id = $request->present_upazila_id;
            $student->present_district_id = $request->present_district_id;
            $student->permanent_village = $request->permanent_village;
            $student->permanent_post_office = $request->permanent_post_office;
            $student->permanent_upazila_id = $request->permanent_upazila_id;
            $student->permanent_district_id = $request->permanent_district_id;
            $student->previous_institute_name = $request->previous_institute_name;
            $student->previous_institute_class = $request->previous_institute_class;
            $student->previous_institute_section = $request->previous_institute_section;
            $student->previous_institute_roll = $request->previous_institute_roll;
            $student->previous_institute_result = $request->previous_institute_result;
            $student->tc_image = $tcImageName;
            $student->save();
        
            $autoPassword = Str::random(8);
            
            $user = new User();
            $user->school_id = $schoolId;
            $user->student_id = $student->id;
            $user->name = $request->student_name;
            $user->user_name = $request->id_card_number ?? null;
            $user->email = $request->email ?? null;
            $user->password = $autoPassword; 
            $user->type = 'student';
            $user->save();

            $academicData = $request->input('academic_data');
            
            $activeSession = SessionManagement::where('status', 'active')
                ->where('school_id', $schoolId)
                ->first();
            
            $hasAcademicData = false;
            if ($academicData && is_array($academicData)) {
                $academicFields = ['class_id', 'version_id', 'session_id', 'section_id', 'shift_id', 'class_roll'];
                
                foreach ($academicFields as $field) {
                    if (!empty($academicData[$field])) {
                        $hasAcademicData = true;
                        break;
                    }
                }
            }
            
            $sessionId = null;
            if ($hasAcademicData && isset($academicData['session_id'])) {
                $sessionId = $academicData['session_id'];
            } elseif ($activeSession) {
                $sessionId = $activeSession->id;
            }
            
            if ($sessionId) {
                $classWiseData = new ClassWiseStudentData();
                $classWiseData->class_id = $hasAcademicData ? ($academicData['class_id'] ?? null) : null;
                $classWiseData->version_id = $hasAcademicData ? ($academicData['version_id'] ?? null) : null;
                $classWiseData->session_id = $sessionId;
                $classWiseData->section_id = $hasAcademicData ? ($academicData['section_id'] ?? null) : null;
                $classWiseData->shift_id = $hasAcademicData ? ($academicData['shift_id'] ?? null) : null;
                $classWiseData->class_roll = $hasAcademicData ? ($academicData['class_roll'] ?? null) : null;
                $classWiseData->student_id = $student->id;
                $classWiseData->save();

                CommonModel::studentWiseSubjectAssign($classWiseData->id);
                
                \Log::info('Academic data saved:', [
                    'session_id' => $sessionId,
                    'is_active_session' => !($hasAcademicData && isset($academicData['session_id'])),
                    'has_other_academic_data' => $hasAcademicData,
                    'data' => $classWiseData->toArray()
                ]);
            }
            
            DB::commit();
            
            $student->load(['classWiseData' => function($query) {
                $query->with(['class', 'version', 'session', 'section']);
            }]);
            
            if ($student->student_image) {
                $student->student_image_url = asset('assets/images/student_images/' . $student->student_image);
            }
            if ($student->tc_image) {
                $student->tc_image_url = asset('assets/images/tc_images/' . $student->tc_image);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Student admission created successfully',
                'data' => $student,
                'user_data' => [
                    'email' => $user->email,
                    'password' => $autoPassword,
                    'type' => $user->type
                ]
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student admission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $student = StudentProfile::with([
                'classWiseData' => function($query) {
                    $query->with(['class', 'version', 'session', 'section', 'shift']);
                },
                'presentDistrict',
                'presentUpazila',  
                'permanentDistrict', 
                'permanentUpazila'   
            ])->find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            if ($student->student_image) {
                $student->student_image_url = asset('assets/images/student_images/' . $student->student_image);
            }
            if ($student->tc_image) {
                $student->tc_image_url = asset('assets/images/tc_images/' . $student->tc_image);
            }

            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => 'Student retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        
        try {
            $student = StudentProfile::find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'student_name' => 'required|string|max:255',
                'id_card_number' => [
                    'nullable',
                    'string',
                    Rule::unique('student_profiles', 'id_card_number')
                        ->where('school_id', auth()->user()->school_id)
                        ->ignore($id)
                ],
                'student_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'religion' => 'nullable|string',
                'birth_certificate_number' => 'nullable|string',
                'nationality' => 'nullable|string',
                'blood_group' => 'nullable|string',
                'mobile_number' => 'nullable|string',
                'email' => 'nullable|email|unique:users,email,' . $student->id . ',student_id',
                'father_name' => 'nullable|string|max:255',
                'father_profession' => 'nullable|string',
                'father_mobile_number' => 'nullable|string',
                'mother_name' => 'nullable|string|max:255',
                'mother_profession' => 'nullable|string',
                'mother_mobile_number' => 'nullable|string',
                'local_guardian_name' => 'nullable|string|max:255',
                'local_guardian_profession' => 'nullable|string',
                'local_guardian_mobile_number' => 'nullable|string',
                'present_village' => 'nullable|string',
                'present_post_office' => 'nullable|string',
                'present_upazila_id' => 'nullable|string',
                'present_district_id' => 'nullable|string',
                'permanent_village' => 'nullable|string',
                'permanent_post_office' => 'nullable|string',
                'permanent_upazila_id' => 'nullable|string',
                'permanent_district_id' => 'nullable|string',
                'previous_institute_name' => 'nullable|string',
                'previous_institute_class' => 'nullable|string',
                'previous_institute_section' => 'nullable|string',
                'previous_institute_roll' => 'nullable|string',
                'previous_institute_result' => 'nullable|string',
                'tc_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'remove_student_image' => 'nullable|boolean',
                'remove_tc_image' => 'nullable|boolean',

                'academic_data.class_id' => 'nullable|exists:class_management,id',
                'academic_data.version_id' => 'nullable|exists:version_management,id',
                'academic_data.session_id' => 'nullable|exists:session_management,id',
                'academic_data.section_id' => 'nullable|exists:section_management,id',
                'academic_data.shift_id' => 'nullable|exists:shift_management,id',
                'academic_data.class_roll' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('remove_student_image') && $request->remove_student_image) {
                if ($student->student_image) {
                    $oldImagePath = public_path('assets/images/student_images/' . $student->student_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
                $student->student_image = null;
            } 
            elseif ($request->hasFile('student_image')) {
                $studentImage = $request->file('student_image');
                
                if ($student->student_image) {
                    $oldImagePath = public_path('assets/images/student_images/' . $student->student_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                $studentImageName = 'student_image_' . time() . '_' . Str::random(10) . '.' . $studentImage->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/student_images');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $studentImage->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $studentImageName);
                
                $student->student_image = $studentImageName;
            }
            
            if ($request->has('remove_tc_image') && $request->remove_tc_image) {
                if ($student->tc_image) {
                    $oldImagePath = public_path('assets/images/tc_images/' . $student->tc_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
                $student->tc_image = null;
            } 
            elseif ($request->hasFile('tc_image')) {
                $tcImage = $request->file('tc_image');
                
                if ($student->tc_image) {
                    $oldImagePath = public_path('assets/images/tc_images/' . $student->tc_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                $tcImageName = 'tc_image_' . time() . '_' . Str::random(10) . '.' . $tcImage->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/tc_images');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $tcImage->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $tcImageName);
                
                $student->tc_image = $tcImageName;
            }
            
            $student->student_name = $request->student_name;
            $student->id_card_number = $request->id_card_number;
            $student->date_of_birth = $request->date_of_birth;
            $student->gender = $request->gender;
            $student->religion = $request->religion;
            $student->birth_certificate_number = $request->birth_certificate_number;
            $student->nationality = $request->nationality;
            $student->blood_group = $request->blood_group;
            $student->mobile_number = $request->mobile_number;
            $student->email = $request->email;
            $student->father_name = $request->father_name;
            $student->father_profession = $request->father_profession;
            $student->father_mobile_number = $request->father_mobile_number;
            $student->mother_name = $request->mother_name;
            $student->mother_profession = $request->mother_profession;
            $student->mother_mobile_number = $request->mother_mobile_number;
            $student->local_guardian_name = $request->local_guardian_name;
            $student->local_guardian_profession = $request->local_guardian_profession;
            $student->local_guardian_mobile_number = $request->local_guardian_mobile_number;
            $student->present_village = $request->present_village;
            $student->present_post_office = $request->present_post_office;
            $student->present_upazila_id = $request->present_upazila_id;
            $student->present_district_id = $request->present_district_id;
            $student->permanent_village = $request->permanent_village;
            $student->permanent_post_office = $request->permanent_post_office;
            $student->permanent_upazila_id = $request->permanent_upazila_id;
            $student->permanent_district_id = $request->permanent_district_id;
            $student->previous_institute_name = $request->previous_institute_name;
            $student->previous_institute_class = $request->previous_institute_class;
            $student->previous_institute_section = $request->previous_institute_section;
            $student->previous_institute_roll = $request->previous_institute_roll;
            $student->previous_institute_result = $request->previous_institute_result;
            $student->save();
            
            $user = User::where('student_id', $student->id)->first();
            if ($user) {
                $user->name = $request->student_name;
                $user->user_name = $request->id_card_number ?? null;
                $user->email = $request->email ?? null;
                $user->save();
            }
            
            $academicData = $request->input('academic_data');
            
            if ($academicData && is_array($academicData)) {
                $hasAcademicData = false;
                $academicFields = ['class_id', 'version_id', 'session_id', 'section_id', 'shift_id', 'class_roll'];
                
                foreach ($academicFields as $field) {
                    if (isset($academicData[$field]) && !empty($academicData[$field])) {
                        $hasAcademicData = true;
                        break;
                    }
                }
                
                if ($hasAcademicData) {
                    $classWiseData = ClassWiseStudentData::where('student_id', $student->id)
                        ->where(function($query) use ($academicData) {
                            if (isset($academicData['session_id']) && !empty($academicData['session_id'])) {
                                $query->where('session_id', $academicData['session_id']);
                            }
                        })
                        ->first();
                    
                    if ($classWiseData) {
                        $classWiseData->class_id = $academicData['class_id'] ?? $classWiseData->class_id;
                        $classWiseData->version_id = $academicData['version_id'] ?? $classWiseData->version_id;
                        $classWiseData->session_id = $academicData['session_id'] ?? $classWiseData->session_id;
                        $classWiseData->section_id = $academicData['section_id'] ?? $classWiseData->section_id;
                        $classWiseData->shift_id = $academicData['shift_id'] ?? $classWiseData->shift_id;
                        $classWiseData->class_roll = $academicData['class_roll'] ?? $classWiseData->class_roll;
                        $classWiseData->save();
                    } else {
                        $classWiseData = new ClassWiseStudentData();
                        $classWiseData->student_id = $student->id;
                        $classWiseData->class_id = $academicData['class_id'] ?? null;
                        $classWiseData->version_id = $academicData['version_id'] ?? null;
                        $classWiseData->session_id = $academicData['session_id'] ?? null;
                        $classWiseData->section_id = $academicData['section_id'] ?? null;
                        $classWiseData->shift_id = $academicData['shift_id'] ?? null;
                        $classWiseData->class_roll = $academicData['class_roll'] ?? null;
                        $classWiseData->save();
                    }
                } else {
                    \Log::info('No academic data provided in request, keeping existing data');
                }
            }
            
            DB::commit();
            
            $student->load(['classWiseData' => function($query) {
                $query->with(['class', 'version', 'session', 'section', 'shift']);
            }]);
            
            if ($student->student_image) {
                $student->student_image_url = asset('assets/images/student_images/' . $student->student_image);
            }
            if ($student->tc_image) {
                $student->tc_image_url = asset('assets/images/tc_images/' . $student->tc_image);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Student admission updated successfully',
                'data' => $student
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating student: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student admission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        
        try {
            $student = StudentProfile::find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }
            
            if ($student->student_image) {
                $imagePath = public_path('assets/images/student_images/' . $student->student_image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            
            if ($student->tc_image) {
                $tcImagePath = public_path('assets/images/tc_images/' . $student->tc_image);
                if (File::exists($tcImagePath)) {
                    File::delete($tcImagePath);
                }
            }
            
            User::where('student_id', $student->id)->delete();
            
            ClassWiseStudentData::where('student_id', $student->id)->delete();
            
            $student->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Student admission deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student admission',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getDropdownData()
    {
        try {
            $classes = ClassManagement::where('status', 'active')->get(['id', 'class_name']);
            $versions = VersionManagement::where('status', 'active')->get(['id', 'version_name']);
            $sessions = SessionManagement::where('status', 'active')->get(['id', 'session_name']);
            $sections = SectionManagement::where('status', 'active')->get(['id', 'section_name']);
            $shifts = ShiftManagement::where('status', 'active')->get(['id', 'shift_name']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'classes' => $classes,
                    'versions' => $versions,
                    'sessions' => $sessions,
                    'sections' => $sections,
                    'shifts' => $shifts
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dropdown data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Reset password for a student
    public function resetPassword(Request $request, $id)
    {
        try {
            $student = StudentProfile::find($id);
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }
            
            $user = User::where('student_id', $student->id)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User account not found for this student'
                ], 404);
            }
            
            $newPassword = Str::random(8);
            
            $user->password = $newPassword;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'data' => [
                    'new_password' => $newPassword, 
                    'email' => $user->email
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}