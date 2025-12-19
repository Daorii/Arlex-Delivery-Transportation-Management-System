<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Drivers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .input { 
            width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; 
            border-radius: 0.5rem; outline: none; 
        }
        .input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3); }
        
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-50" x-data="driverCRUD()" x-init="
    @if(session('success'))
        setTimeout(() => {
            $dispatch('notify', { message: '{{ session('success') }}', type: 'success' });
        }, 100);
    @endif
">

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


    <!-- Sidebar -->
    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <button class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-2xl font-semibold text-gray-800">Drivers Management</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">

            <!-- Stats Cards (ALL CLICKABLE) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Drivers (CLICKABLE - Clears any filters) -->
    <a href="{{ route('drivers.index') }}" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-blue-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Total Drivers</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $totalDrivers }}</h3>
                <p class="text-xs text-blue-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View all →</p>
            </div>
        </div>
    </a>

    <!-- Active Drivers (CLICKABLE - Filters by Active status) -->
    <a href="{{ route('drivers.index') }}?status=Active" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-green-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Active Drivers</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $activeDrivers }}</h3>
                <p class="text-xs text-green-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View active →</p>
            </div>
        </div>
    </a>

    <!-- On Trip Drivers (CLICKABLE - Filters by On Trip status) -->
    <a href="{{ route('drivers.index') }}?status=On Trip" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-yellow-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">On Trip</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $onTripDrivers }}</h3>
                <p class="text-xs text-yellow-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View on trip →</p>
            </div>
        </div>
    </a>

    <!-- Inactive Drivers (CLICKABLE - Filters by Inactive status) -->
    <a href="{{ route('drivers.index') }}?status=Inactive" 
       class="bg-white rounded-lg shadow p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-red-100 p-3 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Inactive Drivers</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $inactiveDrivers }}</h3>
                <p class="text-xs text-red-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View inactive →</p>
            </div>
        </div>
    </a>

</div>

            <!-- Drivers Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">All Drivers</h3>
                    <div class="flex space-x-3">
                        <form action="{{ route('drivers.index') }}" method="GET" class="relative">
    <input type="text" 
           name="search" 
           value="{{ $search ?? '' }}"
           placeholder="Search drivers..." 
           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    @if($search ?? false)
        <a href="{{ route('drivers.index') }}" 
           class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
           title="Clear search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    @endif
</form>
                        <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
    <span>Add Driver</span>
</button>
<a href="{{ route('drivers.archived') }}" 
   class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
    </svg>
    <span>View Archived</span>
</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Middle Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    @forelse($drivers as $driver)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->fname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->mname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->lname }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->license_no }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->contact_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold 
                    @if($driver->status == 'Active') text-green-600
                    @elseif($driver->status == 'On Trip') text-blue-600
                    @elseif($driver->status == 'Inactive') text-red-600
                    @else text-gray-600
                    @endif">
                    {{ $driver->status }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex space-x-2">
                    <!-- Edit Button -->
                    <button @click="openEditModal({{ $driver->toJson() }})" 
                    class="text-green-600 hover:text-green-900 transition-colors" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>

                    <!-- Archive Button -->
                    <button @click="openArchiveModal({ driver_id: {{ $driver->driver_id }}, name: '{{ $driver->fname }} {{ $driver->lname }}' })" 
                    class="text-orange-600 hover:text-orange-900 transition-colors" title="Archive">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center py-8 text-gray-500">No drivers found</td>
        </tr>
    @endforelse
</tbody>
                    </table>
                </div>
                                    

                <!-- Pagination -->
                <div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-medium">{{ $drivers->firstItem() ?? 0 }}</span> 
                        to <span class="font-medium">{{ $drivers->lastItem() ?? 0 }}</span> 
                        of <span class="font-medium">{{ $drivers->total() }}</span> results
                        @if($search ?? false)
                            <span class="text-blue-600">(filtered)</span>
                        @endif
                    </div>
                    
                    <div class="flex space-x-2">
                        {{-- Previous Button --}}
                        @if ($drivers->onFirstPage())
                            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                                Previous
                            </span>
                        @else
                            <a href="{{ $drivers->appends(['search' => $search])->previousPageUrl() }}" 
                               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Previous
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($drivers->getUrlRange(1, $drivers->lastPage()) as $page => $url)
                            @if ($page == $drivers->currentPage())
                                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $drivers->appends(['search' => $search])->url($page) }}" 
                                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($drivers->hasMorePages())
                            <a href="{{ $drivers->appends(['search' => $search])->nextPageUrl() }}" 
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

            </div>


        </main>
    </div>
</div>

<div x-show="showAddModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showAddModal = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div x-show="showAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 border-b border-blue-800 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-white" id="modal-title">Add New Driver</h3>
                <button @click="showAddModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="{{ route('drivers.store') }}">
                @csrf
                <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="fname" placeholder="Enter first name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name</label>
                            <input type="text" name="mname" placeholder="Enter middle name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="lname" placeholder="Enter last name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">License No <span class="text-red-500">*</span></label>
                            <input type="text" name="license_no" placeholder="Enter license no" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                           <input type="tel" name="contact_number" placeholder="+63 XXX XXX XXXX" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                            <input type="text" name="username" placeholder="Enter username"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" placeholder="Enter password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="Active">Active</option>
                                <option value="On Trip">On Trip</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                    <button type="button" @click="showAddModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Add Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT DRIVER MODAL -->
<div x-show="showEditModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showEditModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showEditModal = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="showEditModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 border-b border-green-800 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-white" id="modal-title">Edit Driver</h3>
                <button @click="showEditModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="POST" :action="`/drivers/${form.driver_id}`">
                @csrf
                @method('PUT')
                <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                            <input type="text" name="fname" x-model="form.fname"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name</label>
                            <input type="text" name="mname" x-model="form.mname"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="lname" x-model="form.lname"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">License No</label>
                            <input type="text" name="license_no" x-model="form.license_no"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                            <input type="tel" name="contact_number" x-model="form.contact_number"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" x-model="form.status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                                <option value="Active">Active</option>
                                <option value="On Trip">On Trip</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Update Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ARCHIVE CONFIRMATION MODAL -->
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Driver</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to archive <span class="font-semibold text-gray-900" x-text="archiveDriver.name"></span>? This will hide them from the active drivers list.
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
                <form :action="'/drivers/' + archiveDriver.driver_id + '/archive'" method="POST">
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


<script>

    // Auto-submit search form after user stops typing
let searchTimeout;
document.querySelector('input[name="search"]')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});


function driverCRUD() {
    return {
        showAddModal: false,
        showEditModal: false,
        showArchiveModal: false,
        form: {
            driver_id: '',
            fname: '',
            mname: '',
            lname: '',
            license_no: '',
            contact_number: '',
            status: '',
        },
        archiveDriver: {
            driver_id: '',
            name: ''
        },
        openEditModal(driver) {
            this.form = JSON.parse(JSON.stringify(driver));
            this.showEditModal = true;
        },
        openArchiveModal(driver) {
            this.archiveDriver = driver;
            this.showArchiveModal = true;
        }
    };
}
</script>


</body>
</html>
