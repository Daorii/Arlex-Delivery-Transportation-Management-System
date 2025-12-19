<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADTMS - SIPA Request Client</title>
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
    ...sipaManager(),
    showArchiveModal: false,
    archiveData: {},
    showDeleteRateModal: false,
    deleteRateData: {}
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
                        <!-- Success Icon -->
                        <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <!-- Error Icon -->
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
                    <a href="/sipa-requests" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                <h1 class="text-2xl font-semibold text-gray-800">
                    SIPA Requests - {{ $client->fname }} {{ $client->lname }}
                </h1>
                </div>
            </header>

                        <!-- Client SIPA Requests Content -->
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

    
                <!-- Client SIPA Requests Table Card -->
                <div class="bg-white rounded-lg shadow">
                    
                    <!-- Table Header with Client Name and Add Button -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <h1 class="text-2xl font-semibold text-gray-800">
                                Client - {{ $client->fname }} {{ $client->lname }}
                            </h1>
                            <div class="flex space-x-3">
    <button @click="openModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add SIPA Request
    </button>
    
    <!-- View Archived Button -->
    <a href="{{ route('sipa.archived', $client->client_id) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
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
                                        SIPA Ref No.
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TYPE
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        CREATED AT
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ACTION
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sipaRequests as $request)
                                    <tr 
                                            @click="openRatesModal({{ $request->sipa_id }})" 
                                            class="hover:bg-blue-50 cursor-pointer transition-colors"
                                        >
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $request->sipa_ref_no }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $request->type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($request->created_at)->format('Y-m-d h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2" @click.stop>
    <div class="flex space-x-2">
        <!-- Edit Button -->
        <button 
            @click="editSipaRequest({{ $request->sipa_id }}, '{{ $request->sipa_ref_no }}', '{{ $request->type }}')" 
            class="text-green-600 hover:text-green-900 transition-colors"
            title="Edit"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                </path>
            </svg>
        </button>

        <!-- Archive Button -->
        <button 
            @click="archiveSipa({{ $request->sipa_id }}, '{{ $request->sipa_ref_no }}')"
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
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            No SIPA requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">{{ $totalRequests ?? '3' }}</span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Previous
                            </button>
                            <button class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">1</button>
                            <button class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit SIPA Request Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                        <!-- Modal panel -->
                <div x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">Manage SIPA Request</h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>  
                        </button>
                    </div>

                    <!-- Modal Form -->
                            <form @submit.prevent="submitSipaForm()">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ $client->client_id }}">

                                <div class="space-y-4">
                                    <!-- Request No -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Request No.</label>
                                        <input type="text" x-model="sipaFormData.sipa_ref_no" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., SIPA-2024-001" required>
                                    </div>

                                    <!-- Container Type (Dry/Reefer) -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Container Type</label>
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" x-model="sipaFormData.type" value="Dry" class="mr-2" required>
                                                <span class="text-sm text-gray-700">Dry</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" x-model="sipaFormData.type" value="Reefer" class="mr-2" required>
                                                <span class="text-sm text-gray-700">Reefer</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Actions -->
                                <div class="flex items-center justify-end space-x-3 mt-6">
                                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        Save
                                    </button>
                                </div>
                            </form>
        
                            </div>

                        </div>
                    </div>

    <!-- Manage Rates Modal -->
    <div x-show="showRatesModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRatesModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="closeRatesModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="showRatesModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                
             <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Manage SIPA</h3>
            <button @click="closeRatesModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            </div>

<!-- Tabs -->
<div class="flex border-b border-gray-200 mb-4">

                    <button
    @click="activeTab = 'rates'"
    :class="activeTab === 'rates' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-600'"
    class="px-4 py-2 font-medium focus:outline-none">
    Rates
</button>   
<button
    @click="activeTab = 'dispatches'"
    :class="activeTab === 'dispatches' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-600'"
    class="px-4 py-2 font-medium focus:outline-none">
    Dispatches
