<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ADTMS - Trip Dispatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">

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



<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <a href="/trip-client" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800">Trip Dispatch - Review EIRs</h1>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-8" x-data="dispatchApp()">
            <div class="flex gap-6 h-full">
                <!-- Left: Dispatch List -->
                <div class="w-1/3 bg-white rounded-2xl shadow-xl p-6 overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Dispatches</h3>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full" x-text="dispatches.length + ' total'"></span>
                            
                            <!-- View Archived Button (Better Position) -->
                            <a href="{{ route('trip-dispatch.archived', $client->client_id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg transition-colors"
                               title="View Archived Dispatches">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                Archived
                            </a>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" placeholder="Search dispatch..." x-model="query" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1">
                        <template x-for="d in filteredDispatches()" :key="d.id">
                            <div class="relative p-4 rounded-xl transition-all duration-200 mb-2"
                                :class="selected && selected.id === d.id ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'">
                                
                                <div class="flex items-start justify-between gap-3">
                                    <!-- Main content (clickable) -->
                                    <div @click="selectDispatch(d)" class="flex-1 cursor-pointer min-w-0">
                                        <p class="font-semibold truncate" x-text="d.driver_name"></p>
                                        <p class="text-xs opacity-75" x-text="'Dispatch ID: ' + d.id"></p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <p class="text-sm" x-text="d.total_eirs + ' EIRs'"></p>
                                            <span class="text-xs opacity-70" x-text="d.status"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Archive Button -->
                                    <button @click.stop="archiveDispatchId = d.id; showArchiveModal = true; console.log('Archive clicked:', d.id)" 
                                            class="p-1.5 rounded-lg transition-colors flex-shrink-0"
                                            :class="selected && selected.id === d.id ? 'hover:bg-white/20 text-white' : 'hover:bg-orange-100 text-orange-600'"
                                            title="Archive Dispatch"
                                            type="button">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right: Dispatch Details & EIR Table -->
                <div class="flex-1 bg-white rounded-2xl shadow-xl p-8 overflow-y-auto">

                    <!-- Placeholder when no dispatch selected -->
                    <div x-show="!selected" x-cloak class="flex items-center justify-center h-96">
                        <div class="text-center">
                            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Dispatch Selected</h3>
                            <p class="text-gray-500">Select a dispatch on the left to review submitted EIRs</p>
                        </div>
                    </div>
                        
                    <div x-show="selected" x-transition x-cloak>
                        <!-- Dispatch Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800" x-text="selected ? selected.driver_name : ''"></h2>
                                <p class="text-sm text-gray-600" x-text="selected ? 'Dispatch ID: ' + selected.id : ''"></p>
                                <p class="text-sm text-gray-600" x-text="selected ? 'Submitted At: ' + selected.submitted_at : ''"></p>
                            </div>

                            <div class="space-x-2">
                                <button @click="selectAllApprove" class="px-4 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">Approve All</button>
                                <button @click="selectAllDecline" class="px-4 py-2 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700">Decline All</button>
                                <button @click="selected = null" class="px-4 py-2 border rounded-xl">Close</button>
                            </div>
                        </div>

                        <!-- Basic Info Panel -->
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-gray-50">
                                <p class="text-xs text-gray-500">Driver Name</p>
                                <p class="font-medium text-gray-800" x-text="selected.driver_name"></p>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50">
                                <p class="text-xs text-gray-500">Vehicle / Plate</p>
                                <p class="font-medium text-gray-800" x-text="selected.vehicle"></p>
                            </div>
                        </div>

