@extends('layouts.app')

@section('title', 'My Dispatches')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto">

        <!-- Header Section with Filter Indicator -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-700">
                    @if($statusFilter === 'completed')
                        Completed Dispatches
                    @elseif($statusFilter === 'pending')
                        Pending Dispatches
                    @else
                        All Dispatches
                    @endif
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    @if($statusFilter === 'completed')
                        Showing dispatches with all trips verified
                    @elseif($statusFilter === 'pending')
                        Showing dispatches with pending trips
                    @else
                        Showing all your assigned dispatches
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @if($statusFilter)
                    <a href="{{ route('driver.dispatches') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium shadow-sm whitespace-nowrap text-center">
                        Clear Filter
                    </a>
                @endif
                <a href="{{ route('driver.dashboard') }}" 
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium shadow-sm whitespace-nowrap text-center">
                   ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filter Badges (Optional Visual Indicator) -->
        @if($statusFilter)
        <div class="mb-4">
            @if($statusFilter === 'completed')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-100 text-green-700 border border-green-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Filtering: Completed Only
                </span>
            @elseif($statusFilter === 'pending')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Filtering: Pending Only
                </span>
            @endif
        </div>
        @endif

        <!-- Dispatches Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            @forelse($dispatches as $dispatch)
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg transition-shadow border-l-4 
                @if($dispatch->status === 'pending') border-amber-300
                @elseif($dispatch->status === 'in_progress') border-blue-300
                @elseif($dispatch->status === 'completed') border-emerald-300
                @else border-gray-300
                @endif overflow-hidden">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-700">Dispatch #{{ $dispatch->dispatch_id }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $dispatch->completed_trips }}/{{ $dispatch->total_trips }} trips completed
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border
                            @if($dispatch->status === 'pending') text-amber-700 bg-amber-100 border-amber-200
                            @elseif($dispatch->status === 'in_progress') text-blue-700 bg-blue-100 border-blue-200
                            @elseif($dispatch->status === 'completed') text-emerald-700 bg-emerald-100 border-emerald-200
                            @else text-gray-700 bg-gray-100 border-gray-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $dispatch->status)) }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-4">
                        <!-- SIPA Number -->
                        <div class="flex items-center text-sm text-slate-600">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            <span class="font-medium">Truck: {{ $dispatch->truck_name }}</span>
                        </div>
                        
                        <!-- From/To Location -->
                        <div class="flex items-center text-sm text-slate-600">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div class="flex items-center flex-wrap">
                                <span class="font-medium">{{ $dispatch->from }}</span>
                                <span class="text-blue-600 mx-2">→</span>
                                <span class="font-medium">{{ $dispatch->to }}</span>
                            </div>
                        </div>
                        
                        <!-- Type -->
                        <div class="flex items-center text-sm text-slate-600">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ $dispatch->type }}
                        </div>
                    </div>
                    
                    <a href="{{ route('driver.trip.details', ['dispatch_id' => $dispatch->dispatch_id]) }}" 
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
                <p class="mt-2 text-sm text-gray-500 font-medium">
                    @if($statusFilter === 'completed')
                        No completed dispatches found
                    @elseif($statusFilter === 'pending')
                        No pending dispatches found
                    @else
                        No dispatches assigned yet
                    @endif
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    @if($statusFilter)
                        Try clearing the filter to see all dispatches
                    @else
                        Your assigned trips will appear here
                    @endif
                </p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection