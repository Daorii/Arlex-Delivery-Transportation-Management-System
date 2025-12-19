<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Billing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
    body { font-family: 'Inter', sans-serif; }
    .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .glass-effect { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    [x-cloak] { display: none !important; }
    
    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        
        #soa-printable, #soa-printable * {
            visibility: visible;
        }
        
        #soa-printable {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        
        .no-print {
            display: none !important;
        }
        
        @page {
            margin: 0.5cm;
        }
    }
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
            <h1 class="text-2xl font-semibold text-gray-800">Billing</h1>
            </div>
        </header>


        <!-- Billing Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8" x-data="billingApp()">
            <div class="flex space-x-6 h-full">
                
                <!-- Left: Client List -->
                <div class="w-1/3 bg-white rounded-2xl shadow-xl p-6 overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Clients</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full" x-text="totalClients + ' Total'"></span>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" placeholder="Search clients..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1">
                        <div class="space-y-2">
                            <template x-for="client in clients" :key="client.id">
                                <div @click="selectClient(client)" 
                                     :class="selectedClient && selectedClient.id === client.id ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg scale-105' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                     class="p-4 rounded-xl cursor-pointer transition-all duration-200 transform">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold" x-text="client.name"></p>
                                            <p class="text-xs opacity-75" x-text="'ID: ' + client.id"></p>
                                        </div>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right: Billing Form -->
                <div class="flex-1 bg-white rounded-2xl shadow-xl p-8 overflow-y-auto" x-show="selectedClient" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <template x-if="selectedClient">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-gray-800">Billing Information</h3>
                                <button @click="selectedClient = null" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <form @submit.prevent="generateSoA" class="space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Client Name -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Client Name</label>
                                        <div class="relative">
                                            <input type="text" x-model="selectedClient.name" readonly class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700 font-medium">
                                            <span class="absolute right-4 top-3 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded" x-text="'ID: ' + selectedClient.id"></span>
                                        </div>
                                    </div>

                                   <!-- SIPA Reference Number Dropdown -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            SIPA Reference Number <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <select 
                                                x-model="selectedClient.sipa_ref_no_input"
                                                @change="sipaIdChanged = true"
                                                class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                :disabled="!availableSipas.length || loadingSipa">
                                                <option value="">Select SIPA Reference Number</option>
                                                <template x-for="sipa in availableSipas" :key="sipa.sipa_id">
                                                    <option :value="sipa.sipa_ref_no" x-text="sipa.sipa_ref_no + ' - ' + sipa.type"></option>
                                                </template>
                                            </select>
                                            <button type="button" 
                                                    @click="fetchSipaDetails()"
                                                    :disabled="!selectedClient.sipa_ref_no_input || loadingSipa"
                                                    class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition">
                                                <span x-show="!loadingSipa">Fetch Details</span>
                                                <span x-show="loadingSipa">Loading...</span>
                                            </button>
                                        </div>
                                        <p x-show="sipaError" x-text="sipaError" class="text-red-500 text-sm mt-1"></p>
                                        <p x-show="!availableSipas.length && selectedClient" class="text-amber-600 text-sm mt-1">
                                            No SIPA records found for this client
                                        </p>
                                    </div>

                                    <!-- Trip Details with Scrollbar -->
                                    <div class="col-span-2" x-show="selectedClient.trip_count > 0">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Trip Details</label>
                                        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                                            <p class="text-sm font-medium text-gray-700 mb-2">
                                                <span class="text-blue-600 font-bold" x-text="selectedClient.trip_count"></span> verified trips found
                                            </p>
                                            <!-- Added max-height and overflow-y-auto for scrollbar -->
                                            <div class="max-h-48 overflow-y-auto space-y-1 pr-2">
                                                <template x-for="(trip, index) in selectedClient.trip_ids" :key="index">
                                                    <p class="text-xs text-gray-600 py-1" x-text="(index + 1) + '. ' + trip"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Week Period (Auto-filled) -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Week Period</label>
                                        <input type="text" 
                                            x-model="selectedClient.week_period" 
                                            readonly 
                                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700">
                                    </div>

                                    <!-- Total Weeks -->
                                    <div x-show="selectedClient.total_weeks">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Total Weeks</label>
                                        <input type="text" 
                                            x-model="selectedClient.total_weeks" 
                                            readonly 
                                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700">
                                    </div>

                                    <!-- Date Range -->
                                    <div class="col-span-2" x-show="selectedClient.start_date">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Date Range</label>
                                        <div class="flex gap-4">
                                            <input type="text" 
                                                x-model="selectedClient.start_date" 
                                                readonly 
                                                class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700">
                                            <span class="flex items-center text-gray-500">to</span>
                                            <input type="text" 
                                                x-model="selectedClient.end_date" 
                                                readonly 
                                                class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700">
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                        <select x-model="selectedClient.status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                            <option value="pending">Pending</option>
                                            <option value="processing">Processing</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>

                                    <!-- SIPA Reference Number (Auto-filled) -->
                                    <div x-show="selectedClient.sipa_ref_no">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">SIPA Ref No</label>
                                        <input type="text" 
                                            x-model="selectedClient.sipa_ref_no" 
                                            readonly 
                                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-700">
                                    </div>

                                    <!-- Prepared By -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prepared By <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="selectedClient.prepared_by" placeholder="Enter name" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    </div>

                                    <!-- Checked By -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Checked By <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="selectedClient.checked_by" placeholder="Enter name" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    </div>

                                    <!-- Created At -->
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Created At</label>
                                        <input type="text" x-model="selectedClient.created_at" readonly class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-600">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-4 pt-4">
                                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <span class="flex items-center justify-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>Generate Statement of Account</span>
                                        </span>
                                    </button>
                                    <button type="button" @click="resetForm" class="px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                                        Reset
                                    </button>
                                </div>
                            </form>

                            <!-- SoA Display -->
                            <div x-show="showSoA" x-transition class="mt-8">
                                <div id="soa-printable" class="bg-white rounded-xl shadow-2xl p-8 border-2 border-gray-200" style="font-family: Arial, sans-serif;">
                                    <!-- Header -->
                                    <div class="text-center mb-6">
                                        <h2 class="text-2xl font-bold text-gray-800">Arlex Delivery Services</h2>
                                        <p class="text-sm text-gray-600">Sasa, Davao City</p>
                                        <p class="text-sm text-gray-600">Tin no.: 747-971-831</p>
                                    </div>

                                    <div class="flex justify-between items-start mb-6">
                                        <div>
                                            <p class="text-sm"><strong>SOA #</strong> <span x-text="soaData?.soa_number"></span></p>
                                            <p class="text-sm"><strong>Date:</strong> <span x-text="soaData?.date"></span></p>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <p class="text-sm font-bold mb-2">TO:</p>
                                        <div class="pl-4">
                                            <p class="text-sm font-semibold" x-text="soaData?.client_name"></p>
                                            <p class="text-sm" x-text="soaData?.client_address"></p>
                                        </div>
                                        <p class="text-sm mt-2"><strong>SIPA REF#:</strong> <span x-text="soaData?.sipa_ref_no"></span></p>
                                    </div>

                                    <h3 class="text-center text-lg font-bold mb-4">Statement of Account</h3>

                                    <!-- Table -->
                                    <div class="overflow-x-auto mb-6">
                                        <table class="min-w-full border-collapse border border-gray-400 text-sm">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">DATE</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">CONTAINER #</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">EIR #</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">SIZE</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">DESTINATION</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-right font-semibold">AMOUNT</th>
                                                    <th class="border border-gray-400 px-3 py-2 text-left font-semibold">REMARKS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(item, index) in soaData?.items" :key="index">
                                                    <tr>
                                                        <td class="border border-gray-400 px-3 py-2" x-text="item.delivery_date"></td>
                                                        <td class="border border-gray-400 px-3 py-2" x-text="item.container_no"></td>
                                                        <td class="border border-gray-400 px-3 py-2" x-text="item.eir_no"></td>
                                                        <td class="border border-gray-400 px-3 py-2 text-center" x-text="item.size"></td>
                                                        <td class="border border-gray-400 px-3 py-2" x-text="item.destination"></td>
                                                        <td class="border border-gray-400 px-3 py-2 text-right" x-text="item.amount"></td>
                                                        <td class="border border-gray-400 px-3 py-2" x-text="item.remarks"></td>
                                                    </tr>
                                                </template>
                                                <tr class="bg-gray-100 font-bold">
                                                    <td colspan="5" class="border border-gray-400 px-3 py-2 text-right">TOTAL</td>
                                                    <td class="border border-gray-400 px-3 py-2 text-right">Php <span x-text="soaData?.total_amount"></span></td>
                                                    <td class="border border-gray-400 px-3 py-2"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex justify-between mt-8">
                                        <div>
                                            <p class="text-sm"><strong>Prepared by:</strong></p>
                                            <p class="text-sm mt-1" x-text="soaData?.prepared_by"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm"><strong>Checked by:</strong></p>
                                            <p class="text-sm mt-1" x-text="soaData?.checked_by"></p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-3 mt-8 pt-6 border-t no-print">
                                        <button @click="window.print()" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                                            <span class="flex items-center justify-center space-x-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                                <span>Print SOA</span>
                                            </span>
                                        </button>
                                        <button @click="showSoA = false" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4 mt-6 no-print" x-show="showSoA">
                                <button @click="saveSOA()" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition">
