<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Transport Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        @media print {
            body * { visibility: hidden; }
            #to-print-content, #to-print-content * { visibility: visible; }
            #to-print-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print { display: none !important; }
            @page { margin: 0.5cm; }
        }
        [x-cloak] { display: none !important; }

        /* Ensure dropdown appears above other elements */
.soa-dropdown {
    position: absolute;
    z-index: 9999 !important;
    top: 100%;
    left: 0;
    right: 0;
}

/* Make sure modal allows overflow for dropdown */
.modal-with-dropdown {
    overflow: visible !important;
}
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
                <h1 class="text-2xl font-semibold text-gray-800">Transport Orders</h1>
            </div>
        </header>

        <!-- Transport Orders Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8" x-data="transportOrdersApp()">
            
            <!-- Action Bar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">All Transport Orders</h2>
        <p class="text-sm text-gray-600 mt-1">Manage and track your transport orders</p>
    </div>
    <div class="flex items-center space-x-3">
        <!-- Search Bar -->
        <form action="{{ route('transport-orders.index') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search orders..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('transport-orders.index') }}" 
                   class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                   title="Clear search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            @endif
        </form>
<button @click="openModal" class="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create TO</span>
        </button>
<a href="{{ route('transport-orders.archived') }}" class="flex items-center space-x-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
            <span>View Archived</span>
        </a>
    </div>
