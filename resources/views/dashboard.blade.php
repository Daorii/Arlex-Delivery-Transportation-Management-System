<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
            @include('partials.admin_sidebar')

        <!-- Main Content -->
         
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-4 h-[55px] w-auto">
                    <h1 class="text-2xl font-semibold text-gray-800">Hello Admin!</h1>
                </div>
            </header>

            <!-- Dashboard Content -->
<main class="flex-1 overflow-y-auto bg-gray-50 p-8">
    
    <!-- Top Section: Left side (4 cards + 2 large cards) + Right side (Performance Overview) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left Side: 2 columns -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Top 4 Small Cards in 1 Row (NOW CLICKABLE) -->
<div class="grid grid-cols-4 gap-6">
    
    <!-- Total Clients (CLICKABLE) -->
    <a href="{{ route('clients.index') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-blue-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total Clients</p>
                <h3 class="text-xl font-bold text-gray-800 text-right">{{ $totalClients }}</h3>
                <p class="text-xs text-blue-600 mt-1 group-hover:translate-x-1 transition-transform">View →</p>
            </div>
        </div>
    </a>

    <!-- Active Trips (CLICKABLE) -->
    <a href="{{ route('TD.TripClient') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-green-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Active Trips</p>
                <h3 class="text-xl font-bold text-gray-800 text-right">{{ $activeTrips }}</h3>
                <p class="text-xs text-green-600 mt-1 group-hover:translate-x-1 transition-transform">View →</p>
            </div>
        </div>
    </a>

    <!-- Pending Invoices (CLICKABLE) -->
    <a href="{{ route('invoices.index') }}?status=pending" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-yellow-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Pending Invoices</p>
                <h3 class="text-xl font-bold text-gray-800 text-right">{{ $pendingInvoices }}</h3>
                <p class="text-xs text-yellow-600 mt-1 group-hover:translate-x-1 transition-transform">View →</p>
            </div>
        </div>
    </a>

    <!-- Total Revenue (CLICKABLE) -->
    <a href="{{ route('reports.analytics') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-purple-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total Revenue</p>
                <h3 class="text-xl font-bold text-gray-800 text-right">₱{{ number_format($totalRevenue, 2) }}</h3>
                <p class="text-xs text-purple-600 mt-1 group-hover:translate-x-1 transition-transform">View →</p>
            </div>
        </div>
    </a>

</div>

            <!-- Bottom 2 Large Cards in Row (NOW CLICKABLE) -->
<div class="grid grid-cols-2 gap-6">
    
    <!-- Trucks Availability (CLICKABLE) -->
    <a href="{{ route('trucks.index') }}" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.01] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Trucks Availability</h3>
            <div class="bg-blue-100 p-2 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Available</span>
                <span class="text-lg font-bold text-green-600">{{ $availableTrucks }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">On Trip</span>
                <span class="text-lg font-bold text-blue-600">{{ $trucksOnTrip }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Maintenance</span>
                <span class="text-lg font-bold text-yellow-600">{{ $trucksInMaintenance }}</span>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Total Trucks</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalTrucks }}</span>
                </div>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-blue-600 group-hover:translate-x-1 transition-transform">
            <span>View all trucks</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

    <!-- Drivers Availability (CLICKABLE) -->
    <a href="{{ route('drivers.index') }}" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.01] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Drivers Availability</h3>
            <div class="bg-green-100 p-2 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Available</span>
                <span class="text-lg font-bold text-green-600">{{ $availableDrivers }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">On Trip</span>
                <span class="text-lg font-bold text-blue-600">{{ $driversOnTrip }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">Inactive</span>
                <span class="text-lg font-bold text-red-600">{{ $inactiveDrivers }}</span>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Total Drivers</span>
                    <span class="text-xl font-bold text-gray-800">{{ $totalDrivers }}</span>
                </div>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-green-600 group-hover:translate-x-1 transition-transform">
            <span>View all drivers</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

</div>

        </div>

        <!-- Right Side: Performance Overview (Tall - spans both rows) -->
        <div class="lg:col-span-1 bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Performance Overview</h3>
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Performance Overview Section -->
<div class="p-6 space-y-6">
    <div class="p-4 bg-blue-50 rounded-lg">
        <p class="text-xs text-gray-600 mb-2">Top Client</p>
        <p class="text-2xl font-bold text-blue-600">{{ $topClient }}</p>
        <p class="text-sm text-gray-500 mt-2">{{ $topClientTrips }} trips completed</p>
    </div>
    <div class="p-4 bg-green-50 rounded-lg">
        <p class="text-xs text-gray-600 mb-2">Top Driver</p>
        <p class="text-2xl font-bold text-green-600">{{ $topDriver }}</p>
        <p class="text-sm text-gray-500 mt-2">{{ $topDriverTrips }} trips completed</p>
    </div>
</div>
        </div>
 
    </div>

    <!-- Bottom Row: Maintenance Alert (Full Width) -->
    <div class="mb-8">
        
        <!-- Maintenance Alert -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Maintenance Alert</h3>
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plate No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($maintenanceTrucks ?? [] as $truck)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $truck['plate_no'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $truck['issue'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-yellow-600">Under Maintenance</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">ABC-123</td>
                                <td class="px-4 py-3 text-sm text-gray-600">Engine repair needed</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-yellow-600">Under Maintenance</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">XYZ-789</td>
                                <td class="px-4 py-3 text-sm text-gray-600">Brake maintenance</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-yellow-600">Under Maintenance</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">DEF-456</td>
                                <td class="px-4 py-3 text-sm text-gray-600">Tire replacement</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-yellow-600">Under Maintenance</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>