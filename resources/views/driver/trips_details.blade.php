@extends('layouts.app')

@section('title', 'Trip Details')

@section('content')
<div class="bg-gray-50 min-h-screen">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-4 sm:px-8 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2 sm:space-x-4">
                <a href="{{ route('driver.dispatches') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">
                    <span class="hidden sm:inline">Trip Details - Dispatch #{{ $dispatch->dispatch_id }}</span>
                    <span class="sm:hidden">Dispatch #{{ $dispatch->dispatch_id }}</span>
                </h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-8">

            <!-- Dispatch Info Card -->
            <div class="bg-white rounded-lg shadow mb-6 p-4 sm:p-6 border border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Dispatch Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Driver</p>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $driver->fname }} {{ $driver->lname }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Truck</p>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $truck->plate_no }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Route</p>
                        <p class="font-semibold text-gray-900 text-sm sm:text-base">{{ $sipaDetail->route_from }} → {{ $sipaDetail->route_to }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Effectivity</p>
                        <p class="font-semibold text-sm sm:text-base
                            @if($isExpired) text-red-600 @else text-green-600 @endif">
                            @if($isExpired)
                                Expired
                            @else
                                Valid until {{ date('M d, Y', strtotime($sipaDetail->effectivity_to)) }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Expiration Warning -->
                @if($isExpired)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">This dispatch has expired</p>
                            <p class="text-xs text-red-700 mt-1">You can no longer add new trip details. Expired on {{ date('M d, Y', strtotime($sipaDetail->effectivity_to)) }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Trip Details Table Card -->
            <div class="bg-white rounded-lg shadow border border-gray-100">
                <div class="p-4 sm:p-6 border-b border-gray-200">
    <div class="flex flex-col space-y-4">
        <!-- Title and Add Button Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Trip Details List</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Manage your delivery records</p>
            </div>
            <button 
                @if($isExpired) disabled @endif
                onclick="@if(!$isExpired) document.getElementById('addModal').style.display = 'block' @endif"
                class="flex items-center justify-center px-4 py-2 rounded-lg font-medium text-sm sm:text-base transition-colors
                    @if($isExpired)
                        bg-gray-300 text-gray-500 cursor-not-allowed
                    @else
                        bg-blue-600 text-white hover:bg-blue-700
                    @endif">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Trip Detail
            </button>
        </div>
        
        <!-- Search Bar Row -->
        <form action="{{ route('driver.trip.details', ['dispatch_id' => $dispatch->dispatch_id]) }}" method="GET" class="relative w-full sm:w-64 ml-auto">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search trips..." 
                   class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full text-sm">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('driver.trip.details', ['dispatch_id' => $dispatch->dispatch_id]) }}" 
                   class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                   title="Clear search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            @endif
        </form>
    </div>
</div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-200">
                    @forelse($tripDetails as $trip)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-xs text-gray-500">Container No</div>
                                <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $trip->container_no }}</div>
                            </div>
                            @if($trip->is_verified)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Verified</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Pending</span>
                            @endif
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div>
                                <div class="text-gray-500 text-xs">EIR No</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ $trip->eir_no }}</div>
                            </div>
                            
                            <div>
                                <div class="text-gray-500 text-xs">Delivery Date</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ $trip->delivery_date }}</div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-gray-500 text-xs">Size</div>
                                    <div class="font-medium text-gray-900 mt-0.5">{{ $trip->sipaDetail->size }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-xs">Type</div>
                                    <div class="font-medium text-gray-900 mt-0.5">{{ $trip->sipaDetail->sipa->type }}</div>
                                </div>
                            </div>
                            
                            <div class="pt-2 border-t border-gray-100">
                                <div class="text-gray-500 text-xs">Price</div>
                                <div class="font-bold text-green-600 mt-0.5">₱{{ number_format($trip->sipaDetail->price, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm">No trip details added yet.</p>
                        <p class="text-xs mt-1">Click "Add Trip Detail" to get started.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EIR No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tripDetails as $trip)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $trip->container_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $trip->eir_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ date('M d, Y', strtotime($trip->delivery_date)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $trip->sipaDetail->size }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $trip->sipaDetail->sipa->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">₱{{ number_format($trip->sipaDetail->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($trip->is_verified)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Verified</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm font-medium">No trip details added yet.</p>
                                        <p class="text-xs mt-1">Click "Add Trip Detail" to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
@if($tripDetails->total() > 0)
<div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-sm text-slate-600">
            Showing <span class="font-semibold">{{ $tripDetails->firstItem() ?? 0 }}</span> 
            to <span class="font-semibold">{{ $tripDetails->lastItem() ?? 0 }}</span> 
            of <span class="font-semibold">{{ $tripDetails->total() }}</span> trip details
            @if($search ?? false)
                <span class="text-blue-600 font-medium">(filtered)</span>
            @endif
        </div>
        
        <div class="flex flex-wrap gap-2">
            {{-- Previous Button --}}
            @if ($tripDetails->onFirstPage())
                <span class="px-3 py-1.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                    Previous
                </span>
            @else
                <a href="{{ $tripDetails->appends(['search' => $search])->previousPageUrl() }}" 
                   class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-slate-700">
                    Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($tripDetails->getUrlRange(1, $tripDetails->lastPage()) as $page => $url)
                @if ($page == $tripDetails->currentPage())
                    <span class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                @else
                    <a href="{{ $tripDetails->appends(['search' => $search])->url($page) }}" 
                       class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-slate-700">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($tripDetails->hasMorePages())
                <a href="{{ $tripDetails->appends(['search' => $search])->nextPageUrl() }}" 
                   class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-slate-700">
                    Next
                </a>
            @else
                <span class="px-3 py-1.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                    Next
                </span>
            @endif
        </div>
    </div>
</div>
@endif
            </div>
        </main>
    </div>
</div>

<!-- Add Trip Detail Modal -->
<div id="addModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addModal').style.display = 'none'"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-semibold text-white">Add Trip Detail</h3>
                <button onclick="document.getElementById('addModal').style.display = 'none'" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="{{ route('driver.trip.store') }}" id="tripForm">
                @csrf
                <input type="hidden" name="dispatch_id" value="{{ $dispatch->dispatch_id }}">

                <div class="bg-white px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                        <!-- Container No -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Container No <span class="text-red-500">*</span></label>
                            <input type="text" name="container_no" required
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" 
                                placeholder="Enter container number">
                        </div>

                        <!-- EIR No -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">EIR No <span class="text-red-500">*</span></label>
                            <input type="text" name="eir_no" required
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" 
                                placeholder="Enter EIR number">
                        </div>

                        <!-- Delivery Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Date <span class="text-red-500">*</span></label>
                            <input type="date" name="delivery_date" required
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                        </div>

                        <!-- Size -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Size <span class="text-red-500">*</span></label>
                            <select name="sipa_detail_id" id="sizeSelect" required
                                    onchange="updatePrice()"
                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base">
                                <option value="" disabled selected>Select size</option>
                                @foreach($sipaDetails as $detail)
                                    <option value="{{ $detail->sipa_detail_id }}" data-price="{{ $detail->price }}">
                                        {{ $detail->size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                            <input type="text" value="{{ $sipa->type }}" readonly
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm sm:text-base">
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                            <input type="text" id="priceDisplay" readonly
                                class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed text-sm sm:text-base">
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 border-t">
                    <button type="button" onclick="document.getElementById('addModal').style.display = 'none'"
                        class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm sm:text-base transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm sm:text-base transition-colors">
                        Add Trip Detail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePrice() {
    const select = document.getElementById('sizeSelect');
    const option = select.options[select.selectedIndex];
    const price = option.dataset.price;
    document.getElementById('priceDisplay').value = price ? '₱' + parseFloat(price).toFixed(2) : '';
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

@endsection