</button>
                </div>  

                <!-- ===== Rates Tab ===== -->
                <div x-show="activeTab === 'rates'" x-transition>
                <!-- Add/Edit Rate Form -->
                <form @submit.prevent="submitRateForm()">
                    <div class="space-y-4 bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-gray-700" x-text="isEditingRate ? 'Edit Rate' : 'Add New Rate'"></h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                        <!-- Size -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Size</label>
                        <select x-model="rateFormData.size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm" required>
                            <option value="">Select</option>
                            <option value="20ft">20ft</option>
                            <option value="40ft">40ft</option>
                        </select>
                        </div>

                        <!-- Route From -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Route From</label>
                        <input type="text" x-model="rateFormData.routeFrom" @input="filterRoutes('from')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="Enter origin" list="routes-from" required>
                        <datalist id="routes-from">
                            <template x-for="route in filteredRoutesFrom" :key="route">
                            <option :value="route"></option>
                            </template>
                        </datalist>
                        </div>

                        <!-- Route To -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Route To</label>
                        <input type="text" x-model="rateFormData.routeTo" @input="filterRoutes('to')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="Enter destination" list="routes-to" required>
                        <datalist id="routes-to">
                            <template x-for="route in filteredRoutesTo" :key="route">
                            <option :value="route"></option>
                            </template>
                        </datalist>
                        </div>

                        <!-- Volume Raw -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Volume Raw</label>
                        <input type="number" x-model="rateFormData.quantity" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="Enter quantity" required>
                        </div>

                        <!-- Effectivity From -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Effectivity From</label>
                        <input type="date" x-model="rateFormData.effectivityFrom"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            required>
                        </div>

                        <!-- Effectivity To -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Effectivity To</label>
                        <input type="date" x-model="rateFormData.effectivityTo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            required>
                        </div>

                        <!-- Price -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price (₱)</label>
                        <input type="number" x-model="rateFormData.price" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="5000.00" required>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <span x-text="isEditingRate ? 'Update' : 'Add'"></span>
                        </button>
                        </div>
                    </div>
                    </div>
                </form>

                <!-- Rates Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume Raw</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effectivity</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(rate, index) in rates" :key="index">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="rate.size"></td>
                            <td class="px-4 py-3 text-sm text-gray-900"
                                x-text="(rate.routeFrom || '') + ' → ' + (rate.routeTo || '')">
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900" x-text="rate.quantity"></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900"
                            x-text="'₱' + parseFloat(rate.price).toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                            <td class="px-4 py-3 text-sm"
                            x-text="rate.effectivityFrom + ' - ' + rate.effectivityTo"></td>
                            <td class="px-4 py-3 text-sm space-x-2">
                            <!-- Edit Icon Button -->
                            <button 
                                @click="editRate(rate, index)" 
                                class="text-green-600 hover:text-green-900 transition-colors" 
                                title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                                            a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </button>

                            <!-- Delete Icon Button -->
                                <button 
                                    @click="openDeleteRateModal(rate)"
                                    class="text-red-600 hover:text-red-900 transition-colors" 
                                    title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3
                                                M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        </template>
                        <template x-if="rates.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                            No rates added yet. Add your first rate above.
                            </td>
                        </tr>
                        </template>
                    </tbody>
                    </table>
                </div>
                </div>


                <!-- ===== Dispatches Tab ===== -->
                <div x-show="activeTab === 'dispatches'" x-transition>
                <div class="flex flex-col lg:flex-row gap-10">
                    
                    <!-- ===== Left Side: Dispatch Form (35% width on large screens) ===== -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm space-y-4 flex-shrink-0 w-full lg:w-1/3">

                    <h4 class="text-base font-semibold text-gray-700 mb-2"
                        x-text="isEditingDispatch ? 'Edit Dispatch' : 'Add New Dispatch'"></h4>

                    <div class="space-y-3">
                        <!-- Driver Name with Autocomplete -->
                        <!-- Driver ID -->
                        <!-- Driver Name with Autocomplete -->
<div class="relative">
    <label class="block text-xs font-medium text-gray-700 mb-1">Driver Name</label>
    <input type="text" 
        x-model="driverSearchQuery"
        @input="searchDrivers"
        @focus="showDriverSuggestions = true"
        placeholder="Start typing driver name..."
        autocomplete="off"
        required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg 
            focus:ring-2 focus:ring-green-500 text-sm">
    
    <!-- Driver Suggestions Dropdown -->
    <div x-show="showDriverSuggestions && driverSuggestions.length > 0" 
        @click.away="showDriverSuggestions = false"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
        <template x-for="driver in driverSuggestions" :key="driver.driver_id">
            <div @click="selectDriver(driver)" 
                class="px-3 py-2 hover:bg-green-50 cursor-pointer transition border-b border-gray-100 last:border-0">
                <span class="text-sm font-medium" x-text="driver.full_name"></span>
            </div>
        </template>
    </div>
