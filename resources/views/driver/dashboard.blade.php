@extends('layouts.app')

@section('title', 'Driver Dashboard')

@section('content')


<div class="max-w-6xl mx-auto">

    <div class="flex-1 transition-all duration-300">

        <div class="p-4 sm:p-6 lg:p-8 bg-gradient-to-br bg-white min-h-screen">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-700">Welcome, {{ $driver->fname ?? 'Driver' }}</h1>
                <p class="text-slate-500 mt-1">Here's your daily overview</p>
            </div>

            <!-- Summary Cards (CLICKABLE) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">

    <!-- Total Trips Card (CLICKABLE - Goes to Dispatches) -->
    <a href="{{ route('driver.dispatches') }}" 
       class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-blue-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-blue-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Total Trips</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalTrips }}</h3>
                <p class="text-xs text-slate-500 mt-2">All assigned trips</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-blue-600 group-hover:translate-x-1 transition-transform">
            <span>View all trips</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

    <!-- Total Commission Card (CLICKABLE - Goes to Commission Page) -->
    <a href="{{ route('driver.commission') }}" 
       class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-emerald-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-emerald-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Total Commission</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($totalCommission, 2) }}</h3>
                <p class="text-xs text-slate-500 mt-2">All time earnings</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-emerald-600 group-hover:translate-x-1 transition-transform">
            <span>View commissions</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

    <!-- Completed Trips Card (CLICKABLE - Goes to Dispatches with filter) -->
    <a href="{{ route('driver.dispatches') }}?status=completed" 
       class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-sky-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-sky-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-sky-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Completed Trips</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $completedTrips }}</h3>
                <p class="text-xs text-slate-500 mt-2">Successfully completed</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-sky-600 group-hover:translate-x-1 transition-transform">
            <span>View completed</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

    <!-- Pending Trips Card (CLICKABLE - Goes to Dispatches with filter) -->
    <a href="{{ route('driver.dispatches') }}?status=pending" 
       class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-amber-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-amber-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-amber-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Pending Trips</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $pendingTrips }}</h3>
                <p class="text-xs text-slate-500 mt-2">Awaiting action</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-amber-600 group-hover:translate-x-1 transition-transform">
            <span>View pending</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </a>

</div>

            <!-- Assigned Trips -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-700">Recent Dispatches</h2>
                    <a href="{{ route('driver.dispatches') }}"
                       class="px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors text-sm font-medium shadow-sm">
                        View All Dispatches →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                    @forelse($recentDispatches as $dispatch)
                    <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg transition-shadow border-l-4 
                        @if($dispatch['status'] === 'pending') border-amber-300
                        @elseif($dispatch['status'] === 'in_progress') border-emerald-300
                        @elseif($dispatch['status'] === 'completed') border-blue-300
                        @else border-gray-300
                        @endif overflow-hidden">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-700">Dispatch #{{ $dispatch['dispatch_id'] }}</h3>
                                    <p class="text-xs text-slate-500 mt-1">Assigned Trip</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border
                                    @if($dispatch['status'] === 'pending') text-amber-700 bg-amber-100 border-amber-200
                                    @elseif($dispatch['status'] === 'in_progress') text-emerald-700 bg-emerald-100 border-emerald-200
                                    @elseif($dispatch['status'] === 'completed') text-blue-700 bg-blue-100 border-blue-200
                                    @else text-gray-700 bg-gray-100 border-gray-200
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $dispatch['status'])) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
    <!-- SIPA Number with document/hashtag icon -->
    <div class="flex items-center text-sm text-slate-600">
        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
        </svg>
        <span class="font-medium">SIPA: {{ $dispatch['sipa_number'] }}</span>
    </div>
    
    <!-- Truck with actual truck icon -->
    <div class="flex items-center text-sm text-slate-600">
        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
        </svg>
        <span class="font-medium">{{ $dispatch['truck_name'] }}</span>
    </div>
    
    <!-- From/To Location -->
    <div class="flex items-center text-sm text-slate-600">
        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <div class="flex items-center flex-wrap">
            <span class="font-medium">{{ $dispatch['from'] }}</span>
            <span class="text-blue-600 mx-2">→</span>
            <span class="font-medium">{{ $dispatch['to'] }}</span>
        </div>
    </div>
    
    <!-- Type with tag icon -->
    <div class="flex items-center text-sm text-slate-600">
        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
        </svg>
        {{ $dispatch['type'] }}
    </div>
</div>
                            
                            <a href="{{ route('driver.trip.details', ['dispatch_id' => $dispatch['dispatch_id']]) }}" 
                               class="block w-full text-center px-4 py-2 bg-blue-50 text-blue-600 font-semibold rounded-lg hover:bg-blue-100 transition-colors text-sm border border-blue-100">
                                View Details →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 font-medium">No dispatches assigned yet</p>
                        <p class="mt-1 text-xs text-gray-400">Your assigned trips will appear here</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

@endsection