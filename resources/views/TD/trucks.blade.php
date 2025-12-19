<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Trucks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showArchiveModal: false,
    editTruck: {},
    archiveTruck: {}
}">
    <div class="flex h-screen overflow-hidden">


    <!-- SUCCESS/ERROR NOTIFICATION TOAST -->
<div x-data="{ 
    show: false, 
    message: '', 
    type: 'success',
    showNotification(msg, notifType = 'success') {
        this.message = msg;
        this.type = notifType;
        this.show = true;
        setTimeout(() => { this.show = false; }, 4000);
    }
}"
@notify.window="showNotification($event.detail.message, $event.detail.type)"
x-show="show"
x-transition:enter="transform ease-out duration-300 transition"
x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
x-transition:leave="transition ease-in duration-100"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed top-4 right-4 z-[9999] max-w-sm w-full"
style="display: none;">
    <div :class="{
        'bg-green-50 border-green-400': type === 'success',
        'bg-red-50 border-red-400': type === 'error',
        'bg-blue-50 border-blue-400': type === 'info'
    }" class="border-l-4 p-4 rounded-lg shadow-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p :class="{
                    'text-green-800': type === 'success',
                    'text-red-800': type === 'error',
                    'text-blue-800': type === 'info'
                }" class="text-sm font-medium" x-text="message"></p>
            </div>
            <div class="ml-auto pl-3">
                <button @click="show = false" :class="{
                    'text-green-500 hover:text-green-600': type === 'success',
                    'text-red-500 hover:text-red-600': type === 'error',
                    'text-blue-500 hover:text-blue-600': type === 'info'
                }">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" 
     class="fixed top-4 right-4 z-[9999] max-w-sm w-full">
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
</div>
@endif


        <!-- Sidebar -->
        @include('partials.admin_sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-4 h-[55px] w-auto">
                    <h1 class="text-2xl font-semibold text-gray-800">Truck Management</h1>
                </div>  
            </header>

            <!-- Trucks Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
                <!-- Stats Cards (ALL CLICKABLE) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Trucks (CLICKABLE - Clears filters) -->
    <a href="{{ route('trucks.index') }}" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Trucks</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalTrucks }}</h3>
                <p class="text-xs text-blue-600 mt-2 group-hover:translate-x-1 transition-transform">View all →</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
        </div>
    </a>

    <!-- Available Trucks (CLICKABLE - Filters by Available) -->
    <a href="{{ route('trucks.index') }}?status=Available" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Available</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $availableTrucks }}</h3>
                <p class="text-xs text-green-600 mt-2 group-hover:translate-x-1 transition-transform">View available →</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </a>

    <!-- Under Maintenance (CLICKABLE - Filters by Maintenance) -->
    <a href="{{ route('trucks.index') }}?status=Maintenance" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Under Maintenance</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $maintenanceTrucks }}</h3>
                <p class="text-xs text-yellow-600 mt-2 group-hover:translate-x-1 transition-transform">View maintenance →</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </a>

    <!-- On Trip (CLICKABLE - Filters by On Trip) -->
    <a href="{{ route('trucks.index') }}?status=On Trip" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">On Trip</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $onTripTrucks }}</h3>
                <p class="text-xs text-red-600 mt-2 group-hover:translate-x-1 transition-transform">View on trip →</p>
            </div>
            <div class="bg-red-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
        </div>
    </a>

</div>

                <!-- Trucks Table Card -->
                <div class="bg-white rounded-lg shadow">
                        <!-- Table Header with Search and Actions -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                                <h3 class="text-lg font-semibold text-gray-800">Trucks List</h3>
                                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-3">
                                    <!-- Search Bar -->
<form action="{{ route('trucks.index') }}" method="GET" class="relative">
    <input type="text" 
           name="search" 
           value="{{ $search ?? '' }}"
           placeholder="Search trucks..." 
           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    @if($search ?? false)
        <a href="{{ route('trucks.index') }}" 
           class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
           title="Clear search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    @endif
</form>

                                    
                                    <!-- Add Truck Button -->
                                    <button @click="showAddModal = true" class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Truck
                                    </button>
                                    <a href="{{ route('trucks.archived') }}" 
   class="flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
    </svg>
    View Archived
</a>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Plate Number
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
@forelse($trucks as $truck)
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $truck->plate_no }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $truck->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($truck->status == 'Available')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold text-green-600">
                                                Available
                                            </span>
                                        @elseif($truck->status == 'Maintenance')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold text-yellow-600">
                                                Maintenance
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold text-red-600">
                                                On Trip
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
    <div class="flex space-x-2">
        <!-- Edit -->
        <button @click="editTruck = {{ $truck }}; showEditModal = true" class="text-green-600 hover:text-green-900 transition-colors" title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>

        <!-- Archive -->
        <button @click="archiveTruck = { id: '{{ $truck->truck_id }}', plate_no: '{{ $truck->plate_no }}' }; showArchiveModal = true" class="text-orange-600 hover:text-orange-900 transition-colors" title="Archive">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
        </button>
    </div>
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No records available</td>
                                </tr>
                                @endforelse

                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