</div>

            <!-- Transport Orders Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-800">Transport Orders List</h3>
                </div>

                <!-- Empty State -->
                <div x-show="groupedOrders.length === 0 && {{ $transportOrders->total() === 0 ? 'true' : 'false' }}" class="p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Transport Orders Yet</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first transport order</p>
                    <button @click="openModal" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create First Order</span>
                    </button>
                </div>

                <!-- Table -->
                <div x-show="groupedOrders.length > 0" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">TO Reference Number</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">SIPA Ref No</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Items (Type/Qty)</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Depot From</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Depot To</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Verification Status</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="order in groupedOrders" :key="order.to_ref_no">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center">
                                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900" x-text="order.to_ref_no"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-900" x-text="order.sipa_ref_no"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-center space-y-1">
                                            <template x-for="(item, idx) in order.items" :key="idx">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm font-medium text-gray-900" x-text="item.size + '\"'"></span>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                                          :class="item.type === 'Dry' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                          x-text="item.type">
                                                    </span>
                                                    <span class="text-sm text-gray-600" x-text="'× ' + item.quantity"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-semibold text-green-600" x-text="'₱' + order.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-900" x-text="order.depot_from"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-900" x-text="order.depot_to"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span 
                                            :class="{
                                                'text-yellow-600': order.verification_status === 'Pending',
                                                'text-green-600': order.verification_status === 'Approved',
                                                'text-red-600': order.verification_status === 'Declined'
                                            }"
                                            class="text-sm font-semibold"
                                            x-text="order.verification_status">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
    <div class="flex items-center justify-center space-x-2">
        <!-- View Button -->
        <button @click="openView(order)" 
                class="text-blue-600 hover:text-blue-900 transition-colors" 
                title="View">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
        
        <!-- Edit Button -->
        <button @click="openEdit(order)" 
                class="text-green-600 hover:text-green-900 transition-colors" 
                title="Edit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </button>

        <!-- Archive Button -->
        <button @click="archiveOrder(order.to_ref_no)" 
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
<div x-show="{{ $transportOrders->total() > 0 ? 'true' : 'false' }}" class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing <span class="font-medium">{{ $transportOrders->firstItem() ?? 0 }}</span> 
        to <span class="font-medium">{{ $transportOrders->lastItem() ?? 0 }}</span> 
        of <span class="font-medium">{{ $transportOrders->total() }}</span> results
        @if($search ?? false)
            <span class="text-blue-600">(filtered)</span>
        @endif
    </div>
    
    <div class="flex space-x-2">
        {{-- Previous Button --}}
        @if ($transportOrders->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                Previous
            </span>
        @else
            <a href="{{ $transportOrders->appends(['search' => $search])->previousPageUrl() }}" 
               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                Previous
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($transportOrders->getUrlRange(1, $transportOrders->lastPage()) as $page => $url)
            @if ($page == $transportOrders->currentPage())
                <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
            @else
                <a href="{{ $transportOrders->appends(['search' => $search])->url($page) }}" 
                   class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($transportOrders->hasMorePages())
            <a href="{{ $transportOrders->appends(['search' => $search])->nextPageUrl() }}" 
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

            <!-- VIEW MODAL -->
            <div x-show="showViewModal" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto" @click.away="showViewModal = false">
                    
                    <!-- TO Content -->
                    <div x-show="viewData" id="to-print-content" class="p-8">
                        <!-- Header -->
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Arlex Delivery Services</h2>
                            <p class="text-sm text-gray-600">Transport Order Details</p>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <p class="text-sm"><strong>TO Reference #:</strong> <span x-text="viewData?.to_ref_no"></span></p>
                                <p class="text-sm"><strong>SIPA Ref #:</strong> <span x-text="viewData?.sipa_ref_no"></span></p>
                                <p class="text-sm"><strong>Billing ID:</strong> <span x-text="'#' + viewData?.billing_id"></span></p>
                            </div>
                            <div>
                                <p class="text-sm"><strong>Status:</strong> 
                                    <span :class="{
                                        'text-yellow-600': viewData?.verification_status === 'Pending',
                                        'text-green-600': viewData?.verification_status === 'Approved',
                                        'text-red-600': viewData?.verification_status === 'Declined'
                                    }" class="font-semibold" x-text="viewData?.verification_status"></span>
                                </p>
                                <p class="text-sm"><strong>Route:</strong> <span x-text="viewData?.depot_from + ' → ' + viewData?.depot_to"></span></p>
                            </div>
                        </div>

                        <h3 class="text-center text-lg font-bold mb-4">Transport Order Items</h3>

                        <!-- Items Table -->
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full border-collapse border border-gray-400 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-400 px-3 py-2 text-left font-semibold">Size</th>
                                        <th class="border border-gray-400 px-3 py-2 text-left font-semibold">Type</th>
                                        <th class="border border-gray-400 px-3 py-2 text-center font-semibold">Quantity</th>
                                        <th class="border border-gray-400 px-3 py-2 text-left font-semibold">Depot From</th>
                                        <th class="border border-gray-400 px-3 py-2 text-left font-semibold">Depot To</th>
                                        <th class="border border-gray-400 px-3 py-2 text-right font-semibold">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in viewData?.items" :key="index">
                                        <tr>
                                            <td class="border border-gray-400 px-3 py-2" x-text="item.size ? item.size + '&quot;' : 'N/A'"></td>
                                            <td class="border border-gray-400 px-3 py-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                                      :class="item.type === 'Dry' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                      x-text="item.type || 'N/A'">
                                                </span>
                                            </td>
                                            <td class="border border-gray-400 px-3 py-2 text-center" x-text="item.quantity || 0"></td>
                                            <td class="border border-gray-400 px-3 py-2" x-text="item.depot_from || 'N/A'"></td>
                                            <td class="border border-gray-400 px-3 py-2" x-text="item.depot_to || 'N/A'"></td>
                                            <td class="border border-gray-400 px-3 py-2 text-right" x-text="'₱' + (item.amount ? item.amount.toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00')"></td>
                                        </tr>
                                    </template>
                                    <tr class="bg-gray-100 font-bold">
                                        <td colspan="5" class="border border-gray-400 px-3 py-2 text-right">TOTAL</td>
                                        <td class="border border-gray-400 px-3 py-2 text-right">₱ <span x-text="viewData?.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2})"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 mt-8 pt-6 border-t no-print">
                            <button @click="window.print()" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                                <span class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    <span>Print TO</span>
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
            <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6" @click.away="showEditModal = false">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Edit Transport Order Status</h2>
                        <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500">TO Reference Number</p>
                            <p class="text-lg font-semibold" x-text="selectedOrder?.to_ref_no"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Verification Status <span class="text-red-500">*</span>
                            </label>
                            <select 
                                x-model="selectedOrder.verification_status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Declined">Declined</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="showEditModal = false" class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
                            Cancel
                        </button>
                        <button @click="updateStatus()" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create Order Modal -->
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
                
                <!-- Modal Content -->
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="showModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     class="relative inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white flex items-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Create New Transport Order</span>
                                </h3>
                                <button @click="closeModal" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Form -->
<form @submit.prevent="createOrder" class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
    <div class="space-y-5">
                                <!-- TO Ref No -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        TO Reference Number <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        x-model="newOrder.to_ref_no" 
                                        placeholder="e.g., TO-2024-001"
                                        required
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>

                                <!-- SOA Billing ID with Autocomplete -->
<div class="relative">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        SOA (Statement of Account) <span class="text-red-500">*</span>
        <span class="text-xs text-gray-500 font-normal ml-2">(Only Approved SOAs)</span>
    </label>
    <input 
        type="text" 
        x-model="soaSearchQuery"
        @input="searchSoa"
        @focus="showSuggestions = true"
        placeholder="Start typing SOA ID, SIPA ref, or client name..."
        required
        autocomplete="off"
        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
    
    <!-- Autocomplete Dropdown - FIXED VERSION -->
<div x-show="showSuggestions && soaSuggestions.length > 0" 
    @click.away="showSuggestions = false"
    class="soa-dropdown bg-white border-2 border-gray-200 rounded-xl shadow-2xl max-h-60 overflow-y-auto">
    <template x-for="soa in soaSuggestions" :key="soa.billing_id">
        <div @click="selectSoa(soa)" 
            class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition border-b border-gray-100 last:border-0">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-bold text-blue-600" x-text="'SOA #' + soa.billing_id"></span>
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded" x-text="soa.sipa_ref_no"></span>
                    </div>
                    <p class="text-sm text-gray-900 font-medium" x-text="soa.company_name"></p>
                    <p class="text-xs text-gray-500" x-text="soa.week_period_text"></p>
                </div>
            </div>
        </div>
    </template>
</div>
    
    <!-- No results message -->
    <div x-show="showSuggestions && soaSuggestions.length === 0 && soaSearchQuery.length > 0" 
        class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-xl p-4 text-center">
        <p class="text-sm text-gray-500">No approved SOAs found</p>
    </div>
</div>

                                <!-- SOA Details Summary -->
                                <div x-show="soaDetails" class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Selected SOA Details</h4>
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Client:</span>
                                            <span class="font-medium ml-2" x-text="soaDetails?.client_name"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">SIPA Ref:</span>
                                            <span class="font-medium ml-2" x-text="soaDetails?.sipa_ref_no"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items Table -->
                                <div x-show="soaItems.length > 0">
    <label class="block text-sm font-semibold text-gray-700 mb-3">
                                        Select Items to Include <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <div class="border-2 border-gray-200 rounded-xl overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                                        <input type="checkbox" @change="toggleAllItems($event)" class="rounded">
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Size</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Route</th>
                                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <template x-for="(item, index) in soaItems" :key="index">
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3">
                                                            <input type="checkbox" x-model="item.selected" @change="calculateTotal" class="rounded">
                                                        </td>
<td class="px-4 py-3 text-sm" x-text="item.size + ' ft'"></td>                                                        <td class="px-4 py-3">
                                                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                                                  :class="item.type === 'Dry' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                                  x-text="item.type">
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-sm" x-text="item.quantity"></td>
                                                        <td class="px-4 py-3 text-sm" x-text="item.depot_from + ' → ' + item.depot_to"></td>
                                                        <td class="px-4 py-3 text-right text-sm font-semibold text-green-600" 
                                                            x-text="'₱' + (item.quantity * item.price_per_unit).toLocaleString('en-US', {minimumFractionDigits: 2})">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Total Amount Display -->
                                <div x-show="soaItems.length > 0" class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-700">Total Amount:</span>
                                        <span class="text-2xl font-bold text-green-700" x-text="'₱' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-6 flex items-center justify-end space-x-3">
                                <button 
                                    type="button" 
                                    @click="closeModal"
                                    class="px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 transition">
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    :disabled="!canSubmit"
                                    :class="canSubmit ? 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800' : 'bg-gray-300 cursor-not-allowed'"
                                    class="px-6 py-3 rounded-xl font-semibold text-white transition shadow-lg">
                                    Create Transport Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Success/Error Message Toast -->
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Archive Transport Order</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Are you sure you want to archive Transport Order <span class="font-semibold text-gray-900" x-text="selectedToRefNo"></span>? This will hide it from the active orders list.
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


        </main>
    </div>
</div>

<script>
function transportOrdersApp() {
    return {
        orders: [],
        groupedOrders: [],
        showModal: false,
        showViewModal: false,
        showEditModal: false,
        showArchiveModal: false,
        selectedToRefNo: '',
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        viewData: null,
        selectedOrder: {},
        
        newOrder: {
            to_ref_no: '',
            billing_id: null,
            sipa_ref_no: ''
        },
        
        archiveOrder(toRefNo) {
    this.selectedToRefNo = toRefNo;
    this.showArchiveModal = true;
},

async confirmArchive() {
    try {
        const response = await fetch(`/transport-orders/${this.selectedToRefNo}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            this.showToastMessage('Transport Order archived successfully!', 'success');
            this.showArchiveModal = false;
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            this.showToastMessage(result.message, 'error');
        }
    } catch (error) {
        console.error('Error archiving TO:', error);
        this.showToastMessage('Error archiving transport order', 'error');
    }
},


        soaSearchQuery: '',
        soaSuggestions: [],
        showSuggestions: false,
        soaDetails: null,
        soaItems: [],
        totalAmount: 0,

        init() {
    this.orders = @json($transportOrders->items());
    console.log('Initial orders from database:', this.orders);
    this.groupOrders();
},

        groupOrders() {
            const grouped = {};
            
            this.orders.forEach(order => {
                console.log('Processing order:', order);
                
                if (!grouped[order.to_ref_no]) {
                    grouped[order.to_ref_no] = {
                        to_ref_no: order.to_ref_no,
                        sipa_ref_no: order.sipa_ref_no,
                        billing_id: order.billing_id,
                        verification_status: order.verification_status,
                        depot_from: order.depot_from,
                        depot_to: order.depot_to,
                        items: [],
                        total_amount: 0
                    };
                }
                
                grouped[order.to_ref_no].items.push({
                    size: order.size,
                    type: order.type,
                    quantity: order.quantity,
                    amount: parseFloat(order.total_amount),
                    depot_from: order.depot_from,
                    depot_to: order.depot_to
                });
                
                grouped[order.to_ref_no].total_amount += parseFloat(order.total_amount);
            });
            
            this.groupedOrders = Object.values(grouped);
            console.log('Grouped orders:', this.groupedOrders);
        },

        get canSubmit() {
            return this.newOrder.to_ref_no && 
                   this.newOrder.billing_id && 
                   this.soaItems.some(item => item.selected);
        },

        openModal() {
            this.showModal = true;
            this.resetForm();
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        openView(order) {
            console.log('Opening view with order:', order);
            console.log('Order items:', order.items);
            this.viewData = order;
            this.showViewModal = true;
        },

        openEdit(order) {
            this.selectedOrder = { ...order };
            this.showEditModal = true;
        },

        async updateStatus() {
            try {
                const response = await fetch('/transport-orders/update-status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to_ref_no: this.selectedOrder.to_ref_no,
                        verification_status: this.selectedOrder.verification_status
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showToastMessage('Status updated successfully!', 'success');
                    
                    // Update the order in the list
                    const index = this.groupedOrders.findIndex(o => o.to_ref_no === this.selectedOrder.to_ref_no);
                    if (index !== -1) {
                        this.groupedOrders[index].verification_status = this.selectedOrder.verification_status;
                    }
                    
                    this.showEditModal = false;
                } else {
                    this.showToastMessage(result.message || 'Error updating status', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                this.showToastMessage('Error updating status', 'error');
            }
        },

        resetForm() {
            this.newOrder = {
                to_ref_no: '',
                billing_id: null,
                sipa_ref_no: ''
            };
            this.soaSearchQuery = '';
            this.soaSuggestions = [];
            this.showSuggestions = false;
            this.soaDetails = null;
            this.soaItems = [];
            this.totalAmount = 0;
        },

        async searchSoa() {
            if (this.soaSearchQuery.length < 1) {
                this.soaSuggestions = [];
                return;
            }

            try {
                const response = await fetch(`/transport-orders/search-soa?query=${encodeURIComponent(this.soaSearchQuery)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                this.soaSuggestions = data;
                this.showSuggestions = true;
            } catch (error) {
                console.error('Error searching SOA:', error);
            }
        },

        async selectSoa(soa) {
            this.soaSearchQuery = `SOA #${soa.billing_id} - ${soa.company_name}`;
            this.newOrder.billing_id = soa.billing_id;
            this.newOrder.sipa_ref_no = soa.sipa_ref_no;
            this.showSuggestions = false;

            try {
                const response = await fetch(`/transport-orders/soa-details/${soa.billing_id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.soaDetails = result.data;
                    this.soaItems = result.data.details.map(item => ({
                        ...item,
                        selected: false
                    }));
                } else {
                    this.showToastMessage(result.message, 'error');
                }
            } catch (error) {
                console.error('Error fetching SOA details:', error);
                this.showToastMessage('Error loading SOA details', 'error');
            }
        },

        toggleAllItems(event) {
            const checked = event.target.checked;
            this.soaItems.forEach(item => {
                item.selected = checked;
            });
            this.calculateTotal();
        },

        calculateTotal() {
            this.totalAmount = this.soaItems
                .filter(item => item.selected)
                .reduce((sum, item) => sum + (item.quantity * item.price_per_unit), 0);
        },

        async createOrder() {
            if (!this.canSubmit) return;

            const selectedItems = this.soaItems.filter(item => item.selected);

            try {
                const response = await fetch('/transport-orders/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to_ref_no: this.newOrder.to_ref_no,
                        billing_id: this.newOrder.billing_id,
                        sipa_ref_no: this.newOrder.sipa_ref_no,
                        items: selectedItems,
                        total_amount: this.totalAmount
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showToastMessage('Transport Order created successfully!', 'success');
                    this.closeModal();
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.showToastMessage(result.message || 'Error creating order', 'error');
                }
            } catch (error) {
                console.error('Error creating transport order:', error);
                this.showToastMessage('Error creating transport order', 'error');
            }
        },

        showToastMessage(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', { 
        detail: { message, type } 
    }));
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