<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Invoices</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <h1 class="text-2xl font-semibold text-gray-800">Invoices</h1>
            </div>
        </header>

        <!-- Invoice Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8" x-data="invoiceApp()">
            
            <!-- Action Bar -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">All Invoices</h2>
        <p class="text-sm text-gray-600 mt-1">Manage and track invoice records</p>
    </div>
    <div class="flex items-center space-x-3">
        <!-- Search Bar -->
        <form action="{{ route('invoices.index') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search invoices..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('invoices.index') }}" 
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
        <span>Create Invoice</span>
    </button>
    <a href="{{ route('invoices.archived') }}" class="flex items-center space-x-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
        </svg>
        <span>View Archived</span>
    </a>
</div>                    
            </div>

            <!-- Invoice Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-800">Invoice List</h3>
                </div>

                <!-- Empty State -->
                <div x-show="invoices.length === 0 && {{ $invoices->total() === 0 ? 'true' : 'false' }}" class="p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No invoices found.</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first invoice</p>
                    <button @click="openModal" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create First Invoice</span>
                    </button>
                </div>

                <!-- Table -->
                <div x-show="invoices.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
    <tr>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice Number</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Client Name</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Transport Order Ref</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice Date</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Net Total</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice Status</th>
        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
    </tr>
</thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    <template x-for="invoice in invoices" :key="invoice.invoice_id">
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-semibold text-gray-900" x-text="invoice.invoice_no"></div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-900 font-medium" x-text="invoice.client_name || 'N/A'"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-700" x-text="invoice.transport_order_ref || 'N/A'"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-600" x-text="invoice.invoice_date"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm text-gray-600" x-text="invoice.due_date || 'N/A'"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm font-semibold text-green-600" x-text="'₱' + parseFloat(invoice.net_total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span 
                    :class="{
                        'text-gray-600': invoice.invoice_status === 'Draft',
                        'text-blue-600': invoice.invoice_status === 'Sent',
                        'text-yellow-600': invoice.invoice_status === 'Partially Paid',
                        'text-green-600': invoice.invoice_status === 'Fully Paid',
                        'text-red-600': invoice.invoice_status === 'Overdue',
                        'text-red-800': invoice.invoice_status === 'Cancelled'
                    }"
                    class="text-sm font-semibold"
                    x-text="invoice.invoice_status">
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
    <div class="flex justify-center space-x-2">
        <!-- View Button -->
        <button @click.stop="openView(invoice)" 
                class="text-blue-600 hover:text-blue-900 transition-colors" 
                title="View">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
        
        <!-- Edit Button -->
        <button @click.stop="openEdit(invoice)" 
                class="text-green-600 hover:text-green-900 transition-colors" 
                title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>

        <!-- Archive Button -->
        <button @click.stop="archiveInvoice(invoice.invoice_id)" 
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
<div x-show="{{ $invoices->total() > 0 ? 'true' : 'false' }}" class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $invoices->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $invoices->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $invoices->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered)</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($invoices->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $invoices->appends(['search' => $search])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($invoices->getUrlRange(1, $invoices->lastPage()) as $page => $url)
            @if ($page == $invoices->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $invoices->appends(['search' => $search])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($invoices->hasMorePages())
            <a href="{{ $invoices->appends(['search' => $search])->nextPageUrl() }}" 
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

            <!-- Create Invoice Modal -->
            <div x-show="showModal" x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
                
                <!-- Modal Content -->
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full">
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Create New Invoice</span>
                                </h3>
                                <button @click="closeModal" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Form -->
                        <form @submit.prevent="createInvoice" class="p-6">
                            <div class="grid grid-cols-2 gap-5">
                                <!-- Invoice Number -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Invoice Number <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="newInvoice.invoice_no" 
                                        placeholder="e.g., INV-2024-001"
                                        required
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>

                                <!-- Transport Order Reference (with autocomplete) -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Transport Order <span class="text-red-500">*</span>
                                        <span class="text-xs text-gray-500 font-normal ml-2">(Approved TOs only)</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="toSearchQuery"
                                        @input="searchTransportOrders"
                                        @focus="showToSuggestions = true"
                                        placeholder="Start typing TO ref number..."
                                        required
                                        autocomplete="off"
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    
                                    <!-- Autocomplete Dropdown -->
                                    <div x-show="showToSuggestions && toSuggestions.length > 0" 
                                        @click.away="showToSuggestions = false"
                                        class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                        <template x-for="to in toSuggestions" :key="to.to_id">
                                            <div @click="selectTransportOrder(to)" 
                                                class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition border-b border-gray-100 last:border-0">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900" x-text="to.to_ref_no"></span>
                                                        <p class="text-xs text-gray-500" x-text="to.sipa_ref_no || 'N/A'"></p>
                                                    </div>
                                                    <span class="text-sm text-green-600 font-semibold" x-text="'₱' + parseFloat(to.total_amount).toLocaleString()"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Invoice Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Invoice Date <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        x-model="newInvoice.invoice_date" 
                                        required
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Due Date
                                    </label>
                                    <input 
                                        type="date" 
                                        x-model="newInvoice.due_date"
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>

                                <!-- Total Sales (Read-only) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Total Sales <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3 text-gray-600 font-semibold">₱</span>
                                        <input 
                                            type="text" 
                                            :value="newInvoice.total_sales ? parseFloat(newInvoice.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'"
                                            readonly
                                            class="w-full border-2 border-gray-200 bg-gray-50 rounded-xl pl-8 pr-4 py-3 text-gray-700 cursor-not-allowed">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Auto-filled from Transport Order</p>
                                </div>

                                <!-- VAT Deduction Display -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        VAT Deduction (2%)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3 text-gray-600 font-semibold">₱</span>
                                        <input 
                                            type="text" 
                                            :value="newInvoice.total_sales ? (parseFloat(newInvoice.total_sales) * 0.02).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'"
                                            readonly
                                            class="w-full border-2 border-red-200 bg-red-50 rounded-xl pl-8 pr-4 py-3 text-red-700 cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- Net Total (Read-only, col-span-2) -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Net Total (After 2% VAT Deduction) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3 text-gray-600 font-semibold">₱</span>
                                        <input 
                                            type="text" 
                                            :value="newInvoice.net_total ? parseFloat(newInvoice.net_total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'"
                                            readonly
                                            class="w-full border-2 border-green-200 bg-green-50 rounded-xl pl-8 pr-4 py-3 text-green-700 font-semibold text-lg cursor-not-allowed">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Total Sales - 2% VAT = Net Total</p>
                                </div>

                                <!-- Invoice Status -->
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Invoice Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        x-model="newInvoice.invoice_status" 
                                        required
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                        <option value="">Select status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Sent">Sent</option>
                                        <option value="Partially Paid">Partially Paid</option>
                                        <option value="Fully Paid">Fully Paid</option>
                                        <option value="Overdue">Overdue</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Modal Actions -->
                            <div class="mt-8 flex space-x-4">
                                <button 
                                    type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition shadow-lg hover:shadow-xl">
                                    Create Invoice
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
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex-shrink-0">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Invoice Details</span>
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
            <!-- Invoice Information -->
            <div class="bg-gray-50 rounded-xl p-5 mb-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Invoice Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Invoice Number</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice?.invoice_no"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Client Name</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice?.client_name || 'N/A'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Transport Order Reference</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice?.to_ref_no"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Invoice Status</p>
                        <span 
                            :class="{
                                'text-gray-700 bg-gray-100': viewData?.invoice?.invoice_status === 'Draft',
                                'text-blue-700 bg-blue-100': viewData?.invoice?.invoice_status === 'Sent',
                                'text-yellow-700 bg-yellow-100': viewData?.invoice?.invoice_status === 'Partially Paid',
                                'text-green-700 bg-green-100': viewData?.invoice?.invoice_status === 'Fully Paid',
                                'text-red-700 bg-red-100': viewData?.invoice?.invoice_status === 'Overdue',
                                'text-red-900 bg-red-200': viewData?.invoice?.invoice_status === 'Cancelled'
                            }"
                            class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
                            x-text="viewData?.invoice?.invoice_status">
                        </span>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="bg-gray-50 rounded-xl p-5 mb-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Dates</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Invoice Date</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice?.invoice_date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Due Date</p>
                        <p class="text-base font-semibold text-gray-900" x-text="viewData?.invoice?.due_date || 'N/A'"></p>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-gray-50 rounded-xl p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Financial Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Total Sales</span>
                        <span class="text-base font-semibold text-gray-900" x-text="'₱' + parseFloat(viewData?.invoice?.total_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Net Total (After 2% VAT)</span>
                        <span class="text-base font-semibold text-gray-900" x-text="'₱' + parseFloat(viewData?.invoice?.net_total || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                        <span class="text-sm text-gray-600">Total Paid</span>
                        <span class="text-base font-semibold text-blue-600" x-text="'₱' + parseFloat(viewData?.total_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-semibold text-gray-700">Remaining Balance</span>
                        <span class="text-xl font-bold" 
                              :class="viewData?.remaining_balance > 0 ? 'text-red-600' : 'text-green-600'"
                              x-text="'₱' + parseFloat(viewData?.remaining_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2})">
                        </span>
                    </div>
                </div>
            </div>
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

            <!-- EDIT STATUS MODAL -->
            <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden" @click.away="showEditModal = false">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Edit Invoice Status</span>
                            </h2>
                            <button @click="showEditModal = false" class="text-white hover:text-gray-200 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="updateStatus" class="p-6">
                        <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-500 mb-6">
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-1">Invoice Number</p>
                            <p class="text-xl font-bold text-gray-900" x-text="selectedInvoice?.invoice_no"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Invoice Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                x-model="selectedInvoice.invoice_status" 
                                required
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                <option value="Draft">Draft</option>
                                <option value="Sent">Sent</option>
                                <option value="Partially Paid">Partially Paid</option>
                                <option value="Fully Paid">Fully Paid</option>
                                <option value="Overdue">Overdue</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="mt-6 flex space-x-3">
                            <button 
                                type="submit"
                                class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition shadow-lg hover:shadow-xl">
                                Update Status
                            </button>
                            <button 
                                type="button"
                                @click="showEditModal = false"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Invoice</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to archive invoice <span class="font-semibold text-gray-900" x-text="selectedInvoiceNo"></span>? This will hide it from the active invoices list.
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

<!-- Toast Notification -->
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


        </main>
    </div>
</div>

<script>
function invoiceApp() {
    return {
        invoices: @json($invoices->items()),
        showModal: false,
        showViewModal: false,
        showEditModal: false,
        showArchiveModal: false,
        selectedInvoiceId: null,
        selectedInvoiceNo: '',
        viewData: null,
        selectedInvoice: null,
        toSearchQuery: '',
        showToSuggestions: false,
        toSuggestions: [],
        searchTimeout: null,
        newInvoice: {
            invoice_no: '',
            to_id: '',
            to_ref_no: '',
            invoice_date: '',
            due_date: '',
            total_sales: 0,
            net_total: 0,
            invoice_status: ''
        },

        init() {
            this.setTodayDate();
        },

        setTodayDate() {
            const today = new Date().toISOString().split('T')[0];
            this.newInvoice.invoice_date = today;
        },

        searchTransportOrders() {
            if (this.toSearchQuery.length < 1) {
                this.toSuggestions = [];
                return;
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                fetch(`/invoices/transport-orders?query=${encodeURIComponent(this.toSearchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.toSuggestions = data;
                        this.showToSuggestions = true;
                    })
                    .catch(error => {
                        console.error('Error searching transport orders:', error);
                        this.toSuggestions = [];
                    });
            }, 300);
        },

        selectTransportOrder(to) {
            this.toSearchQuery = to.to_ref_no;
            this.newInvoice.to_id = to.to_id;
            this.newInvoice.to_ref_no = to.to_ref_no;
            this.newInvoice.total_sales = to.total_amount;
            
            const vatDeduction = parseFloat(to.total_amount) * 0.02;
            this.newInvoice.net_total = parseFloat(to.total_amount) - vatDeduction;
            
            this.showToSuggestions = false;
        },

        openModal() {
            this.showModal = true;
            this.resetForm();
            this.setTodayDate();
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.newInvoice = {
                invoice_no: '',
                to_id: '',
                to_ref_no: '',
                invoice_date: '',
                due_date: '',
                total_sales: 0,
                net_total: 0,
                invoice_status: ''
            };
            this.toSearchQuery = '';
            this.toSuggestions = [];
            this.showToSuggestions = false;
        },

        createInvoice() {
            fetch('/invoices/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.newInvoice)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Invoice created successfully!', type: 'success' } 
                    }));
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: data.message || 'Failed to create invoice', type: 'error' } 
                    }));
                }
            })
            .catch(error => {
                console.error('Error creating invoice:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error creating invoice', type: 'error' } 
                }));
            });
        },

        openView(invoice) {
            fetch('/invoices/view/' + invoice.invoice_id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        this.viewData = data.data;
                        this.showViewModal = true;
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { message: data.message || 'Failed to load invoice details', type: 'error' } 
                        }));
                    }
                })
                .catch(error => {
                    console.error('Error loading invoice details:', error);
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Error loading invoice details', type: 'error' } 
                    }));
                });
        },

        openEdit(invoice) {
            this.selectedInvoice = { ...invoice };
            this.showEditModal = true;
        },

        updateStatus() {
            fetch('/invoices/update-status/' + this.selectedInvoice.invoice_id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    invoice_status: this.selectedInvoice.invoice_status
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Status updated successfully!', type: 'success' } 
                    }));
                    const index = this.invoices.findIndex(i => i.invoice_id === this.selectedInvoice.invoice_id);
                    if(index !== -1) {
                        this.invoices[index].invoice_status = this.selectedInvoice.invoice_status;
                    }
                    this.showEditModal = false;
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: data.message || 'Failed to update status', type: 'error' } 
                    }));
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error updating status', type: 'error' } 
                }));
            });
        },

        archiveInvoice(invoiceId) {
            const invoice = this.invoices.find(i => i.invoice_id === invoiceId);
            this.selectedInvoiceId = invoiceId;
            this.selectedInvoiceNo = invoice ? invoice.invoice_no : '';
            this.showArchiveModal = true;
        },

        async confirmArchive() {
            try {
                const response = await fetch(`/invoices/${this.selectedInvoiceId}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Invoice archived successfully!', type: 'success' } 
                    }));
                    this.showArchiveModal = false;
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: result.message, type: 'error' } 
                    }));
                }
            } catch (error) {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: 'Error archiving invoice', type: 'error' } 
                }));
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
                