<div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $trucks->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $trucks->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $trucks->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered by search)</span>
        @endif
        @if($statusFilter ?? false)
            <span class="text-blue-600">(filtered by {{ $statusFilter }})</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($trucks->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $trucks->appends(['search' => $search, 'status' => $statusFilter])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($trucks->getUrlRange(1, $trucks->lastPage()) as $page => $url)
            @if ($page == $trucks->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $trucks->appends(['search' => $search, 'status' => $statusFilter])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($trucks->hasMorePages())
            <a href="{{ $trucks->appends(['search' => $search, 'status' => $statusFilter])->nextPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Next
            </a>
        @else
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Next
            </span>
        @endif
    </div>
</div>

                        <!-- Add Truck Modal -->
                        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                            <!-- Background overlay -->
                            <div x-show="showAddModal" x-transition.opacity
                                @click="showAddModal = false"
                                class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"
                                aria-hidden="true"></div>

                            <!-- Modal panel -->
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div x-show="showAddModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                                <!-- Modal Header -->
                                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 border-b border-blue-800 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-white" id="modal-title">Add New Truck</h3>
                                    </div>
                                    <button @click="showAddModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    </button>
                                </div>

                                <!-- Modal Body -->
                                <form action="{{ route('trucks.store') }}" method="POST">
                                    @csrf
                                    <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        
                                        <!-- Plate Number -->
                                        <div>
                                        <label for="plate_no" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Plate Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="plate_no" name="plate_no" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="Enter Plate Number">
                                        </div>

                                        <!-- Description -->
                                        <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Description <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="description" name="description" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="Enter Description">
                                        </div>

                                        <!-- Status -->
                                        <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select id="status" name="status" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                            <option value="">Select Status</option>
                                            <option value="Available">Available</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="On Trip">On Trip</option>
                                        </select>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                                    <button type="button" @click="showAddModal = false"
                                            class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
                                        Add Truck
                                    </button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>

                                <!-- Edit Truck Modal -->
                            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                    <!-- Background overlay -->
                                    <div x-show="showEditModal" x-transition.opacity
                                        @click="showEditModal = false"
                                        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"
                                        aria-hidden="true"></div>

                                    <!-- Modal panel -->
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div x-show="showEditModal"
                                        x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                                    <!-- Modal Header -->
                                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 border-b border-green-800 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold text-white" id="modal-title">Edit Truck</h3>
                                        </div>
                                        <button @click="showEditModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <form :action="'/trucks/' + editTruck.truck_id" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                                <!-- Plate Number -->
                                                <div>
                                                    <label for="plate_no_edit" class="block text-sm font-semibold text-gray-700 mb-2">
                                                        Plate Number <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="plate_no_edit" name="plate_no" x-model="editTruck.plate_no" required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                                        placeholder="Enter Plate Number">
                                                </div>

                                                <!-- Description -->
                                                <div class="md:col-span-2">
                                                    <label for="description_edit" class="block text-sm font-semibold text-gray-700 mb-2">
                                                        Description <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="description_edit" name="description" x-model="editTruck.description" required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                                        placeholder="Enter Description">
                                                </div>

                                                <!-- Status -->
<div>
    <label for="status_edit" class="block text-sm font-semibold text-gray-700 mb-2">
        Status <span class="text-red-500">*</span>
    </label>
    <select id="status_edit" name="status" x-model="editTruck.status" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
        <option value="">Select Status</option>
        <option value="Available">Available</option>
        <option value="Maintenance">Maintenance</option>
        <option value="On Trip">On Trip</option>
    </select>
</div>

<!-- Reason for Maintenance (shows only if status is Maintenance) -->
<div x-show="editTruck.status === 'Maintenance'" class="md:col-span-2" x-cloak>
    <label for="maintenance_reason" class="block text-sm font-semibold text-gray-700 mb-2">
        Reason for Maintenance <span class="text-red-500">*</span>
    </label>
    <input type="text" id="maintenance_reason" name="maintenance_reason"
        x-model="editTruck.maintenance_reason"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
        placeholder="Enter reason for maintenance">
</div>
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                                            <button type="button" @click="showEditModal = false"
                                                    class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="w-full sm:w-auto px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
                                                Update Truck
                                            </button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
<!-- Archive Confirmation Modal -->
<div x-show="showArchiveModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showArchiveModal"
             @click="showArchiveModal = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div x-show="showArchiveModal"
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Truck</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to archive truck <span class="font-semibold text-gray-900" x-text="archiveTruck.plate_no"></span>? This will hide it from the active trucks list.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                <button type="button" 
                        @click="showArchiveModal = false"
                        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <form :action="'/trucks/' + archiveTruck.id + '/archive'" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        Yes, Archive
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


                    </div>
            </main>
        </div>
    </div>


    <script>
    // Auto-submit search form after user stops typing
    let searchTimeout;
    document.querySelector('input[name="search"]')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
</script>

</body>
</html>