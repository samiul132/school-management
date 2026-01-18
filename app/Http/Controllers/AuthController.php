<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRoleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }


    // public function logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully'
    //     ]);
    // }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function getUserMenus(Request $request)
    {
        $user = Auth::user();
        
        $menuList = UserRoleDetails::select('menus.*')
            ->where('role_id', $user->role_id)
            ->leftJoin('menus', 'menus.id', '=', 'user_role_details.menu_id')
            ->where('menus.show_on_navbar', 1)
            ->orderBy('menus.sorting', 'asc')
            ->get();

        $allPermittedMenus = UserRoleDetails::select('menus.*')
            ->where('role_id', $user->role_id)
            ->leftJoin('menus', 'menus.id', '=', 'user_role_details.menu_id')
            ->get();

        $primaryMenu = [];
        $subMenu = [];
        
        foreach ($menuList as $menu) {
            if ($menu->is_primary_menu == 1) {
                $primaryMenu[] = $menu;
            } else if ($menu->parent_id != null) {
                if (!isset($subMenu[$menu->parent_id])) {
                    $subMenu[$menu->parent_id] = [];
                }
                $subMenu[$menu->parent_id][] = $menu;
            }
        }

        $allRoutes = [];
        
        foreach ($allPermittedMenus as $menu) {
            if (!empty($menu->backend_route) && $menu->backend_route !== '#') {
                $allRoutes[] = $menu->backend_route;
            }
            
            if (!empty($menu->frontend_route) && $menu->frontend_route !== '#') {
                $allRoutes[] = $menu->frontend_route;
            }
        }
        
        $allRoutes = array_values(array_unique(array_filter($allRoutes)));

        return response()->json([
            'success' => true,
            'data' => [
                'primaryMenu' => $primaryMenu,
                'subMenu' => $subMenu,
                'routes' => $allRoutes
            ]
        ]);
    }
}