</div>

                        <!-- Truck ID -->
                        <!-- Truck (Dropdown) -->
<div>
    <label class="block text-xs font-medium text-gray-700 mb-1">Truck</label>
    <select x-model="dispatchForm.truckId"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg 
            focus:ring-2 focus:ring-green-500 text-sm" required>
        <option value="">Select Truck</option>
        <template x-for="truck in availableTrucks" :key="truck.truck_id">
            <option :value="truck.truck_id" x-text="truck.display_name"></option>
        </template>
    </select>
</div>

                        <!-- Status -->
                        <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="dispatchForm.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg 
                                focus:ring-2 focus:ring-green-500 text-sm" required>
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="In Transit">In Transit</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        </div>

                        <!-- Submit Button -->
                          <button type="button"
                    @click="submitDispatchForm()"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 
                            text-white text-sm font-medium rounded-lg shadow-sm 
                            transition-colors">
                Add Dispatch
            </button>
                
                    </div>
                    </div>

                    <!-- ===== Right Side: Dispatch Table (65% width on large screens) ===== -->
                    <div class="flex-1 border border-gray-200 rounded-lg shadow-sm overflow-x-auto bg-white">
                    <h4 class="text-base font-semibold text-gray-700 px-4 pt-4">Dispatch List</h4>
                    <table class="min-w-full divide-y divide-gray-200 mt-2 table-auto">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver Name</th>
<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Truck</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(dispatch, index) in dispatches" :key="index">
                            <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="dispatch.driverName"></td>
<td class="px-4 py-3 text-sm text-gray-900" x-text="dispatch.truckName"></td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="dispatch.status"></td>
                            <td class="px-4 py-3 text-sm space-x-2">
                                <!-- Edit -->
                                <button @click="editDispatch(dispatch, index)" 
                                        class="text-green-600 hover:text-green-900 transition-colors" 
                                        title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 
                                        002 2h11a2 2 0 002-2v-5m-1.414-9.414
                                        a2 2 0 112.828 2.828L11.828 15H9v-2.828
                                        l8.586-8.586z"></path>
                                </svg>
                                </button>

                                <!-- Delete -->
                                <button @click="deleteDispatch(index)" 
                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                        title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 
                                        0116.138 21H7.862a2 2 0 
                                        01-1.995-1.858L5 7m5 4v6m4-6v6
                                        m1-10V4a1 1 0 00-1-1h-4a1 1 
                                        0 00-1 1v3M4 7h16"></path>
                                </svg>
                                </button>

                            </td>
                            </tr>
                        </template>

                        <template x-if="dispatches.length === 0">
                            <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                No dispatches added yet. Add your first dispatch on the left.
                            </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                    </div>

                </div>
                 
                </div>

                </div>
                <!-- Modal Footer -->

            </div>
        </div>
    </div>
 
 <script>


    
