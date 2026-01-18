<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SchoolSettings;
use Symfony\Component\HttpFoundation\Response;

class SchoolSettingsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->school_id !== null) {
                $schoolId = $user->school_id;
                
                $settings = SchoolSettings::find($schoolId);
                
                if (!$settings) {
                    $settings = SchoolSettings::create([
                        'school_name' => $user->name . ' School',
                    ]);
                    
                    $user->school_id = $settings->id;
                    $user->save();
                }
                
                if ($settings->logo) {
                    $settings->logo_url = asset('assets/images/school_logo/' . $settings->logo);
                }
        
                app()->instance('school.settings', $settings);
                
                if (!$request->expectsJson()) {
                    view()->share('school_settings', $settings);
                }
            } else {
                $this->setDefaultSettings();
            }
        } else {
            $this->setDefaultSettings();
        }
        
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse && 
            $request->route()->getName() === 'current-school-settings') {
            try {
                $data = $response->getData(true);
                $schoolSettings = app()->bound('school.settings') ? app('school.settings') : null;
                if ($schoolSettings) {
                    $data['data'] = $schoolSettings;
                    $response->setData($data);
                }
            } catch (\Exception $e) {
            }
        }
        
        return $response;
    }
    
    private function setDefaultSettings()
    {
        $defaultSettings = new \stdClass();
        $defaultSettings->school_name = 'Default School';
        $defaultSettings->logo_url = null;
        
        app()->instance('school.settings', $defaultSettings);
        
        if (!request()->expectsJson()) {
            view()->share('school_settings', $defaultSettings);
        }
    }
}