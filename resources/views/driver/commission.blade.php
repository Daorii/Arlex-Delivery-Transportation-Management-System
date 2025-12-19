@extends('layouts.app')

@section('title', 'My Commissions')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-700">Commission Overview</h1>
                <p class="text-sm text-slate-500 mt-1">Track your earnings and payment status</p>
            </div>
            <a href="{{ route('driver.dashboard') }}" 
               class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium shadow-sm whitespace-nowrap text-center">
               ← Back
            </a>
        </div>

        <!-- Summary Cards (Larger Size with Right-Aligned Values) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    
    <!-- Total Commission Card (CLICKABLE) -->
    <button onclick="showCommissionModal('all')" 
            class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-emerald-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-left cursor-pointer group">
        <div class="flex items-center justify-between mb-6">
            <div class="bg-emerald-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Total Earned</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($allCommissions->where('status', 'Paid')->sum('commission_amount'), 2) }}</h3>
                <p class="text-xs text-green-600 mt-2">Confirmed payments</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-emerald-600 group-hover:translate-x-1 transition-transform">
            <span>View breakdown</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </button>

    <!-- Paid Amount Card (CLICKABLE) -->
    <button onclick="showCommissionModal('paid')" 
            class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-blue-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-left cursor-pointer group">
        <div class="flex items-center justify-between mb-6">
            <div class="bg-blue-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-blue-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Paid</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($allCommissions->where('status', 'Paid')->sum('commission_amount'), 2) }}</h3>
                <p class="text-xs text-gray-500 mt-2">{{ $allCommissions->where('status', 'Paid')->count() }} transactions</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-blue-600 group-hover:translate-x-1 transition-transform">
            <span>View paid commissions</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </button>

    <!-- Pending Card (CLICKABLE) -->
    <button onclick="showCommissionModal('pending')" 
            class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md p-8 border border-amber-100 hover:shadow-xl hover:scale-[1.02] transition-all duration-200 text-left cursor-pointer group">
        <div class="flex items-center justify-between mb-6">
            <div class="bg-amber-100 p-3 rounded-xl">
                <svg class="w-8 h-8 text-amber-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-2">Pending Payment</p>
                <h3 class="text-3xl font-bold text-gray-800">₱{{ number_format($allCommissions->where('status', 'Pending')->sum('commission_amount'), 2) }}</h3>
                <p class="text-xs text-gray-500 mt-2">{{ $allCommissions->where('status', 'Pending')->count() }} awaiting processing</p>
            </div>
        </div>
        <div class="flex items-center text-sm text-amber-600 group-hover:translate-x-1 transition-transform">
            <span>View pending commissions</span>
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </button>

</div>

        <!-- Commission History Table -->
        <div class="mt-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-semibold text-slate-700">Commission History</h2>
                    <p class="text-sm text-slate-500 mt-1">Recent transactions and payments</p>
                </div>
                
                <!-- Search Bar -->
                <form action="{{ route('driver.commission') }}" method="GET" class="relative w-full md:w-auto">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}"
                           placeholder="Search commissions..." 
                           class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($search ?? false)
                        <a href="{{ route('driver.commission') }}" 
                           class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                           title="Clear search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md overflow-hidden border border-gray-100">
                
                <!-- Mobile Card View -->
                <div class="lg:hidden divide-y divide-gray-100">
                    @forelse ($commissions as $c)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-sm font-mono font-semibold text-slate-700">
                                    {{ $c->dispatch_id }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ \Carbon\Carbon::parse($c->created_at)->format('M d, Y') }}
                                </div>
                            </div>
                            @if($c->status === 'Paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                    Paid
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>
                                    Pending
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Week Period:</span>
                                <span class="font-medium text-slate-700">{{ $c->week_period_text }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Trip Amount:</span>
                                <span class="font-medium text-slate-700">₱{{ number_format($c->total_trip_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Rate:</span>
                                <span class="font-medium text-blue-600">{{ number_format($c->commission_rate, 1) }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Commission:</span>
                                <span class="font-bold text-emerald-600">₱{{ number_format($c->commission_amount, 2) }}</span>
                            </div>
                            @if($c->paid_at)
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="text-slate-500">Paid Date:</span>
                                <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($c->paid_at)->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 font-medium">No commission records found</p>
                        <p class="mt-1 text-xs text-gray-400">Your commissions will appear here once they are processed</p>
                    </div>
                    @endforelse
                </div>

                <!-- Desktop Table Content -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Dispatch ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Week Period</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Trip Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Rate</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Commission</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Paid Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($commissions as $c)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-700">
                                        {{ \Carbon\Carbon::parse($c->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($c->created_at)->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-slate-700 font-semibold">
                                        {{ $c->dispatch_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-700">
                                        {{ $c->week_period_text }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-700">
                                        ₱{{ number_format($c->total_trip_amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600">
                                        {{ number_format($c->commission_rate, 1) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-emerald-600">
                                        ₱{{ number_format($c->commission_amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($c->status === 'Paid')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2"></span>
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-2"></span>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($c->paid_at)
                                        <div class="text-sm text-slate-700">
                                            {{ \Carbon\Carbon::parse($c->paid_at)->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($c->paid_at)->format('g:i A') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400 italic">Not paid yet</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 font-medium">No commission records found</p>
                                    <p class="mt-1 text-xs text-gray-400">Your commissions will appear here once they are processed</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($commissions->total() > 0)
                <div class="px-4 sm:px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="text-sm text-slate-600">
                            Showing <span class="font-semibold">{{ $commissions->firstItem() ?? 0 }}</span> 
                            to <span class="font-semibold">{{ $commissions->lastItem() ?? 0 }}</span> 
                            of <span class="font-semibold">{{ $commissions->total() }}</span> results
                            @if($search ?? false)
                                <span class="text-blue-600 font-medium">(filtered)</span>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            {{-- Previous Button --}}
                            @if ($commissions->onFirstPage())
                                <span class="px-3 py-1.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $commissions->appends(['search' => $search])->previousPageUrl() }}" 
                                   class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-slate-700">
                                    Previous
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($commissions->getUrlRange(1, $commissions->lastPage()) as $page => $url)
                                @if ($page == $commissions->currentPage())
                                    <span class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium">{{ $page }}</span>
                                @else
                                    <a href="{{ $commissions->appends(['search' => $search])->url($page) }}" 
                                       class="px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-slate-700">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next Button --}}
                            @if ($commissions->hasMorePages())
                                <a href="{{ $commissions->appends(['search' => $search])->nextPageUrl() }}" 
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
                    
                    <!-- Total Summary -->
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="text-sm text-slate-600 text-center sm:text-right">
                            <span class="font-semibold">Total Commission Earned:</span> 
                            <span class="text-emerald-600 font-bold text-base">₱{{ number_format($allCommissions->where('status', 'Paid')->sum('commission_amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif

           </div>
        </div>

    </div>
</div>

<!-- Commission Details Modal -->
<div id="commissionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeCommissionModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-blue-500 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white" id="modalTitle">Commission Details</h3>
                    <button onclick="closeCommissionModal()" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-white px-6 py-6">
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 border border-emerald-200">
                        <div class="text-sm text-emerald-600 font-medium mb-1">Total Amount</div>
                        <div class="text-2xl font-bold text-emerald-700" id="modalTotalAmount">₱0.00</div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                        <div class="text-sm text-blue-600 font-medium mb-1">Transaction Count</div>
                        <div class="text-2xl font-bold text-blue-700" id="modalTransactionCount">0</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                        <div class="text-sm text-purple-600 font-medium mb-1">Average Commission</div>
                        <div class="text-2xl font-bold text-purple-700" id="modalAverageAmount">₱0.00</div>
                    </div>
                </div>

                <!-- Detailed Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dispatch ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Week Period</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trip Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rate</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Commission</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="modalTableBody">
                            <!-- Table rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4">
                <button onclick="closeCommissionModal()" 
                        class="w-full sm:w-auto px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Commission data from backend
    const allCommissions = @json($allCommissions);

    function showCommissionModal(type) {
        let filteredCommissions = [];
        let modalTitle = '';

        // Filter commissions based on type
        if (type === 'all') {
            filteredCommissions = allCommissions.filter(c => c.status === 'Paid');
            modalTitle = 'Total Earned Commissions';
        } else if (type === 'paid') {
            filteredCommissions = allCommissions.filter(c => c.status === 'Paid');
            modalTitle = 'Paid Commissions';
        } else if (type === 'pending') {
            filteredCommissions = allCommissions.filter(c => c.status === 'Pending');
            modalTitle = 'Pending Commissions';
        }

        // Update modal title
        document.getElementById('modalTitle').textContent = modalTitle;

        // Calculate summary stats
        const totalAmount = filteredCommissions.reduce((sum, c) => sum + parseFloat(c.commission_amount), 0);
        const transactionCount = filteredCommissions.length;
        const averageAmount = transactionCount > 0 ? totalAmount / transactionCount : 0;

        // Update summary stats
        document.getElementById('modalTotalAmount').textContent = '₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('modalTransactionCount').textContent = transactionCount;
        document.getElementById('modalAverageAmount').textContent = '₱' + averageAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // Populate table
        const tableBody = document.getElementById('modalTableBody');
        tableBody.innerHTML = '';

        if (filteredCommissions.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        No ${type} commissions found
                    </td>
                </tr>
            `;
        } else {
            filteredCommissions.forEach(c => {
                const createdDate = new Date(c.created_at);
                const statusBadge = c.status === 'Paid' 
                    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>Paid</span>'
                    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>Pending</span>';

                const row = `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-700">
                            ${createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </td>
                        <td class="px-4 py-3 text-sm font-mono font-semibold text-gray-700">${c.dispatch_id}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${c.week_period_text}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">₱${parseFloat(c.total_trip_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td class="px-4 py-3 text-sm font-medium text-blue-600">${parseFloat(c.commission_rate).toFixed(1)}%</td>
                        <td class="px-4 py-3 text-sm font-bold text-emerald-600">₱${parseFloat(c.commission_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td class="px-4 py-3">${statusBadge}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }

        // Show modal
        document.getElementById('commissionModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scroll
    }

    function closeCommissionModal() {
        document.getElementById('commissionModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scroll
    }

    // Auto-submit search form after user stops typing
    let searchTimeout;
    document.querySelector('input[name="search"]')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCommissionModal();
        }
    });
</script>

@endsection