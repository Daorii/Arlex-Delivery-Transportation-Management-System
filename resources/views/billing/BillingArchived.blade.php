<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADTMS - Archived Billing Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden" x-data="archivedBillingData()">

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
                    <a href="{{ route('billing.records') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-semibold text-gray-800">
                        Archived Billing Records
                    </h1>
                </div>
            </header>

            <!-- Archived Billing Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
                <div class="bg-white rounded-lg shadow">
                    
                    <!-- Table Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <h2 class="text-xl font-semibold text-gray-800">
                                <svg class="w-6 h-6 inline-block mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                Archived Billing Records
                            </h2>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billing ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prepared By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($archivedBillings as $billing)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $billing['billing_id'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $billing['client_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $billing['week_period_text'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $billing['prepared_by'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $billing['checked_by'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            ₱{{ number_format($billing['total_amount'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($billing['status'] == 'Pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold text-yellow-600">Pending</span>
                                            @elseif($billing['status'] == 'Paid')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold text-green-600">Paid</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold text-gray-600">{{ $billing['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <div class="flex space-x-2">
                                                <!-- Restore Button -->
                                                <button 
                                                    @click="selectedBilling = { id: {{ $billing['billing_id'] }}, client: '{{ $billing['client_name'] }}' }; showRestoreModal = true"
                                                    class="text-green-600 hover:text-green-900 transition-colors" 
                                                    title="Restore">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>

                                                <!-- Permanent Delete Button -->
                                                <button 
                                                    @click="selectedBilling = { id: {{ $billing['billing_id'] }}, client: '{{ $billing['client_name'] }}' }; showDeleteModal = true"
                                                    class="text-red-600 hover:text-red-900 transition-colors" 
                                                    title="Permanently Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                            </svg>
                                            <p class="mt-4 text-gray-500 text-sm">No archived billing records found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Restore Confirmation Modal -->
        <div x-show="showRestoreModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showRestoreModal" @click="showRestoreModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showRestoreModal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Restore Billing Record</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Are you sure you want to restore billing record for <span class="font-semibold text-gray-900" x-text="selectedBilling.client || 'N/A'"></span>? This will move it back to the active billing records list.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                        <button type="button" @click="showRestoreModal = false" class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="button" @click="restoreBilling()" class="w-full sm:w-auto px-5 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Yes, Restore
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permanent Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="showDeleteModal" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Permanently Delete</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Are you sure you want to permanently delete billing record for <span class="font-semibold text-gray-900" x-text="selectedBilling.client || 'N/A'"></span>? <span class="text-red-600 font-semibold">This action cannot be undone!</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-2 space-y-reverse sm:space-y-0 sm:space-x-3 border-t border-gray-200">
                        <button type="button" @click="showDeleteModal = false" class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="button" @click="deleteBilling()" class="w-full sm:w-auto px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Yes, Delete Permanently
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function archivedBillingData() {
            return {
                showRestoreModal: false,
                showDeleteModal: false,
                selectedBilling: {},
                
                restoreBilling() {
    fetch('/billing/' + this.selectedBilling.id + '/restore', {
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
                detail: { 
                    message: 'Billing restored successfully!', 
                    type: 'success' 
                } 
            }));
            setTimeout(() => window.location.reload(), 1000);
        } else {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { 
                    message: 'Error: ' + data.message, 
                    type: 'error' 
                } 
            }));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.dispatchEvent(new CustomEvent('notify', { 
            detail: { 
                message: 'Error restoring billing', 
                type: 'error' 
            } 
        }));
    });
},

deleteBilling() {
    fetch('/billing/' + this.selectedBilling.id, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { 
                    message: 'Billing permanently deleted!', 
                    type: 'success' 
                } 
            }));
            setTimeout(() => window.location.reload(), 1000);
        } else {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { 
                    message: 'Error: ' + data.message, 
                    type: 'error' 
                } 
            }));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.dispatchEvent(new CustomEvent('notify', { 
            detail: { 
                message: 'Error deleting billing', 
                type: 'error' 
            } 
        }));
    });
}
            };
        }
    </script>
</body>
</html>