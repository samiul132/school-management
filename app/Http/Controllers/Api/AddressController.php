<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function getDistricts(Request $request)
    {
        try {
            $districts = District::select('id', 'name', 'bn_name', 'division_id')
                ->orderBy('name')
                ->get()
                ->map(function ($district) {
                    return [
                        'id' => $district->id,
                        'name' => $district->name,
                        'bn_name' => $district->bn_name,
                        'division_id' => $district->division_id,
                        'display_name' => $district->name . ' (' . $district->bn_name . ')'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $districts,
                'message' => 'Districts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve districts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve districts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUpazilasByDistrict(Request $request, $districtId)
    {
        try {
            Log::info('Fetching upazilas for district: ' . $districtId);
            
            $districtExists = District::where('id', $districtId)->exists();
            if (!$districtExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'District not found',
                    'data' => []
                ], 404);
            }

            $upazilas = Upazila::select('id', 'district_id', 'name', 'bn_name')
                ->where('district_id', $districtId)
                ->orderBy('name')
                ->get();
            
            Log::info('Found ' . $upazilas->count() . ' upazilas for district ' . $districtId);

            $mappedUpazilas = $upazilas->map(function ($upazila) {
                return [
                    'id' => $upazila->id,
                    'district_id' => $upazila->district_id,
                    'name' => $upazila->name,
                    'bn_name' => $upazila->bn_name,
                    'display_name' => $upazila->name . ' (' . $upazila->bn_name . ')'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $mappedUpazilas,
                'message' => 'Upazilas retrieved successfully',
                'count' => $mappedUpazilas->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve upazilas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upazilas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}