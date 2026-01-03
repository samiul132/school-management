<?php

namespace App\Http\Controllers;

use App\Models\AppSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AppSliderController extends Controller
{
    private $imageManager;
    
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function index(Request $request)
    {
        try {
            $query = AppSlider::query();
            
            if ($request->has('search') && !empty($request->search)) {
                $query->where('title', 'like', "%{$request->search}%");
            }
            
            $sliders = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));
            
            foreach ($sliders->items() as $slider) {
                $slider->image_url = asset('assets/images/app_slider_image/' . $slider->image);
            }
            
            return response()->json([
                'success' => true,
                'data' => $sliders
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sliders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255', 
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $image = $request->file('image');
            $imageName = 'slider_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            $destinationPath = public_path('assets/images/app_slider_image');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $processedImage = $this->imageManager->read($image->getRealPath());
            $processedImage->scale(width: 1200);
            $processedImage->save($destinationPath . '/' . $imageName);
            
            $slider = AppSlider::create([
                'title' => $request->title,
                'image' => $imageName
            ]);
            
            $slider->image_url = asset('assets/images/app_slider_image/' . $slider->image);
            
            return response()->json([
                'success' => true,
                'message' => 'Slider created successfully',
                'data' => $slider
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create slider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $slider = AppSlider::findOrFail($id);
            $slider->image_url = asset('assets/images/app_slider_image/' . $slider->image);

            return response()->json([
                'success' => true,
                'data' => $slider
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider not found'
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $slider = AppSlider::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->hasFile('image')) {
                $oldImagePath = public_path('assets/images/app_slider_image/' . $slider->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }

                $image = $request->file('image');
                $imageName = 'slider_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                $destinationPath = public_path('assets/images/app_slider_image');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $processedImage = $this->imageManager->read($image->getRealPath());
                $processedImage->scale(width: 1200);
                $processedImage->save($destinationPath . '/' . $imageName);
                
                $slider->image = $imageName;
            }
            
            $slider->title = $request->title;
            $slider->save();
            
            $slider->image_url = asset('assets/images/app_slider_image/' . $slider->image);
            
            return response()->json([
                'success' => true,
                'message' => 'Slider updated successfully',
                'data' => $slider
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update slider',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $slider = AppSlider::findOrFail($id);
            
            $imagePath = public_path('assets/images/app_slider_image/' . $slider->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            
            $slider->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Slider deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete slider',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}