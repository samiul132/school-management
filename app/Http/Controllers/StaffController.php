<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Subsidiary;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class StaffController extends Controller
{
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    public function index()
    {
        try {
            $staffs = Staff::with(['subsidiary', 'designation'])->orderBy('created_at', 'desc')->get();

            $staffs->transform(function ($staff) {
                $staff->image_url = $staff->photo
                    ? asset('assets/images/staffs/' . $staff->photo)
                    : asset('images/no-image.jpg');
                return $staff;
            });

            return response()->json([
                'success' => true,
                'data' => $staffs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staffs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {

        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required',
            'last_name'    => 'required',
            'designation'  => 'required',
            'basic_salary' => 'required',
            'working_hour' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imageName = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'staff_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                $path = public_path('assets/images/staffs');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                $this->imageManager
                    ->read($image->getRealPath())
                    ->scale(width: 800)
                    ->save($path . '/' . $imageName);
            }

            $staff = new Staff();
            $staff->first_name       = $request->first_name;
            $staff->last_name        = $request->last_name;
            $staff->fathers_name     = $request->fathers_name;
            $staff->mothers_name     = $request->mothers_name;
            $staff->is_teachers      = $request->is_teachers;
            $staff->designation      = $request->designation;
            $staff->maritial_status  = $request->maritial_status;
            $staff->date_of_birth    = $request->date_of_birth;
            $staff->joining_date     = $request->joining_date;
            $staff->phone            = $request->phone;
            $staff->email            = $request->email;
            $staff->qualification    = $request->qualification;
            $staff->address          = $request->address;
            $staff->nid              = $request->nid;
            $staff->subsidiaries_id  = $request->subsidiaries_id;
            $staff->basic_salary     = $request->basic_salary;
            $staff->working_hour     = $request->working_hour;
            $staff->status     = $request->status;
            $staff->photo            = $imageName;
            $staff->save();

            $staff->load(['subsidiary', 'designation']);
            

            $staff->image_url = $imageName
                ? asset('assets/images/staffs/' . $imageName)
                : asset('images/no-image.jpg');

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully',
                'data' => $staff
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $staff = Staff::with(['subsidiary', 'designation'])->find($id);

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'staff not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $staff
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required',
            'last_name'    => 'required',
            'designation'  => 'required',
            'basic_salary' => 'required',
            'working_hour' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $staff = Staff::find($id);
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found'
                ], 404);
            }

            $imageName = $staff->photo;

            if ($request->has('remove_photo') && $request->remove_photo) {
                if ($imageName && File::exists(public_path('assets/images/staffs/' . $imageName))) {
                    File::delete(public_path('assets/images/staffs/' . $imageName));
                }
                $imageName = null;
            }

            if ($request->hasFile('image')) {
                if ($imageName && File::exists(public_path('assets/images/staffs/' . $imageName))) {
                    File::delete(public_path('assets/images/staffs/' . $imageName));
                }

                $image = $request->file('image');
                $imageName = 'staff_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                $path = public_path('assets/images/staffs');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                $this->imageManager
                    ->read($image->getRealPath())
                    ->scale(width: 800)
                    ->save($path . '/' . $imageName);
            }

            $staff->first_name       = $request->first_name;
            $staff->last_name        = $request->last_name;
            $staff->fathers_name     = $request->fathers_name;
            $staff->mothers_name     = $request->mothers_name;
            $staff->is_teachers      = $request->is_teachers;
            $staff->designation      = $request->designation;
            $staff->maritial_status  = $request->maritial_status;
            $staff->date_of_birth    = $request->date_of_birth;
            $staff->joining_date     = $request->joining_date;
            $staff->phone            = $request->phone;
            $staff->email            = $request->email;
            $staff->qualification    = $request->qualification;
            $staff->address          = $request->address;
            $staff->nid              = $request->nid;
            $staff->subsidiaries_id  = $request->subsidiaries_id;
            $staff->basic_salary     = $request->basic_salary;
            $staff->working_hour     = $request->working_hour;
            $staff->status           = $request->status;
            $staff->photo            = $imageName;
            $staff->save();

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
                'data' => $staff
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $staff = Staff::find($id);
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found'
                ], 404);
            }

            if ($staff->photo && File::exists(public_path('assets/images/staffs/' . $staff->photo))) {
                File::delete(public_path('assets/images/staffs/' . $staff->photo));
            }

            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete staff',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
