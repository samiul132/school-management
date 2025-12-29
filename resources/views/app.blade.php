<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Dynamic Title with fallback -->
    @php
        $schoolName = 'School Management System';
        try {
            if (auth()->check() && app()->has('school.settings')) {
                $settings = app('school.settings');
                $schoolName = $settings->school_name ?? $schoolName;
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
    @endphp
    
    <title>{{ $schoolName }} || SMART CAMPUS</title>
    
    <!-- Dynamic Favicon -->
    <!-- @php
        $faviconPath = asset('assets/favicon.png');
        try {
            if (auth()->check() && app()->has('school.settings')) {
                $settings = app('school.settings');
                if ($settings && $settings->logo) {
                    $faviconPath = $settings->logo_url ?? asset('assets/images/school_logo/' . $settings->logo);
                }
            }
        } catch (\Exception $e) {
            // Use default favicon
        }
    @endphp -->

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    
    <!-- Fonts 
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> 
    -->
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
