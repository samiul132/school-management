<?php

namespace App\Http\Controllers;

use App\Models\SchoolSettings;
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

    public function index(): JsonResponse
    {
        try {
            $settings = SchoolSettings::all();
            
            $settings->transform(function ($setting) {
                if ($setting->logo) {
                    $setting->logo_url = asset('assets/images/school_logo/' . $setting->logo);
                }
                return $setting;
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
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
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
            $settings->school_id = auth()->id();
            $settings->save();

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
        if($id=='undefined'){
            $id = Auth::user()->id;
        }
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