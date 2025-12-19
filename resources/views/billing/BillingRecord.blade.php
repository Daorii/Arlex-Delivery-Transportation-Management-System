<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Billing Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
    body { font-family: 'Inter', sans-serif; }
    
    [x-cloak] { display: none !important; }
        
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            
            #soa-print-content, #soa-print-content * {
                visibility: visible;
            }
            
            #soa-print-content {
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
<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('partials.admin_sidebar')

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

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
    <div class="flex items-center space-x-4 h-[55px] w-auto">
        <h1 class="text-2xl font-semibold text-gray-800">Billing Records</h1>
    </div>
</header>

        <!-- Billing Records Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8"
              x-data="{
    billings: {{ Illuminate\Support\Js::from($billingRecords->items()) }},
    showViewModal: false,
    showEditModal: false,
    showArchiveModal: false,
    selectedBilling: {},
    soaData: null,
    loadingView: false,
    
    openView(billing) {
    this.loadingView = true;
    this.showViewModal = true;
    
    fetch('/billing/view/' + billing.billing_id)
        .then(response => response.json())
        .then(data => {
            this.loadingView = false;
            if(data.success) {
                this.soaData = data.data;
            } else {
                this.$dispatch('notify', { 
                    message: 'Error loading billing details: ' + data.message, 
                    type: 'error' 
                });
                this.showViewModal = false;
            }
        })
        .catch(error => {
            this.loadingView = false;
            console.error('Error:', error);
            this.$dispatch('notify', { 
                message: 'Error loading billing details', 
                type: 'error' 
            });
            this.showViewModal = false;
        });
},
    
    openEdit(billing) {
        this.selectedBilling = { ...billing };
        this.showEditModal = true;
    },
    
    updateStatus() {
    fetch('/billing/update-status/' + this.selectedBilling.billing_id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({
            status: this.selectedBilling.status
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            this.$dispatch('notify', { 
                message: 'Status updated successfully!', 
                type: 'success' 
            });
            
            const index = this.billings.findIndex(b => b.billing_id === this.selectedBilling.billing_id);
            if(index !== -1) {
                this.billings[index].status = this.selectedBilling.status;
            }
            
            this.showEditModal = false;
        } else {
            this.$dispatch('notify', { 
                message: 'Error updating status: ' + data.message, 
                type: 'error' 
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        this.$dispatch('notify', { 
            message: 'Error updating status', 
            type: 'error' 
        });
    });
},
    
    archiveBilling() {
    fetch('/billing/' + this.selectedBilling.billing_id + '/archive', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Use notification toast instead of alert
            this.$dispatch('notify', { 
                message: 'Billing archived successfully!', 
                type: 'success' 
            });
            
            const index = this.billings.findIndex(b => b.billing_id === this.selectedBilling.billing_id);
            if(index !== -1) {
                this.billings.splice(index, 1);
            }
            
            this.showArchiveModal = false;
        } else {
            this.$dispatch('notify', { 
                message: 'Error archiving billing: ' + data.message, 
                type: 'error' 
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        this.$dispatch('notify', { 
            message: 'Error archiving billing', 
            type: 'error' 
        });
    });
}
}">

                

            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">All Billing Records</h2>
        <p class="text-sm text-gray-600 mt-1">View and manage client billing records</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Search Bar -->
        <form action="{{ route('billing.records') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search billings..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('billing.records') }}" 
                   class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                   title="Clear search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            @endif
        </form>
        <div>
            <a href="{{ route('billing.archived') }}" 
               class="flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                View Archived
            </a>
        </div>
    </div>
</div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-800">Billing Records List</h3>
                </div>

                <!-- Empty State -->
                <div x-show="billings.length === 0 && {{ $billingRecords->total() === 0 ? 'true' : 'false' }}" class="p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Billing Records Yet</h3>
                    <p class="text-gray-500">Billing records will appear here once they are created.</p>
                </div>

                <!-- Table -->
                <div x-show="billings.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Billing ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Week Period</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prepared By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Checked By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="billing in billings" :key="billing.billing_id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap" x-text="'#' + billing.billing_id"></td>
                                <td class="px-6 py-4 whitespace-nowrap" x-text="billing.client_name || 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap" x-text="billing.week_period_text"></td>
                                <td class="px-6 py-4 whitespace-nowrap" x-text="billing.prepared_by"></td>
                                <td class="px-6 py-4 whitespace-nowrap" x-text="billing.checked_by || 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-green-600 font-semibold"
                                    x-text="'₱' + parseFloat(billing.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="{
                                            'text-yellow-800': billing.status.toLowerCase() === 'draft',
                                            'text-blue-800': billing.status.toLowerCase() === 'pending',
                                            'text-green-800': billing.status.toLowerCase() === 'approved',
                                            'text-red-800': billing.status.toLowerCase() === 'cancelled'
                                        }"
                                        class="text-sm font-semibold"
                                        x-text="billing.status.charAt(0).toUpperCase() + billing.status.slice(1)">
                                    </span>
                                </td>
                                <!-- Actions with Icons -->
<td class="px-6 py-4 whitespace-nowrap text-sm">
    <div class="flex space-x-2">
        <!-- View Button (Eye Icon) -->
        <button @click="openView(billing)" 
                class="text-blue-600 hover:text-blue-900 transition-colors" 
                title="View">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
        
        <!-- Edit Button -->
        <button @click="openEdit(billing)" 
                class="text-green-600 hover:text-green-900 transition-colors" 
                title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>

        <!-- Archive Button -->
        <button @click="selectedBilling = billing; showArchiveModal = true" 
                class="text-orange-600 hover:text-orange-900 transition-colors" 
                title="Archive">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
        </button>
    </div>
</td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
<div x-show="{{ $billingRecords->total() > 0 ? 'true' : 'false' }}" class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $billingRecords->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $billingRecords->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $billingRecords->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered)</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($billingRecords->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $billingRecords->appends(['search' => $search])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($billingRecords->getUrlRange(1, $billingRecords->lastPage()) as $page => $url)
            @if ($page == $billingRecords->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $billingRecords->appends(['search' => $search])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($billingRecords->hasMorePages())
            <a href="{{ $billingRecords->appends(['search' => $search])->nextPageUrl() }}" 
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

            <!-- VIEW BILLING MODAL (Full SOA Display) -->
            <div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto" @click.away="showViewModal = false">
                    
                    <!-- Loading State -->
                    <div x-show="loadingView" class="p-12 text-center">
                        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-600">Loading billing details...</p>
                    </div>

                    <!-- SOA Content -->
                    <div x-show="!loadingView && soaData" id="soa-print-content" class="p-8">
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
                            <button @click="showViewModal = false" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDIT STATUS MODAL -->
            <div x-show="showEditModal"x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
<div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6" @click.away="showEditModal = false">
    <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Edit Billing Status</h2>
                    <button class="text-gray-500 hover:text-gray-700" @click="showEditModal = false">✕</button>
                </div>

                <!-- Modal Body -->
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500">Billing ID</p>
                        <p class="text-lg font-semibold" x-text="'#' + selectedBilling.billing_id"></p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Client</p>
                        <p class="text-base" x-text="selectedBilling.client_name"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select x-model="selectedBilling.status" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700" @click="showEditModal = false">
                        Cancel
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700" @click="updateStatus()">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

            <!-- ARCHIVE CONFIRMATION MODAL -->
            <div x-show="showArchiveModal" 
                 x-cloak 
                 class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
                 style="display: none;">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6" @click.away="showArchiveModal = false">
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Billing Record</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Are you sure you want to archive billing record 
                                <span class="font-semibold text-gray-900">#<span x-text="selectedBilling.billing_id || 'N/A'"></span></span> 
                                for 
                                <span class="font-semibold" x-text="selectedBilling.client_name || 'N/A'"></span>?
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button @click="showArchiveModal = false" 
                                class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
                            Cancel
                        </button>
                        <button @click="archiveBilling()" 
                                class="px-4 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700">
                            Yes, Archive
                        </button>
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