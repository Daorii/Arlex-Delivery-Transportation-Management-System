<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50" x-data="paymentApp()" x-init="
    const urlParams = new URLSearchParams(window.location.search);
    const successMsg = urlParams.get('success');
    const errorMsg = urlParams.get('error');
    
    if (successMsg) {
        setTimeout(() => {
            $dispatch('notify', { message: successMsg, type: 'success' });
        }, 100);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (errorMsg) {
        setTimeout(() => {
            $dispatch('notify', { message: errorMsg, type: 'error' });
        }, 100);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
">

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

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <h1 class="text-2xl font-semibold text-gray-800">Payments</h1>
            </div>
        </header>

        <!-- Payment Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
            
            <!-- Action Bar -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">All Payments</h2>
        <p class="text-sm text-gray-600 mt-1">Manage and track payment records</p>
    </div>
    <div class="flex items-center space-x-3">
        <!-- Search Bar -->
        <form action="{{ route('payments.index') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search payments..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('payments.index') }}" 
                   class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                   title="Clear search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            @endif
        </form>
    <button @click="openModal" class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Record Payment</span>
    </button>
    <a href="{{ route('payments.archived') }}" class="flex items-center space-x-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
        </svg>
        <span>View Archived</span>
    </a>
</div>
            </div>

            <!-- Payment Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-800">Payment List</h3>
                </div>

                <!-- Empty State -->
                <div x-show="payments.length === 0 && {{ ($paymentsQuery->total() ?? 0) === 0 ? 'true' : 'false' }}" class="p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No payments found.</h3>
                    <p class="text-gray-500 mb-4">Get started by recording your first payment</p>
                    <button @click="openModal" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Record First Payment</span>
                    </button>
                </div>

                <!-- Table -->
                <div x-show="payments.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
    <tr>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Ref</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice Number</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Date</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Method</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
    </tr>
</thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    <template x-for="payment in payments" :key="payment.payment_id">
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-semibold text-gray-900" x-text="payment.payment_ref_no"></div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-900 font-medium" x-text="payment.invoice_no"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-600" x-text="payment.payment_date"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm font-semibold text-green-600" x-text="'₱' + parseFloat(payment.payment_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-900" x-text="payment.payment_method"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex justify-center space-x-2">
                    <!-- View Button -->
                    <button @click.stop="openView(payment)" 
                            class="text-blue-600 hover:text-blue-900 transition-colors" 
                            title="View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>

                    <!-- Archive Button -->
<button @click.stop="archivePayment(payment.payment_id, payment.payment_ref_no)" 
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
<div x-show="{{ ($paymentsQuery->total() ?? 0) > 0 ? 'true' : 'false' }}" class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $paymentsQuery->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $paymentsQuery->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $paymentsQuery->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered)</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($paymentsQuery->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $paymentsQuery->appends(['search' => $search])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($paymentsQuery->getUrlRange(1, $paymentsQuery->lastPage()) as $page => $url)
            @if ($page == $paymentsQuery->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $paymentsQuery->appends(['search' => $search])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($paymentsQuery->hasMorePages())
            <a href="{{ $paymentsQuery->appends(['search' => $search])->nextPageUrl() }}" 
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

            <!-- Create/Edit Payment Modal -->
            <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
                
                <!-- Modal Content -->
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full">
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span x-text="isEditing ? 'Edit Payment' : 'Record New Payment'"></span>
                                </h3>
                                <button @click="closeModal" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Form -->
<form @submit.prevent="savePayment" class="p-6">
    <div class="grid grid-cols-2 gap-5">
        <!-- Payment Reference Number -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Payment Reference <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                x-model="formData.payment_ref_no" 
                placeholder="e.g., PAY-2024-001"
                required
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Invoice Selection (with autocomplete) -->
        <div class="relative">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Invoice <span class="text-red-500">*</span>
               <span class="text-xs text-gray-500 font-normal ml-2">(Only unpaid/partially paid)</span>
            </label>
            <input 
                type="text" 
                x-model="invoiceSearchQuery"
                @input="searchInvoices"
                @focus="showInvoiceSuggestions = true"
                placeholder="Start typing invoice number..."
                required
                autocomplete="off"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            
            <!-- Autocomplete Dropdown -->
            <div x-show="showInvoiceSuggestions && invoiceSuggestions.length > 0" 
                @click.away="showInvoiceSuggestions = false"
                class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                <template x-for="invoice in invoiceSuggestions" :key="invoice.invoice_id">
                    <div @click="selectInvoice(invoice)" 
                        class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition border-b border-gray-100 last:border-0">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm font-medium text-gray-900" x-text="invoice.invoice_no"></span>
                                <p class="text-xs text-gray-500" x-text="'TO: ' + invoice.to_ref_no"></p>
                                <span class="text-xs px-2 py-1 rounded-full mt-1 inline-block"
                                      :class="{
                                          'bg-blue-100 text-blue-800': invoice.invoice_status === 'Sent',
                                          'bg-yellow-100 text-yellow-800': invoice.invoice_status === 'Partially Paid',
                                          'bg-green-100 text-green-800': invoice.invoice_status === 'Fully Paid'
                                      }"
                                      x-text="invoice.invoice_status">
                                </span>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600" x-text="'Total: ₱' + parseFloat(invoice.net_total).toLocaleString()"></p>
                                <p class="text-xs text-blue-600" x-text="'Paid: ₱' + parseFloat(invoice.paid_amount).toLocaleString()"></p>
                                <p class="text-xs font-semibold" 
                                   :class="invoice.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'"
                                   x-text="'Balance: ₱' + parseFloat(invoice.remaining_balance).toLocaleString()">
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Payment Date -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Payment Date <span class="text-red-500">*</span>
            </label>
            <input 
                type="date" 
                x-model="formData.payment_date" 
                required
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Payment Amount -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Payment Amount <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-4 top-3 text-gray-600 font-semibold">₱</span>
                <input 
                    type="number" 
                    step="0.01"
                    x-model="formData.payment_amount" 
                    placeholder="0.00"
                    required
                    :max="selectedInvoiceBalance"
                    class="w-full border-2 border-gray-200 rounded-xl pl-8 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <p x-show="selectedInvoiceBalance > 0" class="text-xs text-gray-500 mt-1">
                Remaining balance: <span class="font-semibold text-red-600" x-text="'₱' + parseFloat(selectedInvoiceBalance).toLocaleString()"></span>
            </p>
        </div>

        <!-- Payment Method -->
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Payment Method <span class="text-red-500">*</span>
            </label>
            <select 
                x-model="formData.payment_method" 
                @change="updatePaymentMethodFields"
                required
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <option value="">Select method</option>
                <option value="Cash">Cash</option>
                <option value="Check">Check</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Online Payment">Online Payment</option>
            </select>
        </div>

        <!-- Bank Name (conditional) -->
        <div x-show="formData.payment_method === 'Bank Transfer' || formData.payment_method === 'Check'">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Bank Name
            </label>
            <input 
                type="text" 
                x-model="formData.bank_name" 
                placeholder="e.g., BPI, BDO"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Check Number (conditional) -->
        <div x-show="formData.payment_method === 'Check'">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Check Number
            </label>
            <input 
                type="text" 
                x-model="formData.check_number" 
                placeholder="e.g., 123456"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Transaction Reference (conditional) -->
        <div x-show="formData.payment_method === 'Bank Transfer' || formData.payment_method === 'Online Payment'" :class="{'col-span-2': formData.payment_method === 'Online Payment'}">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Transaction Reference
            </label>
            <input 
                type="text" 
                x-model="formData.transaction_ref_no" 
                placeholder="e.g., TXN123456"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Received By -->
        <div :class="{'col-span-2': formData.payment_method === 'Cash' || formData.payment_method === 'Online Payment'}">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Received By
            </label>
            <input 
                type="text" 
                x-model="formData.received_by" 
                placeholder="e.g., John Doe"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>

        <!-- Remarks -->
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Remarks
            </label>
            <textarea 
                x-model="formData.remarks" 
                rows="3"
                placeholder="Additional notes or comments..."
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
        </div>
    </div>

    <!-- Modal Actions -->
    <div class="mt-8 flex space-x-4">
        <button 
            type="submit"
            class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition shadow-lg hover:shadow-xl">
            Record Payment
        </button>
        <button 
            type="button"
            @click="closeModal"
            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
            Cancel
        </button>
    </div>
</form>
                    </div>
                </div>
            </div>

          <!-- VIEW MODAL -->
<div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.away="showViewModal = false">
        <!-- Modal Header (Fixed) -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex-shrink-0">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Payment Details</span>
                </h2>
                <button @click="showViewModal = false" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div x-show="viewData" class="overflow-y-auto flex-1 p-6">
            <!-- Payment Information -->
            <div class="bg-gray-50 rounded-xl p-5 mb-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Payment Reference</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.payment_ref_no"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Invoice Number</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice_no"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Payment Date</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.payment_date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Payment Status</p>
                        <span 
                            :class="{
                                'text-yellow-700 bg-yellow-100': viewData?.payment_status === 'Pending',
                                'text-green-700 bg-green-100': viewData?.payment_status === 'Completed',
                                'text-red-700 bg-red-100': viewData?.payment_status === 'Failed',
                                'text-gray-700 bg-gray-100': viewData?.payment_status === 'Cancelled'
                            }"
                            class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
                            x-text="viewData?.payment_status">
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-xl p-5 mb-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Payment Amount</span>
                        <span class="text-lg font-bold text-green-600" x-text="'₱' + parseFloat(viewData?.payment_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Payment Method</span>
                        <span class="text-base font-semibold text-gray-900" x-text="viewData?.payment_method"></span>
                    </div>
                    <template x-if="viewData?.bank_name">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Bank Name</span>
                            <span class="text-base font-semibold text-gray-900" x-text="viewData?.bank_name"></span>
                        </div>
                    </template>
                    <template x-if="viewData?.check_number">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Check Number</span>
                            <span class="text-base font-semibold text-gray-900" x-text="viewData?.check_number"></span>
                        </div>
                    </template>
                    <template x-if="viewData?.transaction_ref_no">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Transaction Reference</span>
                            <span class="text-base font-semibold text-gray-900" x-text="viewData?.transaction_ref_no"></span>
                        </div>
                    </template>
                    <template x-if="viewData?.received_by">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Received By</span>
                            <span class="text-base font-semibold text-gray-900" x-text="viewData?.received_by"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Remarks Section -->
            <template x-if="viewData?.remarks">
                <div class="bg-gray-50 rounded-xl p-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Remarks</h3>
                    <p class="text-sm text-gray-800" x-text="viewData?.remarks"></p>
                </div>
            </template>
        </div>

        <!-- Modal Footer (Fixed) -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end flex-shrink-0">
            <button @click="showViewModal = false" 
                    class="px-6 py-2.5 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition shadow-lg hover:shadow-xl">
                Close
            </button>
        </div>
    </div>
</div>


<!-- Archive Confirmation Modal -->
<div x-show="showArchiveModal" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6" @click.away="showArchiveModal = false">
        <div class="flex items-start mb-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Payment</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to archive payment <span class="font-semibold text-gray-900" x-text="selectedPaymentRef"></span>?
                </p>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <button @click="showArchiveModal = false" 
                    class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
                Cancel
            </button>
            <button @click="confirmArchive()" 
                    class="px-4 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700">
                Yes, Archive
            </button>
        </div>
    </div>
</div>




<script>
function paymentApp() {
    return {
        payments: @json($paymentsQuery->items() ?? []),
        showModal: false,
        showViewModal: false,
        showArchiveModal: false,  // ← ADD
        selectedPaymentId: null,   // ← ADD
        selectedPaymentRef: '',    // ← ADD

        
        formData: {
            payment_id: null,
            invoice_id: null,
            payment_ref_no: '',
            payment_date: new Date().toISOString().split('T')[0],
            payment_amount: '',
            payment_method: '',
            bank_name: '',
            check_number: '',
            transaction_ref_no: '',
            remarks: '',
            received_by: ''
        },
        
        invoiceSearchQuery: '',
        invoiceSuggestions: [],
        showInvoiceSuggestions: false,
        selectedInvoiceBalance: 0,

        openModal() {
            this.showModal = true;
            this.resetForm();
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.formData = {
                payment_id: null,
                invoice_id: null,
                payment_ref_no: '',
                payment_date: new Date().toISOString().split('T')[0],
                payment_amount: '',
                payment_method: '',
                bank_name: '',
                check_number: '',
                transaction_ref_no: '',
                remarks: '',
                received_by: ''
            };
            this.invoiceSearchQuery = '';
            this.selectedInvoiceBalance = 0;
        },

        async searchInvoices() {
            if (this.invoiceSearchQuery.length < 1) {
                this.invoiceSuggestions = [];
                return;
            }

            try {
                const response = await fetch(`/payments/search-invoices?query=${encodeURIComponent(this.invoiceSearchQuery)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                this.invoiceSuggestions = data;
                this.showInvoiceSuggestions = true;
            } catch (error) {
                console.error('Error searching invoices:', error);
            }
        },

        selectInvoice(invoice) {
            this.invoiceSearchQuery = `${invoice.invoice_no} - TO: ${invoice.to_ref_no}`;
            this.formData.invoice_id = invoice.invoice_id;
            this.selectedInvoiceBalance = parseFloat(invoice.remaining_balance);
            this.formData.payment_amount = this.selectedInvoiceBalance;
            this.showInvoiceSuggestions = false;
        },

        updatePaymentMethodFields() {
            // Reset conditional fields
            if (this.formData.payment_method === 'Cash') {
                this.formData.bank_name = '';
                this.formData.check_number = '';
                this.formData.transaction_ref_no = '';
            } else if (this.formData.payment_method === 'Check') {
                this.formData.transaction_ref_no = '';
            } else if (this.formData.payment_method === 'Bank Transfer') {
                this.formData.check_number = '';
            } else if (this.formData.payment_method === 'Online Payment') {
                this.formData.bank_name = '';
                this.formData.check_number = '';
            }
        },

        async savePayment() {
            try {
                const response = await fetch('/payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = '/payments?success=' + encodeURIComponent(result.message);
                } else {
                    this.$dispatch('notify', { message: result.message || 'Error saving payment', type: 'error' });
                }
            } catch (error) {
                console.error('Error saving payment:', error);
                this.$dispatch('notify', { message: 'Error saving payment', type: 'error' });
            }
        },

        openView(payment) {
            this.viewData = payment;
            this.showViewModal = true;
        },
archivePayment(paymentId, paymentRef) {
            this.selectedPaymentId = paymentId;
            this.selectedPaymentRef = paymentRef;
            this.showArchiveModal = true;
        },

        async confirmArchive() {
            try {
                const response = await fetch(`/payments/${this.selectedPaymentId}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.showArchiveModal = false;
                    this.$dispatch('notify', { message: 'Payment archived successfully!', type: 'success' });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.$dispatch('notify', { message: result.message, type: 'error' });
                }
            } catch (error) {
                console.error('Error:', error);
                this.$dispatch('notify', { message: 'Error archiving payment', type: 'error' });
            }
        },
        
        // UPDATE deletePayment to only show in archived page
        async deletePayment(payment) {
            if (!confirm(`Are you sure you want to PERMANENTLY delete payment ${payment.payment_ref_no}? This cannot be undone!`)) {
                return;
            }

            try {
                const response = await fetch(`/payments/${payment.payment_id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.$dispatch('notify', { message: result.message, type: 'success' });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    this.$dispatch('notify', { message: result.message, type: 'error' });
                }
            } catch (error) {
                console.error('Error deleting payment:', error);
                this.$dispatch('notify', { message: 'Error deleting payment', type: 'error' });
            }
        }
    }
    
}
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