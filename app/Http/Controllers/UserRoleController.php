<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Models\UserRoleDetails;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Auth;

class UserRoleController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $roles = UserRole::with(['roleDetails.menu'])->get();
            // $roles = UserRole::with(['roleDetails.menu'])
            //     ->when(Auth::user()->type === 'SUPER_ADMIN', fn ($q) => $q->withoutGlobalScope('school'))
            //     ->get();
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user roles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'permitted_menu' => 'required|array',
            'permitted_menu.*' => 'exists:menus,id',
        ]);

        DB::beginTransaction();

        try {
            $userRole = new UserRole();
            $userRole->role_name = $request->role_name;
            $userRole->save();

            foreach ($request->permitted_menu as $menuId) {
                $roleDetail = new UserRoleDetails();
                $roleDetail->role_id = $userRole->id;
                $roleDetail->menu_id = $menuId;
                $roleDetail->save();
            }
            
            DB::commit();

            return response()->json([
                'message' => 'User role created successfully',
                'data' => $userRole->load('roleDetails.menu')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User role creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create user role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $role = UserRole::with(['roleDetails.menu'])->findOrFail($id);
            return response()->json($role);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'role_name' => 'sometimes|required|string|max:255',
            'permitted_menu' => 'sometimes|required|array',
            'permitted_menu.*' => 'exists:menus,id',
        ]);

        DB::beginTransaction();

        try {
            $userRole = UserRole::findOrFail($id);
            
            $userRole->role_name = $request->input('role_name', $userRole->role_name);
            $userRole->save();

            if ($request->has('permitted_menu')) {
                UserRoleDetails::where('role_id', $id)->delete();

                foreach ($request->permitted_menu as $menuId) {
                    $roleDetail = new UserRoleDetails();
                    $roleDetail->role_id = $userRole->id;
                    $roleDetail->menu_id = $menuId;
                    $roleDetail->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'User role updated successfully',
                'data' => $userRole->load('roleDetails.menu')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update user role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $userRole = UserRole::findOrFail($id);
            
            UserRoleDetails::where('role_id', $id)->delete();
            $userRole->delete();

            DB::commit();

            return response()->json([
                'message' => 'User role deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'User role not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete user role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getMenus(Request $request)
    {
        $user = auth()->user();
        
        if ($user->type === 'SUPER_ADMIN') {
            $menus = Menu::orderBy('sorting')->get();
        } 
        else if ($user->type === 'ADMIN') {
            $excludedMenuIds = [21, 22, 23, 24, 25, 26, 27]; 
            
            $menus = Menu::whereNotIn('id', $excludedMenuIds)
                        ->orderBy('sorting')
                        ->get();
        }
        else {
            $menus = Menu::whereHas('roleDetails', function($query) use ($user) {
                $query->where('role_id', $user->role_id);
            })->orderBy('sorting')->get();
        }
        
        return response()->json($menus);
    }
}