<!-- EIR Table -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container No</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EIR No</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="(eir, index) in selected.eirs" :key="index">
                <tr :class="eir.status === 'approved' ? 'bg-green-50' : (eir.status === 'declined' ? 'bg-red-50' : '')" 
                    class="transition hover:bg-gray-50">
                    
                    <!-- Container No -->
                    <td class="px-4 py-4 text-sm text-gray-900">
                        <template x-if="editingEir === eir.detail_id">
                            <input type="text" 
                                   x-model="editForm.container_no" 
                                   class="w-full min-w-[150px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </template>
                        <template x-if="editingEir !== eir.detail_id">
                            <span x-text="eir.container_no"></span>
                        </template>
                    </td>
                    
                    <!-- EIR No -->
                    <td class="px-4 py-4 text-sm text-gray-700">
                        <template x-if="editingEir === eir.detail_id">
                            <input type="text" 
                                   x-model="editForm.eir_no" 
                                   class="w-full min-w-[120px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </template>
                        <template x-if="editingEir !== eir.detail_id">
                            <span x-text="eir.eir_no"></span>
                        </template>
                    </td>
                    
                    <!-- Delivery Date -->
                    <td class="px-4 py-4 text-sm text-gray-700">
                        <template x-if="editingEir === eir.detail_id">
                            <input type="date" 
                                   x-model="editForm.delivery_date" 
                                   class="w-full min-w-[150px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </template>
                        <template x-if="editingEir !== eir.detail_id">
                            <span x-text="eir.delivery_date"></span>
                        </template>
                    </td>
                    
                    <!-- Size -->
                    <td class="px-4 py-4 text-sm text-gray-700">
                        <template x-if="editingEir === eir.detail_id">
                            <select x-model="editForm.size" 
                                    class="w-full min-w-[80px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="20">20</option>
                                <option value="40">40</option>
                            </select>
                        </template>
                        <template x-if="editingEir !== eir.detail_id">
                            <span x-text="eir.size"></span>
                        </template>
                    </td>
                    
                    <!-- Type -->
<td class="px-4 py-4 text-sm text-gray-700">
    <template x-if="editingEir === eir.detail_id">
        <select x-model="editForm.type" 
                class="w-full min-w-[100px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="Dry">Dry</option>
            <option value="Reefer">Reefer</option>
        </select>
    </template>
    <template x-if="editingEir !== eir.detail_id">
        <span x-text="eir.type"></span>
    </template>
