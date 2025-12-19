<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Clients</title>
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
    showDeleteModal: false,
    editClient: {},
    deleteClient: {}
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
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
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
        <!-- END NOTIFICATION TOAST -->

        <!-- Sidebar -->
        @include('partials.admin_sidebar')


        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-4 h-[55px] w-auto">
                    <h1 class="text-2xl font-semibold text-gray-800">Clients Management</h1>
                </div>
            </header>

           <!-- Clients Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-8">

                <!-- Success Message Handler -->
                <div x-data="{ 
                    init() {
                        @if(session('success'))
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: '{{ session('success') }}', type: 'success' }
                            }));
                        @endif
                        @if(session('error'))
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: '{{ session('error') }}', type: 'error' }
                            }));
                        @endif
                    }
                }"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <!-- LEFT: Icon -->
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <!-- RIGHT: Label + Number -->
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Total Clients</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $totalClients ?? '1,234' }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <!-- LEFT: Icon -->
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <!-- RIGHT: Label + Number -->
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Regular Clients</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $regularClients ?? '856' }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <!-- LEFT: Icon -->
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <!-- RIGHT: Label + Number -->
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">New This Month</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $newClients ?? '23' }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <!-- LEFT: Icon -->
            <div class="bg-orange-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <!-- RIGHT: Label + Number -->
            <div>
                <p class="text-sm text-gray-600 mb-1 text-right">Occasional Clients</p>
                <h3 class="text-3xl font-bold text-gray-800 text-right">{{ $occasionalClients ?? '378' }}</h3>
            </div>
        </div>
    </div>
</div>

                <!-- Clients Table Card -->
                <div class="bg-white rounded-lg shadow">
                    <!-- Table Header with Search and Actions -->
                    <!-- Table Header with Search and Actions -->
<div class="p-6 border-b border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <h3 class="text-lg font-semibold text-gray-800">Clients List</h3>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-3">
            <!-- Search Bar -->
<form action="{{ route('clients.index') }}" method="GET" class="relative">
    <input type="text" 
           name="search" 
           value="{{ $search ?? '' }}"
           placeholder="Search clients..." 
           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    @if($search ?? false)
        <a href="{{ route('clients.index') }}" 
           class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
           title="Clear search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    @endif
</form>
            
            <!-- Add Client Button -->
<button @click="showAddModal = true" class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Add Client
</button>

<!-- View Archived Button -->
<a href="{{ route('clients.archived') }}" class="flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors whitespace-nowrap">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
    </svg>
    View Archived
</a>
        </div>
    </div>
</div>

                    <!-- Validation Errors (Add this after the modal header in BOTH modals) -->