<span class="flex items-center justify-center space-x-2">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
</svg>
<span>Save SOA</span>
</span>
</button>
</div>
</div>
</template>
</div>
            <!-- Empty State -->
            <div x-show="!selectedClient" class="flex-1 flex items-center justify-center bg-white rounded-2xl shadow-xl">
                <div class="text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Client Selected</h3>
                    <p class="text-gray-500">Select a client from the list to view billing details</p>
                </div>
            </div>
        </div>
    </main>
</div>
</div>
<script>
    function billingApp() {
    return {
        clients: @json($clients ?? []),
        totalClients: @json($totalClients ?? 0),
        selectedClient: null,
        availableSipas: [],
        showSoA: false,
        soaData: null,
        loadingSipa: false,
        sipaError: '',
        sipaIdChanged: false,

        selectClient(client) {
    this.selectedClient = JSON.parse(JSON.stringify(client));
    this.selectedClient.sipa_ref_no_input = '';
    this.selectedClient.prepared_by = 'Arvie Therese Francisco';  // ← ADD THIS
    this.selectedClient.checked_by = 'Arlex Francisco';           // ← ADD THIS
    this.showSoA = false;
    this.sipaError = '';
    this.availableSipas = [];
    this.fetchAvailableSipas();
},

        async fetchAvailableSipas() {
            if (!this.selectedClient) return;

            try {
                const response = await fetch(`/billing/get-client-sipas/${this.selectedClient.id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.availableSipas = data.sipas;
                } else {
                    console.error('Error fetching SIPAs:', data.message);
                    this.availableSipas = [];
                }
            } catch (error) {
                console.error('Error:', error);
                this.availableSipas = [];
            }
        },

        fetchSipaDetails() {
            if (!this.selectedClient) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'No client selected', type: 'error' }
                }));
                return;
            }

            if (!this.selectedClient.sipa_ref_no_input) {
                this.sipaError = 'Please enter a SIPA Reference Number';
                return;
            }

            this.loadingSipa = true;
            this.sipaError = '';

            fetch('/billing/fetch-sipa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    sipa_ref_no: this.selectedClient.sipa_ref_no_input,
                    client_id: this.selectedClient.id
                })
            })
            .then(response => response.json())
            .then(data => {
                this.loadingSipa = false;
                
                if(data.success) {
                    this.selectedClient.sipa_id = data.data.sipa_id;
                    this.selectedClient.trip_ids = data.data.trip_ids;
                    this.selectedClient.trip_count = data.data.trip_count;
                    this.selectedClient.week_period = data.data.week_period;
                    this.selectedClient.total_weeks = data.data.total_weeks;
                    this.selectedClient.start_date = data.data.start_date;
                    this.selectedClient.end_date = data.data.end_date;
                    this.selectedClient.sipa_ref_no = data.data.sipa_ref_no;
                    this.sipaIdChanged = false;
                    
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'SIPA details loaded successfully!', type: 'success' }
                    }));
                } else {
                    this.sipaError = data.message;
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: data.message, type: 'error' }
                    }));
                    
                    this.selectedClient.sipa_id = '';
                    this.selectedClient.trip_ids = [];
                    this.selectedClient.trip_count = 0;
                    this.selectedClient.week_period = '';
                    this.selectedClient.total_weeks = '';
                    this.selectedClient.start_date = '';
                    this.selectedClient.end_date = '';
                    this.selectedClient.sipa_ref_no = '';
                }
            })
            .catch(error => {
                this.loadingSipa = false;
                this.sipaError = 'Error fetching SIPA details';
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error fetching SIPA details', type: 'error' }
                }));
            });
        },

        generateSoA() {
            if (!this.selectedClient || !this.selectedClient.sipa_id) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Please enter and fetch SIPA details first', type: 'error' }
                }));
                return;
            }

            if (!this.selectedClient.prepared_by || !this.selectedClient.checked_by) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Please fill in Prepared By and Checked By fields', type: 'error' }
                }));
                return;
            }

            if (this.selectedClient.trip_count === 0) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'No verified trips found for this SIPA', type: 'error' }
                }));
                return;
            }

            fetch('/billing/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.selectedClient)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    this.soaData = data.data;
                    this.showSoA = true;
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Statement of Account generated successfully!', type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error generating SoA: ' + data.message, type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error generating SoA', type: 'error' }
                }));
            });
        },

        resetForm() {
    if (this.selectedClient) {
        const clientId = this.selectedClient.id;
        const original = this.clients.find(c => c.id === clientId);
        this.selectedClient = JSON.parse(JSON.stringify(original));
        this.selectedClient.sipa_ref_no_input = '';
        this.selectedClient.prepared_by = 'Arvie Therese Francisco';  // ← ADD THIS
        this.selectedClient.checked_by = 'Arlex Francisco';           // ← ADD THIS
        this.showSoA = false;
        this.sipaError = '';
        this.fetchAvailableSipas();
    }
},
            
        saveSOA() {
            if (!this.soaData) return;

            fetch('/billing/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    client_id: this.selectedClient.id,
                    sipa_id: this.selectedClient.sipa_id,
                    sipa_ref_no: this.selectedClient.sipa_ref_no,
                    week_period: this.soaData.week_period,
                    prepared_by: this.soaData.prepared_by,
                    checked_by: this.soaData.checked_by,
                    total_amount: parseFloat(this.soaData.total_amount.replace(/,/g, '')),
                    status: this.selectedClient.status
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'SOA saved successfully with ID: ' + data.billing_id, type: 'success' }
                    }));
                    this.showSoA = false;
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error saving SOA: ' + data.message, type: 'error' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error saving SOA', type: 'error' }
                }));
            });
        }
    }
}
</script>
</body>
</html>
