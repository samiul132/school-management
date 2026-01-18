<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\UserRole;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $authUser = auth()->user();

            $query = User::with('role')
                ->where('type', 'ADMIN')
                ->where('id', '!=', $authUser->id); 

            if ($authUser->type === 'SUPER_ADMIN') {
                $query->withoutGlobalScope('school');
            }

            return response()->json($query->get());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'role_id' => 'required|exists:user_roles,id',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role_id;
            $user->password = Hash::make($request->password);
            $user->status = $request->status;
            $user->save();
            
            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user->load('role')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('User creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            return response()->json([
                'error' => 'Failed to create user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $authUser = auth()->user();
            
            $query = User::with('role');
            
            if ($authUser->type === 'SUPER_ADMIN') {
                $query->withoutGlobalScope('school')
                    ->where('type', 'ADMIN');
            }
            
            $user = $query->findOrFail($id);
            
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $id,
            'role_id' => 'required|exists:user_roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8', 
        ]);

        DB::beginTransaction();

        try {
            $authUser = auth()->user();
        
            $query = User::query();
            
            if ($authUser->type === 'SUPER_ADMIN') {
                $query->withoutGlobalScope('school')
                    ->where('type', 'ADMIN');
            }

            $user = $query->findOrFail($id); 
            
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role_id;
            $user->status = $request->status;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->load('role')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update user',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    // Chnage Password
    public function userUpdate(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,' . $id,
            'role_id' => 'required|exists:user_roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            
            $user->name = $request->name;
            $user->email = $request->email;
            //$user->role_id = $request->role_id;
            //$user->status = $request->status;
            
            if ($request->filled('old_password') || $request->filled('password') || $request->filled('password_confirmation')) {
                
                if (!$request->filled('old_password') || !$request->filled('password') || !$request->filled('password_confirmation')) {
                    return response()->json([
                        'error' => 'All password fields are required'
                    ], 422);
                }
                
                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json([
                        'error' => 'Current password is incorrect'
                    ], 422);
                }
                
                if ($request->old_password === $request->password) {
                    return response()->json([
                        'error' => 'New password must be different from current password'
                    ], 422);
                }
                
                if ($request->password !== $request->password_confirmation) {
                    return response()->json([
                        'error' => 'Passwords do not match'
                    ], 422);
                }
                
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->load('role')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            
            if ($user->id === auth()->id()) {
                return response()->json([
                    'error' => 'You cannot delete your own account'
                ], 403);
            }
            
            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to delete user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoles(): JsonResponse
    {
        try {
            $roles = UserRole::select('id', 'role_name')->get();
            return response()->json($roles);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch roles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // public function getRoles(): JsonResponse
    // {
    //     try {
    //         $query = UserRole::select('id', 'role_name');

    //         if (auth()->user()->type === 'SUPER_ADMIN') {
    //             $query->withoutGlobalScope('school');
    //         }

    //         return response()->json($query->get());

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Failed to fetch roles',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}