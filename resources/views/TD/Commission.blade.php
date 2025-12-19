<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Commission Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
<div class="flex h-screen overflow-hidden">

    <!-- TOAST NOTIFICATION -->
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

    @include('partials.admin_sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <h1 class="text-2xl font-semibold text-gray-800">Commission Records</h1>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-8" x-data="commissionApp()">
            <div class="flex space-x-6 h-full">
                
                <!-- Left: Driver List -->
                <div class="w-1/3 bg-white rounded-2xl shadow-xl p-6 overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Drivers</h3>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full" x-text="drivers.length + ' Total'"></span>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" 
                                   x-model="searchQuery"
                                   @input="filterDrivers"
                                   placeholder="Search drivers..." 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1">
                        <div class="space-y-2">
                            <template x-for="driver in filteredDrivers" :key="driver.driver_id">
                                <div @click="selectDriver(driver)" 
                                     :class="selectedDriver && selectedDriver.driver_id === driver.driver_id ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg scale-105' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                     class="p-4 rounded-xl cursor-pointer transition-all duration-200 transform">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold" x-text="driver.driver_name"></p>
                                            <p class="text-xs opacity-75" x-text="driver.commission_count + ' commission(s)'"></p>
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

                <!-- Right: Commission Records -->
                <div class="flex-1 bg-white rounded-2xl shadow-xl p-8 overflow-y-auto" 
                     x-show="selectedDriver" 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0 transform scale-95" 
                     x-transition:enter-end="opacity-100 transform scale-100">
                    
                    <template x-if="selectedDriver">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-gray-800">
                                    Commission Records — <span x-text="selectedDriver.driver_name"></span>
                                </h3>
                                <button @click="selectedDriver = null" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- TABS -->
                            <div class="mb-6">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8">
                                        <button @click="activeTab = 'pending'" 
                                                :class="activeTab === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            <div class="flex items-center space-x-2">
                                                <span>Pending Commissions</span>
                                                <span class="px-2 py-1 text-xs rounded-full" 
                                                      :class="activeTab === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                                                      x-text="pendingCommissions().length"></span>
                                            </div>
                                        </button>
                                        <button @click="activeTab = 'history'" 
                                                :class="activeTab === 'history' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                            <div class="flex items-center space-x-2">
                                                <span>Payment History</span>
                                                <span class="px-2 py-1 text-xs rounded-full" 
                                                      :class="activeTab === 'history' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                                      x-text="getGroupedHistory().length"></span>
                                            </div>
                                        </button>
                                    </nav>
                                </div>
                            </div>

                            <!-- PENDING TAB CONTENT -->
                            <div x-show="activeTab === 'pending'">
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                                        <h4 class="text-lg font-semibold text-gray-800">Pending Commissions</h4>
                                    </div>

                                    <div x-show="loadingCommissions" class="p-12 text-center">
                                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                        <p class="text-gray-500 mt-4">Loading commissions...</p>
                                    </div>

                                    <div x-show="!loadingCommissions" class="overflow-x-auto">
                                        <table class="w-full text-center text-sm">
                                            <thead class="bg-gray-50 border-b">
                                                <tr>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Dispatch ID</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Trip Amount</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Rate</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Commission</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700 whitespace-nowrap">Week Period</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                                                    <th class="px-6 py-4 font-semibold text-gray-700">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <template x-for="c in pendingCommissions()" :key="c.commission_id">
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-5" x-text="c.dispatch_id"></td>
                                                        <td class="px-6 py-5 font-semibold text-green-600" x-text="`₱${parseFloat(c.total_trip_amount).toLocaleString()}`"></td>
                                                        <td class="px-6 py-5" x-text="c.commission_rate + '%'"></td>
                                                        <td class="px-6 py-5 font-bold text-green-700" x-text="`₱${parseFloat(c.commission_amount).toLocaleString()}`"></td>
                                                        <td class="px-6 py-5 text-xs whitespace-nowrap" x-text="c.week_period_text"></td>
                                                        <td class="px-6 py-5">
                                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                                                Pending
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-5">
                                                            <div class="flex space-x-2 justify-center">
                                                                <button @click="markAsPaid(c)" class="px-3 py-1 bg-green-600 text-white text-sm rounded-md hover:bg-green-700" title="Mark as Paid">
                                                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="pendingCommissions().length === 0">
                                                    <td colspan="7" class="py-8 text-center text-gray-500">
                                                        <div class="flex flex-col items-center justify-center">
                                                            <svg class="w-16 h-16 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <p class="font-semibold">All caught up!</p>
                                                            <p class="text-sm">No pending commissions</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div x-show="!loadingCommissions && pendingCommissions().length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                                        <div class="bg-blue-50 border border-blue-200 px-5 py-3 rounded-xl">
                                            <p class="text-sm text-gray-600">Total Pending Commission:</p>
                                            <h3 class="text-xl font-bold text-green-700" x-text="`₱${totalPendingCommission().toLocaleString()}`"></h3>
                                        </div>
                                        <button @click="releaseAll()" class="px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors shadow-md hover:shadow-lg">
                                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Release All Pending
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- HISTORY TAB CONTENT -->
                            <div x-show="activeTab === 'history'">
                                <!-- Date Filter -->
                                <div class="mb-4 flex items-center justify-between">
                                    <div class="flex gap-2">
                                        <button @click="historyFilter = '30'" 
                                                :class="historyFilter === '30' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                                            Last 30 Days
                                        </button>
                                        <button @click="historyFilter = '90'" 
                                                :class="historyFilter === '90' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                                            Last 3 Months
                                        </button>
                                        <button @click="historyFilter = 'all'" 
                                                :class="historyFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition">
                                            All Time
                                        </button>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Showing <span class="font-semibold" x-text="getGroupedHistory().length"></span> week period(s)
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                                    <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-green-200">
                                        <h4 class="text-lg font-semibold text-gray-800">Payment History (Grouped by Week)</h4>
                                    </div>

                                    <div x-show="loadingCommissions" class="p-12 text-center">
                                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
                                        <p class="text-gray-500 mt-4">Loading history...</p>
                                    </div>

                                    <div x-show="!loadingCommissions" class="divide-y divide-gray-200">
                                        <template x-for="(group, index) in getGroupedHistory()" :key="index">
                                            <div>
                                                <!-- Week Summary Row -->
                                                <div @click="group.expanded = !group.expanded" 
                                                     class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-4">
                                                            <svg :class="group.expanded ? 'rotate-90' : ''" 
                                                                 class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                            <div>
                                                                <p class="font-bold text-gray-800" x-text="group.week_period"></p>
                                                                <p class="text-sm text-gray-500" x-text="group.trips.length + ' trip(s)'"></p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-6">
                                                            <div class="text-right">
                                                                <p class="text-sm text-gray-600">Total Commission</p>
                                                                <p class="text-xl font-bold text-green-700" x-text="`₱${group.total_commission.toLocaleString()}`"></p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm text-gray-600">Payment Date</p>
                                                                <p class="text-sm font-medium text-gray-800" x-text="formatDate(group.paid_at)"></p>
                                                            </div>
                                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold inline-flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Paid
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Expanded Details -->
                                                <div x-show="group.expanded" 
                                                     x-collapse 
                                                     class="bg-gray-50 border-t border-gray-200">
                                                    <div class="px-6 py-4">
                                                        <h5 class="font-semibold text-gray-700 mb-3">Trip Details</h5>
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-sm">
                                                                <thead class="bg-white border-b">
                                                                    <tr>
                                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Dispatch ID</th>
                                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Trip Amount</th>
                                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Rate</th>
                                                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Commission</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-gray-200">
                                                                    <template x-for="trip in group.trips" :key="trip.commission_id">
                                                                        <tr class="hover:bg-white">
                                                                            <td class="px-4 py-3" x-text="trip.dispatch_id"></td>
                                                                            <td class="px-4 py-3 font-semibold text-green-600" x-text="`₱${parseFloat(trip.total_trip_amount).toLocaleString()}`"></td>
                                                                            <td class="px-4 py-3" x-text="trip.commission_rate + '%'"></td>
                                                                            <td class="px-4 py-3 font-bold text-green-700" x-text="`₱${parseFloat(trip.commission_amount).toLocaleString()}`"></td>
                                                                        </tr>
                                                                    </template>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="getGroupedHistory().length === 0" class="py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="font-semibold">No payment history</p>
                                                <p class="text-sm">Paid commissions will appear here</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="!loadingCommissions && getGroupedHistory().length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                        <div class="bg-green-50 border border-green-200 px-5 py-3 rounded-xl inline-block">
                                            <p class="text-sm text-gray-600">Total Paid (Filtered Period):</p>
                                            <h3 class="text-xl font-bold text-green-700" x-text="`₱${getTotalPaidFiltered().toLocaleString()}`"></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Empty State -->
                <div x-show="!selectedDriver" class="flex-1 flex items-center justify-center bg-white rounded-2xl shadow-xl">
                    <div class="text-center">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
</svg>
<h3 class="text-xl font-semibold text-gray-700 mb-2">No Driver Selected</h3>
<p class="text-gray-500">Select a driver from the list to view commission records</p>
</div>
</div>
</div>
</main>
</div>
</div>
<script>
function commissionApp() {
    return {
        drivers: @json($drivers),
        filteredDrivers: @json($drivers),
        selectedDriver: null,
        commissions: [],
        searchQuery: '',
        loadingCommissions: false,
        activeTab: 'pending',
        historyFilter: '30', // Default to last 30 days

        filterDrivers() {
            if (!this.searchQuery) {
                this.filteredDrivers = this.drivers;
            } else {
                this.filteredDrivers = this.drivers.filter(driver => 
                    driver.driver_name.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
        },

        async selectDriver(driver) {
            this.selectedDriver = driver;
            this.commissions = [];
            this.loadingCommissions = true;
            this.activeTab = 'pending';

            try {
                const response = await fetch(`/commissions/view-driver/${driver.driver_id}`);
                const data = await response.json();
                
                if (data.success) {
                    this.commissions = data.data.map(c => ({
                        ...c,
                        editing: false,
                        originalStatus: c.status
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Failed to load commissions', type: 'error' }
                    }));
                }
            } catch (error) {
                console.error('Error loading commissions:', error);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Error loading commissions', type: 'error' }
                }));
            } finally {
                this.loadingCommissions = false;
            }
        },

        pendingCommissions() {
            return this.commissions.filter(c => c.status === 'Pending');
        },

        paidCommissions() {
            return this.commissions.filter(c => c.status === 'Paid');
        },

        getFilteredPaidCommissions() {
            const paid = this.paidCommissions();
            if (this.historyFilter === 'all') return paid;

            const days = parseInt(this.historyFilter);
            const cutoffDate = new Date();
            cutoffDate.setDate(cutoffDate.getDate() - days);

            return paid.filter(c => {
                if (!c.paid_at) return false;
                const paidDate = new Date(c.paid_at);
                return paidDate >= cutoffDate;
            });
        },

        getGroupedHistory() {
            const filtered = this.getFilteredPaidCommissions();
            const grouped = {};

            filtered.forEach(commission => {
                const period = commission.week_period_text;
                if (!grouped[period]) {
                    grouped[period] = {
                        week_period: period,
                        trips: [],
                        total_commission: 0,
                        paid_at: commission.paid_at,
                        expanded: false
                    };
                }
                grouped[period].trips.push(commission);
                grouped[period].total_commission += parseFloat(commission.commission_amount) || 0;
            });

            // Convert to array and sort by paid_at (most recent first)
            return Object.values(grouped).sort((a, b) => {
                return new Date(b.paid_at) - new Date(a.paid_at);
            });
        },

        getTotalPaidFiltered() {
            return this.getFilteredPaidCommissions().reduce((sum, c) => sum + (parseFloat(c.commission_amount) || 0), 0);
        },

        totalPendingCommission() {
            return this.pendingCommissions().reduce((sum, c) => sum + (parseFloat(c.commission_amount) || 0), 0);
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric'
            });
        },

        async markAsPaid(c) {
            try {
                const response = await fetch(`/commissions/update-status/${c.commission_id}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: 'Paid' })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    c.status = 'Paid';
                    c.paid_at = new Date().toISOString();
                    
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Commission marked as paid successfully', type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Failed to update commission status', type: 'error' }
                    }));
                }
            } catch (error) {
                console.error('Error updating status:', error);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Error updating commission status', type: 'error' }
                }));
            }
        },

        async releaseAll() {
            if (!confirm('Are you sure you want to release all pending commissions for this driver?')) {
                return;
            }

            try {
                const response = await fetch(`/commissions/release-all/${this.selectedDriver.driver_id}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    const now = new Date().toISOString();
                    this.commissions = this.commissions.map(c => {
                        if (c.status === 'Pending') {
                            return { ...c, status: 'Paid', paid_at: now };
                        }
                        return c;
                    });
                    
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: `Successfully released ${data.updated_count} commission(s)`, type: 'success' }
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Failed to release commissions', type: 'error' }
                    }));
                }
            } catch (error) {
                console.error('Error releasing commissions:', error);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Error releasing commissions', type: 'error' }
                }));
            }
        }
    };
}
</script>
</body>
</html>