function sipaManager() {
    return {

        // Add after your existing dispatch methods

async loadAvailableTrucks() {
    try {
        const response = await fetch('/dispatch/available-trucks');
        const data = await response.json();
        this.availableTrucks = data;
    } catch (error) {
        console.error('Error loading trucks:', error);
    }
},

async searchDrivers() {
    if (this.driverSearchQuery.length < 2) {
        this.driverSuggestions = [];
        return;
    }

    try {
        const response = await fetch(`/dispatch/search-drivers?query=${encodeURIComponent(this.driverSearchQuery)}`);
        const data = await response.json();
        this.driverSuggestions = data;
        this.showDriverSuggestions = true;
    } catch (error) {
        console.error('Error searching drivers:', error);
    }
},

selectDriver(driver) {
    this.driverSearchQuery = driver.full_name;
    this.dispatchForm.driverId = driver.driver_id;
    this.dispatchForm.driverName = driver.full_name;
    this.showDriverSuggestions = false;
},


        // --- Modal states ---
        showModal: false,
        isEditing: false,
        showRatesModal: false,
        isEditingRate: false,

        // --- Expiration state ---
        isExpired: false,

        // --- SIPA data ---
        formData: {
            requestNo: '',
            containers: [{ size: '', quantity: 1 }],
            effectivityFrom: '2025-11-01',
            effectivityTo: '2025-11-30',
            createdAt: new Date().toLocaleString('en-US', { 
                year: 'numeric', month: '2-digit', day: '2-digit', 
                hour: '2-digit', minute: '2-digit', hour12: true 
            })
        },

        // --- SIPA Request form data ---
        sipaFormData: {
            sipa_id: null,
            sipa_ref_no: '',
            type: ''
        },
        isEditingSipa: false,

        // --- Rates data ---
        rates: [],
        rateFormData: {
            size: '',
            quantity: '',
            price: '',
            routeFrom: '',
            routeTo: '',
            effectivityFrom: '',
            effectivityTo: ''
        },
        editingRateId: null,
        sipaId: null, // initially no SIPA selected
        filteredRoutesFrom: [],
        filteredRoutesTo: [],

        // --- Dispatches data ---
        dispatches: [],
        dispatchForm: {
    driverId: '',
    driverName: '',
    truckId: '',
    status: ''
},
driverSearchQuery: '',
driverSuggestions: [],
showDriverSuggestions: false,
availableTrucks: [],
                isEditingDispatch: false,
        editingDispatchIndex: null,

        activeTab: 'rates',

        // -----------------------
        // --- SIPA Modal Functions ---
        // -----------------------
        openModal() {
            this.isEditingSipa = false;
            this.sipaFormData = { sipa_id: null, sipa_ref_no: '', type: '' };
            this.showModal = true;
        },

        editSipaRequest(sipaId, refNo, type) {
            this.isEditingSipa = true;
            this.sipaFormData = { sipa_id: sipaId, sipa_ref_no: refNo, type: type };
            this.showModal = true;
        },


        archiveSipa(sipaId, refNo) {
    this.archiveData = { id: sipaId, refNo: refNo };
    this.showArchiveModal = true;
},

        submitSipaForm() {
            const url = this.isEditingSipa 
                ? `/sipa-requests/${this.sipaFormData.sipa_id}` 
                : '{{ route("sipa.store") }}';
            const method = this.isEditingSipa ? 'PUT' : 'POST';
            const payload = {
                client_id: {{ $client->client_id }},
                sipa_ref_no: this.sipaFormData.sipa_ref_no,
                type: this.sipaFormData.type
            };

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                this.showModal = false;
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: data.message || 'SIPA request saved successfully!', type: 'success' }
                }));
                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(err => {
                console.error(err);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Failed to save SIPA request. Please try again.', type: 'error' }
                }));
            });
        },

        editRequest(request) {
            this.isEditing = true;
            this.formData.requestNo = request.ref_no;
            this.parseVolumeRaw(request.volume_raw);
            this.formData.effectivityFrom = request.effectivity_from;
            this.formData.effectivityTo = request.effectivity_to;
            this.showModal = true;
        },

        parseVolumeRaw(volumeRaw) {
            const parts = volumeRaw.split(',');
            this.formData.containers = parts.map(part => {
                const match = part.trim().match(/(\d+)x?\s*(20ft|40ft)/i);
                if (match) {
                    return { quantity: parseInt(match[1]), size: match[2] };
                }
                return { size: '', quantity: 1 };
            });
        },

        closeModal() {
            this.showModal = false;
            setTimeout(() => this.resetForm(), 200);
        },

        resetForm() {
            this.formData = {
                requestNo: '',
                containers: [{ size: '', quantity: 1 }],
                effectivityFrom: '2025-11-01',
                effectivityTo: '2025-11-30',
                createdAt: new Date().toLocaleString('en-US', { 
                    year: 'numeric', month: '2-digit', day: '2-digit', 
                    hour: '2-digit', minute: '2-digit', hour12: true 
                })
            };
        },

        addContainer() {
            this.formData.containers.push({ size: '', quantity: 1 });
        },

        removeContainer(index) {
            this.formData.containers.splice(index, 1);
        },

        submitForm() {
            const volumeRaw = this.formData.containers
                .filter(c => c.size && c.quantity)
                .map(c => `${c.quantity}x ${c.size}`)
                .join(', ');

            const data = {
                ref_no: this.formData.requestNo,
                volume_raw: volumeRaw,
                effectivity_from: this.formData.effectivityFrom,
                effectivity_to: this.formData.effectivityTo,
                created_at: this.formData.createdAt
            };

            console.log('Form submitted:', data);
            alert(this.isEditing ? 'SIPA Request updated!' : 'SIPA Request added!');
            this.closeModal();
        },




        // -----------------------
        // --- Rates Modal Functions ---
        // -----------------------
        openRatesModal(sipaId) {
    this.sipaId = sipaId;
    this.showRatesModal = true;
    this.resetRateForm();
    this.loadRates();
    this.loadDispatches();
    this.loadAvailableTrucks(); // ADD THIS LINE
},

        loadRates() {
            fetch(`/sipadetails/${this.sipaId}`)
                .then(res => res.json())
                .then(data => {
                    this.rates = data.map(r => ({
                        ...r,
                        quantity: r.volume,
                        routeFrom: r.route_from,
                        routeTo: r.route_to,
                        effectivityFrom: r.effectivity_from,
                        effectivityTo: r.effectivity_to
                    }));

                    if (this.rates.length === 0) {
                        this.isExpired = false;
                        return;
                    }

                    const latestEffectivityTo = this.rates.reduce((latest, r) => {
                        return new Date(r.effectivityTo) > new Date(latest)
                            ? r.effectivityTo
                            : latest;
                    }, this.rates[0].effectivityTo);

                    this.checkIfExpired(latestEffectivityTo);
                });
        },

        submitRateForm() {
    if (this.isExpired) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'This SIPA is expired. You cannot add or modify rates.', type: 'error' }
        }));
        return;
    }
    if (!this.rateFormData.size || !this.rateFormData.quantity || !this.rateFormData.price) {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Please fill all required fields!', type: 'error' }
        }));
        return;
    }

    const method = this.editingRateId ? 'PUT' : 'POST';
    const url = this.editingRateId ? `/sipadetails/${this.editingRateId}` : '/sipadetails';

    const payload = {
        sipa_id: this.sipaId,
        size: this.rateFormData.size,
        volume: this.rateFormData.quantity,
        price: this.rateFormData.price,
        route_from: this.rateFormData.routeFrom,
        route_to: this.rateFormData.routeTo,
        effectivity_from: this.rateFormData.effectivityFrom,
        effectivity_to: this.rateFormData.effectivityTo
    };

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(res => {
        if (!res.ok) {
            return res.json().then(err => Promise.reject(err));
        }
        return res.json();
    })
    .then(data => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: data.message || 'Rate saved successfully!', type: 'success' }
        }));

        if (this.editingRateId) {
            const index = this.rates.findIndex(r => r.sipa_detail_id === this.editingRateId);
            if (index !== -1) this.rates[index] = { ...data.rate, quantity: data.rate.volume, routeFrom: data.rate.route_from, routeTo: data.rate.route_to, effectivityFrom: data.rate.effectivity_from, effectivityTo: data.rate.effectivity_to };
        } else {
            this.rates.push({ ...data.rate, quantity: data.rate.volume, routeFrom: data.rate.route_from, routeTo: data.rate.route_to, effectivityFrom: data.rate.effectivity_from, effectivityTo: data.rate.effectivity_to });
        }

        this.resetRateForm();
        this.loadRates(); // Reload to check expiration
    })
    .catch(err => {
        console.error(err);
        const message = err.expired 
            ? err.message 
            : 'Failed to save rate. Please try again.';
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: message, type: 'error' }
        }));
        
        // If expired, reload rates to update UI
        if (err.expired) {
            this.loadRates();
        }
    });
},

        editRate(rate, index) {
    this.editingRateId = rate.sipa_detail_id;
    this.rateFormData = {
        size: rate.size,
        quantity: rate.quantity,
        price: rate.price,
        routeFrom: rate.routeFrom,
        routeTo: rate.routeTo,
        effectivityFrom: rate.effectivityFrom,
        effectivityTo: rate.effectivityTo
    };
    this.isEditingRate = true;
    // Modal is already open, no need to set showRatesModal again
},

        openDeleteRateModal(rate) {
    this.deleteRateData = {
        id: rate.sipa_detail_id,
        size: rate.size,
        route: `${rate.routeFrom || ''} → ${rate.routeTo || ''}`
    };
    this.showDeleteRateModal = true;
},

