<?php

namespace App\Http\Controllers;

use App\Models\SchoolSettings;
use App\Models\SessionManagement;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Auth;

class SchoolSettingsController extends Controller
{
    private $imageManager;
    
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    private function generateStudentId($schoolId)
    {
        $schoolCode = 'IQRA'; 
        
        $activeSession = SessionManagement::where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();

        if (!$activeSession) {
            throw new \Exception('No active session found.');
        }

        $sessionYear = $activeSession->session_name;

        $lastStudent = StudentProfile::where('school_id', $schoolId)
            ->where('id_card_number', 'like', $schoolCode . $sessionYear . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent && $lastStudent->id_card_number) {
            $lastNumber = (int) substr($lastStudent->id_card_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $schoolCode . $sessionYear . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function makeStudentId($schoolId)
    {
        $instance = new self();
        return $instance->generateStudentId($schoolId);
    }

    public function getNextStudentId(): JsonResponse
    {
        try {
            $schoolId = auth()->user()->school_id;
            $nextId = $this->generateStudentId($schoolId);

            return response()->json([
                'success' => true,
                'student_id' => $nextId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $settings = SchoolSettings::all();
            
            if ($settings->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No school settings found'
                ], 404);
            }

            $settings->each(function($setting) {
                if ($setting->logo) {
                    $setting->logo_url = asset('assets/images/school_logo/' . $setting->logo);
                }
            });

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch school settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:1024'
        ]);

        try {
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $logoName = null;
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = 'school_logo_' . time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/school_logo');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $logo->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $logoName);
            }

            $settings = new SchoolSettings();
            $settings->school_name = $request->school_name;
            $settings->address = $request->address;
            $settings->mobile_number = $request->mobile_number;
            $settings->email = $request->email;
            $settings->logo = $logoName;
            $settings->save();

            $autoPassword = rand(10000000, 99999999);
            
            $user = new User();
            $user->school_id = $settings->id;
            $user->type = 'ADMIN';
            $user->name = $request->school_name;
            $user->email = $request->email;
            $user->password = bcrypt($autoPassword);
            //dd($user);
            $user->save();

            if ($settings->logo) {
                $settings->logo_url = asset('assets/images/school_logo/' . $settings->logo);
            }

            return response()->json([
                'success' => true,
                'message' => 'School settings created successfully',
                'data' => $settings
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create school settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $settings = SchoolSettings::find($id);

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'School settings not found'
                ], 404);
            }

            if ($settings->logo) {
                $settings->logo_url = asset('assets/images/school_logo/' . $settings->logo);
            }

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch school settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $settings = SchoolSettings::find($id);

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'School settings not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'school_name' => 'required|string|max:255',
                'address' => 'nullable|string|max:500',
                'mobile_number' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'remove_logo' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('remove_logo') && $request->remove_logo) {
                if ($settings->logo) {
                    $oldLogoPath = public_path('assets/images/school_logo/' . $settings->logo);
                    if (File::exists($oldLogoPath)) {
                        File::delete($oldLogoPath);
                    }
                }
                $settings->logo = null;
            } 
            elseif ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                
                if ($settings->logo) {
                    $oldLogoPath = public_path('assets/images/school_logo/' . $settings->logo);
                    if (File::exists($oldLogoPath)) {
                        File::delete($oldLogoPath);
                    }
                }

                $logoName = 'school_logo_' . time() . '_' . Str::random(10) . '.' . $logo->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/school_logo');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $imagePath = $logo->getRealPath();
                $processedImage = $this->imageManager->read($imagePath);
                $processedImage->scale(width: 800);
                $processedImage->save($destinationPath . '/' . $logoName);
                
                $settings->logo = $logoName;
            }

            $settings->school_name = $request->input('school_name');
            $settings->address = $request->input('address', $settings->address);
            $settings->mobile_number = $request->input('mobile_number', $settings->mobile_number);
            $settings->email = $request->input('email', $settings->email);
            
            $settings->save();

            if ($settings->logo) {
                $settings->logo_url = asset('assets/images/school_logo/' . $settings->logo);
            }

            return response()->json([
                'success' => true,
                'message' => 'School settings updated successfully',
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            \Log::error('School Settings Update Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update school settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            if (auth()->user()->type !== 'SUPER_ADMIN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only super admin can delete school settings'
                ], 403);
            }

            $settings = SchoolSettings::find($id);

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'School settings not found'
                ], 404);
            }

            if ($settings->logo) {
                $logoPath = public_path('assets/images/school_logo/' . $settings->logo);
                if (File::exists($logoPath)) {
                    File::delete($logoPath);
                }
            }

            $settings->delete();

            return response()->json([
                'success' => true,
                'message' => 'School settings deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete school settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}