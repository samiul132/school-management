<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use App\Models\UserRoleDetails;
use App\Models\Menu;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class AuthMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    	$menuList = UserRoleDetails::select('menus.*')
    		->where('role_id', Auth::user()->role_id)
    		->leftJoin('menus', 'menus.id', '=', 'user_role_details.menu_id')
    		->orderBy('sorting', 'asc')
    		->get();

    	$primaryMenu = [];
    	$subMenu = [];
    	$permitedMenus = [];
    	foreach ($menuList as $key => $value) {
    		if ($value->is_primary_menu==1 && $value->show_on_navbar==1) {
    			$primaryMenu[] = $value;
    		}

    		if ($value->is_primary_menu==0 && $value->show_on_navbar==1) {
    			$subMenu[$value->parent_id][] = $value;
    		}
    		$permitedMenus[] = $value->backend_route;
    	}

    	//dd($permitedMenus);
        //View::share('primaryMenuList', $primaryMenu);
        //View::share('subMenuList', $subMenu);
        
        $currentRoute = Route::currentRouteName();

        $activeRoute = Menu::where('backend_route', $currentRoute)->first();


        if (in_array($currentRoute, $permitedMenus) || $activeRoute==NULL) {
        	return $next($request);
        } else {
        	return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You are not authorized to access this content.'
            ], 500);
        }

    }
}