<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="flex bg-gray-100 min-h-screen">

    <!-- Admin Sidebar -->
    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 p-6">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
