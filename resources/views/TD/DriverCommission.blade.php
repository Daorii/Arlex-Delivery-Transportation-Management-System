<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ADTMS - Driver Commission</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <h1 class="text-2xl font-semibold text-gray-800">Driver Commission</h1>
            </div>
        </header>

        <!-- Main -->
        <main class="flex-1 overflow-y-auto p-8" x-data="driverApp()">
            <div class="flex space-x-6 h-full">

                <!-- Left: Drivers list (1/3) -->
                <div class="w-1/3 bg-white rounded-2xl shadow-xl p-6 overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Drivers</h3>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full" x-text="drivers.length + ' Total'"></span>
                    </div>

                    <!-- Search -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" placeholder="Search driver..." x-model="query" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-y-auto flex-1">
                        <div class="space-y-2">
                            <template x-for="driver in filteredDrivers()" :key="driver.driver_id">
                                <div @click="selectDriver(driver)" :class="selectedDriver && selectedDriver.driver_id === driver.driver_id ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'" class="p-4 rounded-xl cursor-pointer transition-all duration-200 transform">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold" x-text="driver.driver_name"></p>
                                            <p class="text-xs opacity-75" x-text="'ID: ' + driver.driver_id"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold" x-text="driver.approved_count + ' approved'"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right: Commission details (2/3) -->
                <div class="flex-1 bg-white rounded-2xl shadow-xl p-8 overflow-y-auto">

                    <!-- Empty state when no driver selected -->
                    <div x-show="!selectedDriver" class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500">
                            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold">No Driver Selected</h3>
                            <p class="text-gray-500">Select a driver from the list to view weekly periods and generate commission.</p>
                        </div>
                    </div>

                    <!-- Selected driver panel -->
                    <div x-show="selectedDriver" x-transition>

                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800" x-text="selectedDriver.driver_name"></h2>
                                <p class="text-sm text-gray-500 mt-1">Driver ID: <span x-text="selectedDriver.driver_id"></span></p>
                            </div>

                            <button @click="resetPanel" class="p-2 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Commission Rate Input -->
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Commission Rate (%)</label>
                            <input type="number" step="0.01" x-model.number="commissionRate" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 bg-white text-lg font-semibold" placeholder="e.g., 12" />
                        </div>

                        <!-- Loading State -->
                        <div x-show="loadingWeeks" class="text-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-500">Loading weekly periods...</p>
                        </div>

                        <!-- Weekly Periods List -->
                        <div x-show="!loadingWeeks && weeklyPeriods.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="font-semibold">No unsaved periods found</p>
                            <p class="text-sm">All approved trips have been processed or no approved trips yet.</p>
                        </div>

                        <div x-show="!loadingWeeks && weeklyPeriods.length > 0">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Select Weekly Period</h3>
                            <div class="space-y-3">
                                <template x-for="period in weeklyPeriods" :key="period.week_start">
                                    <div @click="selectWeekPeriod(period)" 
                                         :class="selectedPeriod && selectedPeriod.week_start === period.week_start ? 'bg-gradient-to-r from-green-500 to-green-600 text-white border-green-600' : 'bg-white hover:bg-gray-50 text-gray-800 border-gray-200'"
                                         class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-bold text-lg" x-text="period.week_label"></p>
                                                <p class="text-sm opacity-90" x-text="period.trip_count + ' trip(s)'"></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-lg" x-text="'₱' + parseFloat(period.total_amount).toLocaleString(undefined, {minimumFractionDigits:2})"></p>
                                                <p class="text-xs opacity-75">Total Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Selected Period Details -->
                        <div x-show="selectedPeriod" x-transition class="mt-6">
                            <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 mb-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">
                                    Selected Period: <span x-text="selectedPeriod.week_label"></span>
                                </h3>
                                
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="bg-white rounded-lg p-3 text-center">
                                        <p class="text-sm text-gray-600">Trips</p>
                                        <p class="text-2xl font-bold text-gray-800" x-text="selectedPeriod.trip_count"></p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 text-center">
                                        <p class="text-sm text-gray-600">Total Amount</p>
                                        <p class="text-2xl font-bold text-green-600" x-text="'₱' + parseFloat(selectedPeriod.total_amount).toLocaleString(undefined, {minimumFractionDigits:2})"></p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 text-center">
                                        <p class="text-sm text-gray-600">Commission</p>
                                        <p class="text-2xl font-bold text-blue-600" x-text="'₱' + calculateCommission().toLocaleString(undefined, {minimumFractionDigits:2})"></p>
                                    </div>
                                </div>

                                <!-- Trip Details Table -->
                                <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                                    <div class="bg-gray-50 px-4 py-3 border-b">
                                        <h4 class="font-semibold text-gray-800">Trip Details</h4>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Dispatch ID</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Container No</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">EIR No</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Type</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Size</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Price</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Commission</th>
                                                    <th class="px-3 py-2 text-xs text-gray-500 text-center">Delivery Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                <template x-for="trip in selectedPeriod.trips" :key="trip.detail_id">
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.dispatch_id"></td>
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.container_no"></td>
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.eir_no"></td>
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.type"></td>
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.size"></td>
                                                        <td class="px-3 py-2 text-sm font-semibold text-center" x-text="'₱'+parseFloat(trip.price).toLocaleString(undefined,{minimumFractionDigits:2})"></td>
                                                        <td class="px-3 py-2 text-sm font-semibold text-green-700 text-center" x-text="'₱'+(parseFloat(trip.price)*commissionRate/100).toLocaleString(undefined,{minimumFractionDigits:2})"></td>
                                                        <td class="px-3 py-2 text-sm text-center" x-text="trip.delivery_date"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="mt-6 flex justify-end">
                                    <button @click="saveCommission()" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 shadow-md transition-all hover:shadow-lg">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Save Commission for this Period
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </main>

    </div>