</td>
                    
                    <!-- Price -->
                    <td class="px-4 py-4 text-sm text-gray-700">
                        <template x-if="editingEir === eir.detail_id">
                            <input type="number" 
                                   x-model="editForm.price" 
                                   step="0.01"
                                   class="w-full min-w-[120px] px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </template>
                        <template x-if="editingEir !== eir.detail_id">
                            <span x-text="formatCurrency(eir.price)"></span>
                        </template>
                    </td>
                    
                    <!-- Status -->
                    <td class="px-4 py-4 text-sm text-center">
                        <span x-text="eir.status" 
                              :class="eir.status === 'pending' ? 'px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold' : (eir.status === 'approved' ? 'px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold' : 'px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold')"></span>
                    </td>
                    
                    <!-- Action Buttons -->
                    <td class="px-4 py-4 text-sm">
                        <template x-if="editingEir === eir.detail_id">
                            <div class="flex space-x-2 justify-center">
                                <button @click="saveEdit(eir)" 
                                        class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm" 
                                        title="Save">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                <button @click="cancelEdit()" 
                                        class="p-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors shadow-sm" 
                                        title="Cancel">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        
                        <template x-if="editingEir !== eir.detail_id">
                            <div class="flex space-x-2 justify-center">
                                <!-- Check Button (Approve) -->
                                <button
                                    @click="approveEir(eir)"
                                    :disabled="eir.status === 'approved'"
                                    class="p-2 rounded-lg transition-colors shadow-sm"
                                    :class="eir.status === 'approved' ? 'bg-gray-300 cursor-not-allowed text-gray-500' : 'bg-green-600 hover:bg-green-700 text-white'"
                                    title="Approve">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Edit Button - GREEN like Drivers table (CENTER) -->
                                <button
                                    @click="startEdit(eir)"
                                    class="text-green-600 hover:text-green-900 transition-colors p-1"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                <!-- X Button (Decline) -->
                                <button
                                    @click="declineEir(eir)"
                                    :disabled="eir.status === 'declined'"
                                    class="p-2 rounded-lg transition-colors shadow-sm"
                                    :class="eir.status === 'declined' ? 'bg-gray-300 cursor-not-allowed text-gray-500' : 'bg-red-600 hover:bg-red-700 text-white'"
                                    title="Decline">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

                        <!-- Actions / Summary -->
                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Selected Dispatch: <span class="font-medium" x-text="selected.id"></span></p>
                                <p class="text-sm text-gray-600">Approved: <span class="font-semibold" x-text="countStatus('approved')"></span> / <span x-text="selected.eirs.length"></span></p>
                            </div>

                            <div class="flex gap-3">
                                <button @click="saveReview" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">Save Review</button>
                                <button @click="resetEirs" class="px-4 py-2 border rounded-xl">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archive Confirmation Modal - MOVED INSIDE MAIN -->
            <div x-show="showArchiveModal" 
                 x-cloak
                 class="fixed inset-0 z-[9999] overflow-y-auto" 
                 style="display: none;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showArchiveModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="showArchiveModal = false"
                         class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    
                    <div x-show="showArchiveModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         @click.stop
                         class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Dispatch</h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Are you sure you want to archive Dispatch ID: <span class="font-semibold" x-text="archiveDispatchId"></span>?
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        It will be moved to the archived list and can be viewed later.
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
                            <button type="button"
                                    @click="confirmArchive(archiveDispatchId); showArchiveModal = false"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                Yes, Archive
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

    
<script>
function dispatchApp(){
    return {
        query: '',
        dispatches: @json($dispatches ?? []),
        selected: null,
        showArchiveModal: false,
        archiveDispatchId: null,
        editingEir: null,
        editForm: {},

        init() {
            console.log('Dispatches loaded:', this.dispatches);
        },

        filteredDispatches(){
            if(!this.query) return this.dispatches;
            const q = this.query.toLowerCase();
            return this.dispatches.filter(d => d.driver_name.toLowerCase().includes(q) || String(d.id).includes(q));
        },

        selectDispatch(d){
            this.selected = JSON.parse(JSON.stringify(d));
        },

        approveEir(eir){ 
            eir.status = 'approved'; 
        },
        
        declineEir(eir){ 
            eir.status = 'declined'; 
        },

        selectAllApprove(){
            if(this.selected) this.selected.eirs.forEach(e => e.status = 'approved');
        },

        selectAllDecline(){
            if(this.selected) this.selected.eirs.forEach(e => e.status = 'declined');
        },

        countStatus(status){
            if(!this.selected) return 0;
            return this.selected.eirs.filter(e => e.status === status).length;
        },

        resetEirs(){
            if(!this.selected) return;
            const original = this.dispatches.find(d => d.id === this.selected.id);
            if(original) this.selected = JSON.parse(JSON.stringify(original));
        },

        startEdit(eir) {
            this.editingEir = eir.detail_id;
            this.editForm = JSON.parse(JSON.stringify(eir));
        },

        cancelEdit() {
            this.editingEir = null;
            this.editForm = {};
        },

        saveEdit(eir) {
            // Update the EIR with edited values
            Object.assign(eir, this.editForm);
            this.editingEir = null;
            this.editForm = {};
            
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { message: 'EIR updated! Click "Save Review" to persist changes.', type: 'info' }
            }));
        },

        saveReview(){
    if(!this.selected) return alert('No dispatch selected.');
    
    fetch('/trip-dispatch/save-review', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            dispatch_id: this.selected.id,
            eirs: this.selected.eirs
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { message: 'Review saved successfully!', type: 'success' }
            }));
            // REMOVED: setTimeout(() => location.reload(), 1000);
            // Update the original dispatch in the list to reflect saved changes
            const dispatchIndex = this.dispatches.findIndex(d => d.id === this.selected.id);
            if(dispatchIndex !== -1) {
                this.dispatches[dispatchIndex] = JSON.parse(JSON.stringify(this.selected));
            }
        } else {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { message: 'Error saving review: ' + data.message, type: 'error' }
            }));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.dispatchEvent(new CustomEvent('notify', { 
            detail: { message: 'Error: ' + error, type: 'error' }
        }));
    });
},

        formatCurrency(val){
            return '₱' + Number(val).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        },

        confirmArchive(dispatchId) {
            if(!dispatchId) {
                console.error('No dispatch ID provided');
                return;
            }

            console.log('Archiving dispatch:', dispatchId);

            fetch('/trip-dispatch/' + dispatchId + '/archive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Dispatch archived successfully!', type: 'success' }
                    }));
                    
                    this.dispatches = this.dispatches.filter(d => d.id !== dispatchId);
                    
                    if(this.selected && this.selected.id === dispatchId) {
                        this.selected = null;
                    }
                    
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error archiving dispatch: ' + (data.message || 'Unknown error'), type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error archiving dispatch: ' + error.message, type: 'error' }
                }));
            });
        }
    }
}
</script>
</body>
</html>