@if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                   
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Client Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Address
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact Number
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
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
                                @forelse($clients ?? [] as $client)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                      
                                        <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ substr($client->fname, 0, 1) }}{{ substr($client->lname, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                        {{ $client->fname }} {{ $client->mname }} {{ $client->lname }}
                                         </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $client->company_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $client['address'] }}
                                        </td>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $client->contact }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $client['email'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold text-green-600">
                                            Active
                                        </span>
                                    </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex space-x-2">
                                            <!-- Edit Button -->
                            <button 
                                @click='
                                    editClient = {
                                        id: {{ $client->client_id }},
                                        first_name: @json($client->fname),
                                        middle_name: @json($client->mname),
                                        last_name: @json($client->lname),
                                        company_name: @json($client->company_name),
                                        address: @json($client->address),
                                        contact: @json($client->contact),
                                        email: @json($client->email)
                                    }; 
                                    showEditModal = true
                                ' 
                                class="text-green-600 hover:text-green-900 transition-colors" 
                                title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <!-- Validation Errors (Add this after the modal header in BOTH modals) -->
                            @if ($errors->any())
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        <!-- Archive Button -->
                        <button 
                            @click="
                                deleteClient = {
                                    id: '{{ $client->client_id }}',
                                    fname: '{{ $client->fname }}',
                                    lname: '{{ $client->lname }}'
                                };
                                showDeleteModal = true
                            "
                            class="text-orange-600 hover:text-orange-900 transition-colors" 
                            title="Archive">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </button>


                                            </div>
                                        </td>
                                    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg font-medium mb-1">No Clients Found</p>
                <p class="text-gray-400 text-sm mb-4">Get started by adding your first client</p>
                <button @click="showAddModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add First Client
                    </span>
                </button>
            </div>
        </td>
    </tr>
@endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
<div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $clients->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $clients->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $clients->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered)</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($clients->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $clients->appends(['search' => $search])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
            @if ($page == $clients->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $clients->appends(['search' => $search])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($clients->hasMorePages())
            <a href="{{ $clients->appends(['search' => $search])->nextPageUrl() }}" 
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

    <!-- Add Client Modal -->
    <div x-show="showAddModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
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
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 border-b border-blue-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white" id="modal-title">Add New Client</h3>
                        </div>
                        <button @click="showAddModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('clients.store') }}" method="POST">
               @csrf

                    <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter first name">
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Middle Name
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="middle_name" 
                                           name="middle_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter middle name">
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter last name">
                                </div>
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="company_name" 
                                           name="company_name"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter company name">
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="address" 
                                              name="address"
                                              required
                                              rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                              placeholder="Enter complete address"></textarea>
                                </div>
                            </div>

                            <!-- Contact -->
                            <div>
                                <label for="contact" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Contact Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input type="tel" 
       id="contact" 
       name="contact"
       required
       pattern="[0-9]{10,15}"
       title="Please enter only numbers (10-15 digits)"
       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
       placeholder="+63 XXX XXX XXXX">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" 
                                           id="email" 
                                           name="email"
                                           required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="email@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                        <button type="button" 
                                @click="showAddModal = false"
                                class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Client Modal -->
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
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 border-b border-green-800">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white" id="modal-title">Edit Client</h3>
                        </div>
                        <button @click="showEditModal = false" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form :action="'/clients/' + editClient.id" method="POST">
    @csrf
    @method('PUT')

                    <div class="bg-white px-6 py-6 max-h-[calc(100vh-300px)] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- First Name -->
                            <div>
                                <label for="edit_first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="edit_first_name" 
                                           name="first_name" 
                                           required
                                           x-model="editClient.first_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter first name">
                                </div>
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="edit_middle_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Middle Name
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="edit_middle_name" 
                                           name="middle_name"
                                           x-model="editClient.middle_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter middle name">
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="edit_last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="edit_last_name" 
                                           name="last_name"
                                           required
                                           x-model="editClient.last_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter last name">
                                </div>
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label for="edit_company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="edit_company_name" 
                                           name="company_name"
                                           required
                                           x-model="editClient.company_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Enter company name">
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="edit_address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="edit_address" 
                                              name="address"
                                              required
                                              rows="3"
                                              x-model="editClient.address"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"
                                              placeholder="Enter complete address"></textarea>
                                </div>
                            </div>

                            <!-- Contact -->
                            <div>
                                <label for="edit_contact" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Contact Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                   <input type="tel" 
       id="edit_contact" 
       name="contact"
       required
       pattern="[0-9]{10,15}"
       title="Please enter only numbers (10-15 digits)"
       x-model="editClient.contact"
       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
       placeholder="+63 XXX XXX XXXX">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="edit_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" 
                                           id="edit_email" 
                                           name="email"
                                           required
                                           x-model="editClient.email"
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                           placeholder="email@example.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                        <button type="button" 
                                @click="showEditModal = false"
                                class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Archive Confirmation Modal -->
<div x-show="showDeleteModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showDeleteModal = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <!-- Modal Content -->
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Client</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to archive <span class="font-semibold text-gray-900" x-text="deleteClient.fname + ' ' + deleteClient.lname"></span>? You can restore this client later from the archived clients page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                <button type="button" 
                        @click="showDeleteModal = false"
                        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                    Cancel
                </button>
                <form :action="'/clients/' + deleteClient.id + '/archive'" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        }, 500); // Wait 500ms after user stops typing
    });
</script>


</body>
</html>