</div>

<script>
function driverApp() {
    return {
        query: '',
        drivers: @json($drivers ?? []),
        selectedDriver: null,
        weeklyPeriods: [],
        selectedPeriod: null,
        commissionRate: 12,
        loadingWeeks: false,

        filteredDrivers() {
            if (!this.query) return this.drivers;
            return this.drivers.filter(d => d.driver_name.toLowerCase().includes(this.query.toLowerCase()));
        },

        selectDriver(driver) {
            this.selectedDriver = JSON.parse(JSON.stringify(driver));
            this.weeklyPeriods = [];
            this.selectedPeriod = null;
            this.loadWeeklyPeriods();
        },

        loadWeeklyPeriods() {
            if (!this.selectedDriver) return;

            this.loadingWeeks = true;
            
            fetch(`/commissions/driver-weeks/${this.selectedDriver.driver_id}`)
                .then(r => r.json())
                .then(data => {
                    this.loadingWeeks = false;
                    if (data.success) {
                        this.weeklyPeriods = data.periods || [];
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: data.message || 'Error loading weekly periods', type: 'error' }
                        }));
                        this.weeklyPeriods = [];
                    }
                })
                .catch(err => {
                    this.loadingWeeks = false;
                    console.error(err);
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Error loading weekly periods', type: 'error' }
                    }));
                });
        },

        selectWeekPeriod(period) {
            this.selectedPeriod = period;
        },

        calculateCommission() {
            if (!this.selectedPeriod) return 0;
            return this.selectedPeriod.total_amount * (this.commissionRate / 100);
        },

        saveCommission() {
            if (!this.selectedPeriod || !this.selectedDriver) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Please select a period first', type: 'error' }
                }));
                return;
            }

            if (!this.commissionRate || this.commissionRate <= 0) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Please enter a valid commission rate', type: 'error' }
                }));
                return;
            }

            const payload = {
                driver_id: this.selectedDriver.driver_id,
                rate: this.commissionRate,
                from: this.selectedPeriod.week_start,
                to: this.selectedPeriod.week_end,
                items: this.selectedPeriod.trips
            };

            fetch('/commissions/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: 'Commission saved successfully!', type: 'success' }
                    }));
                    // Reload weekly periods to refresh the list
                    this.selectedPeriod = null;
                    this.loadWeeklyPeriods();
                } else {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: data.message || 'Error saving commission', type: 'error' }
                    }));
                }
            })
            .catch(err => {
                console.error(err);
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: 'Error saving commission', type: 'error' }
                }));
            });
        },

        resetPanel() {
            this.selectedDriver = null;
            this.weeklyPeriods = [];
            this.selectedPeriod = null;
            this.commissionRate = 12;
        }
    }
}
</script>
</body>
</html>