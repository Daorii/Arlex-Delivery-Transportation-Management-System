<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADTMS - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50"
      x-data="{ collapsed: false, mobileOpen: false }"
      x-init="
          collapsed = window.innerWidth < 1024;
          window.addEventListener('resize', () => {
              if(window.innerWidth >= 1024) {
                  mobileOpen = false;
              } else {
                  collapsed = true;
              }
          })
      ">
<div class="flex h-screen overflow-hidden">

    {{-- Mobile Overlay (MOVED OUTSIDE SIDEBAR) --}}
    <div x-show="mobileOpen" 
         @click="mobileOpen = false"
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 lg:hidden z-40"></div>

    {{-- Sidebar --}}
    @include('partials.sidebar-driver')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col transition-all duration-300"
         :class="{
             'lg:ml-16': collapsed && !mobileOpen,
             'lg:ml-64': !collapsed && !mobileOpen,
             'ml-0': mobileOpen
         }">
        
        {{-- Mobile Header --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between lg:hidden">
            <button @click="mobileOpen = true" class="text-gray-800 text-xl">☰</button>
            <h1 class="text-xl font-semibold text-gray-800">@yield('title')</h1>
            <div class="w-6"></div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>