confirmDeleteRate() {
    fetch(`/sipadetails/${this.deleteRateData.id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: data.message || 'Rate deleted successfully!', type: 'success' }
        }));
        this.showDeleteRateModal = false;
        this.loadRates();
    })
    .catch(err => {
        console.error(err);
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Failed to delete rate. Please try again.', type: 'error' }
        }));
    });
},

        resetRateForm() {
    this.editingRateId = null;
    this.isEditingRate = false;  // ✅ Add this line
    this.rateFormData = {
        size: '',
        quantity: '',
        price: '',
        routeFrom: '',
        routeTo: '',
        effectivityTo: '',
        effectivityFrom: ''
    };
},

        // -----------------------
        // --- Dispatch Functions ---
        // -----------------------
        loadDispatches() {
    fetch(`/dispatch/sipa/${this.sipaId}`)
        .then(res => res.json())
        .then(data => {
            this.dispatches = data.map(d => ({
                dispatch_id: d.dispatch_id,
                driverId: d.driver_id,
                driverName: d.driverName,
                truckId: d.truck_id,
                truckName: d.truckName,
                status: d.status,
                sipa_id: d.sipa_id
            }));
        })
        .catch(err => console.error('Error loading dispatches:', err));
},

        submitDispatchForm() {
            if (this.isExpired) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'This SIPA is expired. You cannot add or modify dispatches.', type: 'error' }
                }));
                return;
            }

            if (!this.dispatchForm.driverId || !this.dispatchForm.truckId || !this.dispatchForm.status) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Please fill all required fields!', type: 'error' }
                }));
                return;
            }

            const method = this.isEditingDispatch ? 'PUT' : 'POST';
            const url = this.isEditingDispatch 
                ? `/dispatch/${this.dispatches[this.editingDispatchIndex].dispatch_id}` 
                : '/dispatch';

            const payload = {
                sipa_id: this.sipaId,
                driver_id: this.dispatchForm.driverId,
                truck_id: this.dispatchForm.truckId,
                status: this.dispatchForm.status
            };

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: data.message || 'Dispatch saved successfully!', type: 'success' }
                }));
                this.loadDispatches();
                this.resetDispatchForm();
            })
            .catch(err => {
                console.error('Error:', err);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Failed to save dispatch. Please try again.', type: 'error' }
                }));
            });
        },

        editDispatch(dispatch, index) {
            this.dispatchForm = {
                driverId: dispatch.driverId,
                truckId: dispatch.truckId,
                status: dispatch.status
            };
                        this.editingDispatchIndex = index;
            this.isEditingDispatch = true;
        },

        deleteDispatch(index) {
            if (!confirm('Are you sure?')) return;

            const dispatchId = this.dispatches[index].dispatch_id;

            fetch(`/dispatch/${dispatchId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: data.message || 'Dispatch deleted successfully!', type: 'success' }
                }));
                this.loadDispatches();
            })
            .catch(err => console.error('Error:', err));
        },

        resetDispatchForm() {
            this.dispatchForm = { driverId: '', truckId: '', status: '' };
                        this.isEditingDispatch = false;
            this.editingDispatchIndex = null;
        },
        
        closeRatesModal() { 
            this.showRatesModal = false; 
            setTimeout(() => cthis.resetRateForm(), 200); },

        // -----------------------
        // --- Utility Functions ---
        // -----------------------
        checkIfExpired(effectivityTo) {
            const today = new Date();
            const expiry = new Date(effectivityTo);
            this.isExpired = expiry < today;
        },

        filterRoutes(type) {
            // placeholder for your route filter logic
        }
    };
}
</script>
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive SIPA Request</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Are you sure you want to archive <span class="font-semibold text-gray-900" x-text="archiveData.refNo"></span>? You can restore it later from the archived page.
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
                    <form :action="'/sipa-requests/' + archiveData.id + '/archive'" method="POST">
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


        <!-- Delete Rate Confirmation Modal -->
    <div x-show="showDeleteRateModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteRateModal"
                 @click="showDeleteRateModal = false"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="showDeleteRateModal"
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Rate</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Are you sure you want to delete the rate for <span class="font-semibold text-gray-900" x-text="deleteRateData.size"></span> (<span x-text="deleteRateData.route"></span>)? <span class="text-red-600 font-semibold">This action cannot be undone!</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                    <button type="button" 
                            @click="showDeleteRateModal = false"
                            class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmDeleteRate()"
                            class="w-full sm:w-auto px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <style>
        [x-cloak] { display: none !important; }
    </style>


</body>
</html>