<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADTMS - Reports & Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
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
                <h1 class="text-2xl font-semibold text-gray-800">Reports & Analytics</h1>
            </div>

        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8" x-data="dashboardApp()">
            
            <!-- COLLAPSIBLE FILTER BAR -->
            <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" x-data="{ filterOpen: false }">
                <button @click="filterOpen = !filterOpen" 
                        class="w-full flex items-center justify-between px-6 py-3 text-left hover:bg-gray-50 transition">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="font-medium text-gray-700">Date Filter</span>
                        <span class="text-xs text-gray-500" x-show="filterOpen === false">(Click to expand)</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': filterOpen }" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div x-show="filterOpen" x-collapse class="border-t border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('reports.analytics') }}" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                                <input type="date" name="from_date" 
                                       value="{{ request('from_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                                <input type="date" name="to_date" 
                                       value="{{ request('to_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                    Apply Filter
                                </button>
                                @if(request('from_date') || request('to_date'))
                                <a href="{{ route('reports.analytics') }}" 
                                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                    Clear
                                </a>
                                @endif
                            </div>
                            @if(request('from_date') || request('to_date'))
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Filtering: {{ request('from_date') }} to {{ request('to_date') }}
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Summary Cards (4 Cards - REMOVED Total Clients, ALL CLICKABLE) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    
    <!-- Total SIPA (CLICKABLE) -->
    <a href="{{ route('sipa-requests') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-purple-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total SIPA</p>
                <h3 class="text-xl font-bold text-gray-800 text-right" x-text="stats.totalSipa"></h3>
                <p class="text-xs text-purple-600 mt-1 group-hover:translate-x-1 transition-transform text-right">View →</p>
            </div>
        </div>
    </a>

    <!-- Total Trips (CLICKABLE) -->
    <a href="{{ route('TD.TripClient') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-green-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Total Trips</p>
                <h3 class="text-xl font-bold text-gray-800 text-right" x-text="stats.totalTrips"></h3>
                <p class="text-xs text-green-600 mt-1 group-hover:translate-x-1 transition-transform text-right">View →</p>
            </div>
        </div>
    </a>

    <!-- Transport Orders (CLICKABLE) -->
    <a href="{{ route('transport-orders.index') }}" 
       class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
        <div class="flex items-center justify-between">
            <div class="bg-orange-100 p-2 rounded-full group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-1">Transport Orders</p>
                <p class="text-lg font-bold text-gray-800 text-right">
                    <span class="text-green-600" x-text="stats.completedOrders"></span>
                    <span class="text-gray-400 text-base mx-1">/</span>
                    <span class="text-yellow-600" x-text="stats.pendingOrders"></span>
                </p>
                <p class="text-xs text-orange-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View →</p>
            </div>
        </div>
    </a>

    <!-- Total Sales (CLICKABLE - Now links to Invoices) -->
<a href="{{ route('invoices.index') }}" 
   class="bg-white rounded-lg shadow p-4 hover:shadow-lg hover:scale-[1.02] transition-all duration-200 cursor-pointer group block">
    <div class="flex items-center justify-between">
        <div class="bg-indigo-100 p-2 rounded-full group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-600 mb-1">Total Sales</p>
            <h3 class="text-xl font-bold text-gray-800 text-right" x-text="'₱' + stats.totalRevenue.toLocaleString()"></h3>
            <p class="text-xs text-indigo-600 mt-1 text-right group-hover:translate-x-1 transition-transform">View →</p>
        </div>
    </div>
</a>

</div>

           <!-- Revenue Trend Chart - Full Width -->
<div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue Trend</h3>
    <div id="revenueTrendChart" style="height: 350px;"></div>
</div>

            <!-- Trips/Dispatch Status Bar Chart -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Trips & Dispatch Status</h3>
                <div id="tripsStatusChart" style="height: 300px;"></div>
            </div>

            <!-- Recent Activity Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Recent Transport Orders -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Transport Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">TO Ref</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">From</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">To</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="order in recentOrders" :key="order.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="order.ref"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="order.from"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="order.to"></td>
                                        <td class="px-4 py-3">
                                            <span 
                                                :class="{
                                                    'text-yellow-600': order.status === 'Pending',
                                                    'text-green-600': order.status === 'Approved',
                                                    'text-red-600': order.status === 'Declined'
                                                }"
                                                class="text-xs font-semibold"
                                                x-text="order.status">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Invoices -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Invoices</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="invoice in recentInvoices" :key="invoice.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="invoice.no"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="invoice.client"></td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900" x-text="'₱' + invoice.total.toLocaleString()"></td>
                                        <td class="px-4 py-3">
                                            <span 
                                                :class="{
                                                    'text-yellow-600': invoice.status === 'pending',
                                                    'text-green-600': invoice.status === 'paid',
                                                    'text-red-600': invoice.status === 'failed'
                                                }"
                                                class="text-xs font-semibold capitalize"
                                                x-text="invoice.status">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Trips -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Trips</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trip ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vehicle</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="trip in recentTrips" :key="trip.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="trip.id"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="trip.vehicle"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="trip.driver"></td>
                                        <td class="px-4 py-3">
                                            <span 
                                                :class="{
                                                    'text-blue-600': trip.status === 'In Transit',
                                                    'text-green-600': trip.status === 'Completed',
                                                    'text-yellow-600': trip.status === 'Scheduled'
                                                }"
                                                class="text-xs font-semibold"
                                                x-text="trip.status">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    function dashboardApp() {
    return {
        stats: {
            // REMOVED: totalClients
            totalSipa: {{ $totalSipa }},
            totalTrips: {{ $totalTrips }},
            completedOrders: {{ $completedOrders }},
            pendingOrders: {{ $pendingOrders }},
            totalRevenue: {{ $totalRevenue }}
        },
        recentOrders: @json($recentOrders),
        recentInvoices: @json($recentInvoices),
        recentTrips: @json($recentTrips)
    }
}

    // Initialize Charts after Alpine loads
    document.addEventListener('alpine:init', () => {
        setTimeout(() => {
          
          // Revenue Trend Chart with ECharts
var chartDom = document.getElementById('revenueTrendChart');
var myChart = echarts.init(chartDom);

const rawData = @json($revenueTrendData);

// If no real data, generate sample data for last 8 quarters
let displayData = rawData;
if (!rawData || rawData.length === 0 || rawData.every(d => d.Revenue === 0)) {
    displayData = [];
    const currentYear = new Date().getFullYear();
    const currentQuarter = Math.ceil((new Date().getMonth() + 1) / 3);
    
    for (let i = 7; i >= 0; i--) {
        const yearOffset = Math.floor(i / 4);
        const quarterOffset = i % 4;
        const year = currentYear - yearOffset;
        const quarter = ((currentQuarter - quarterOffset - 1 + 4) % 4) + 1;
        const period = year + ' Q' + quarter;
        
        // Generate realistic sample revenue
        const baseRevenue = 150000 + Math.random() * 100000;
        const invoiceRevenue = 200000 + Math.random() * 150000;
        
        displayData.push({
            Period: period,
            Category: 'Transport Orders',
            Revenue: Math.round(baseRevenue)
        });
        displayData.push({
            Period: period,
            Category: 'Invoices',
            Revenue: Math.round(invoiceRevenue)
        });
    }
}

const categories = [...new Set(displayData.map(d => d.Category))];
const datasetWithFilters = [];
const seriesList = [];

categories.forEach(function(category) {
    var datasetId = 'dataset_' + category;
    datasetWithFilters.push({
        id: datasetId,
        fromDatasetId: 'dataset_raw',
        transform: {
            type: 'filter',
            config: {
                and: [
                    { dimension: 'Category', '=': category }
                ]
            }
        }
    });
    seriesList.push({
        type: 'line',
        datasetId: datasetId,
        showSymbol: true,
        symbolSize: 8,
        name: category,
        endLabel: {
            show: true,
            formatter: function(params) {
                if (params.value && params.value[2] !== undefined && params.value[2] !== null) {
                    return params.value[1] + ': ₱' + (params.value[2] / 1000) + 'k';
                }
                return '';
            }
        },
        labelLayout: {
            moveOverlap: 'shiftY'
        },
        emphasis: {
            focus: 'series'
        },
        encode: {
            x: 'Period',
            y: 'Revenue',
            label: ['Category', 'Revenue'],
            itemName: 'Period',
            tooltip: ['Revenue']
        }
    });
});

var option = {
    animationDuration: 10000,
    dataset: [
        {
            id: 'dataset_raw',
            source: displayData
        },
        ...datasetWithFilters
    ],
    title: {
        text: 'Quarterly Revenue Growth',
        subtext: 'Click on any data point to see analysis'
    },
    tooltip: {
        order: 'valueDesc',
        trigger: 'axis',
        formatter: function(params) {
            let result = params[0].axisValue + '<br/>';
            params.forEach(param => {
                if (param.value && param.value[2]) {
                    result += param.marker + ' ' + param.seriesName + ': ₱' + param.value[2].toLocaleString() + '<br/>';
                }
            });
            return result;
        }
    },
    xAxis: {
        type: 'category',
        nameLocation: 'middle'
    },
    yAxis: {
    name: 'Revenue (₱)',
    min: 0,
    max: 500000,
    interval: 100000,
    axisLabel: {
        formatter: function(value) {
            return '₱' + (value / 1000) + 'k';
        }
    }
},
    grid: {
        right: 180
    },
    series: seriesList
};

myChart.setOption(option);

// Function to generate quarterly analysis
function generateQuarterlyAnalysis(category, period, revenue) {
    const categoryData = displayData
        .filter(d => d.Category === category)
        .sort((a, b) => {
            const [aYear, aQ] = a.Period.split(' Q');
            const [bYear, bQ] = b.Period.split(' Q');
            return aYear === bYear ? aQ - bQ : aYear - bYear;
        });
    
    const currentIndex = categoryData.findIndex(d => d.Period === period);
    
    if (currentIndex === -1) return "No data available for analysis.";
    
    const currentRevenue = categoryData[currentIndex].Revenue;
    let analysisText = `In ${period}, ${category} generated ₱${currentRevenue.toLocaleString()} in revenue. `;
    
    // Compare with previous quarter
    if (currentIndex > 0) {
        const prevRevenue = categoryData[currentIndex - 1].Revenue;
        const prevPeriod = categoryData[currentIndex - 1].Period;
        const change = currentRevenue - prevRevenue;
        const percentChange = ((change / prevRevenue) * 100).toFixed(1);
        
        if (change > 0) {
            analysisText += `This represents a <span style="color: #4ade80; font-weight: bold;">+${percentChange}%</span> increase (₱${change.toLocaleString()}) compared to ${prevPeriod}. `;
        } else if (change < 0) {
            analysisText += `This represents a <span style="color: #f87171; font-weight: bold;">${percentChange}%</span> decrease (₱${Math.abs(change).toLocaleString()}) compared to ${prevPeriod}. `;
        } else {
            analysisText += `This is unchanged from ${prevPeriod}. `;
        }
    }
    
    // Compare with next quarter if available
    if (currentIndex < categoryData.length - 1) {
        const nextRevenue = categoryData[currentIndex + 1].Revenue;
        const nextPeriod = categoryData[currentIndex + 1].Period;
        const change = nextRevenue - currentRevenue;
        const percentChange = ((change / currentRevenue) * 100).toFixed(1);
        
        if (change > 0) {
            analysisText += `Revenue then grew to ₱${nextRevenue.toLocaleString()} in ${nextPeriod} (<span style="color: #4ade80; font-weight: bold;">+${percentChange}%</span>).`;
        } else if (change < 0) {
            analysisText += `Revenue then declined to ₱${nextRevenue.toLocaleString()} in ${nextPeriod} (<span style="color: #f87171; font-weight: bold;">${percentChange}%</span>).`;
        } else {
            analysisText += `Revenue remained stable at ₱${nextRevenue.toLocaleString()} in ${nextPeriod}.`;
        }
    }
    
    // Compare year-over-year (same quarter, previous year)
    const [currentYear, currentQ] = period.split(' Q');
    const prevYearPeriod = (parseInt(currentYear) - 1) + ' Q' + currentQ;
    const prevYearData = categoryData.find(d => d.Period === prevYearPeriod);
    
    if (prevYearData) {
        const yoyChange = currentRevenue - prevYearData.Revenue;
        const yoyPercent = ((yoyChange / prevYearData.Revenue) * 100).toFixed(1);
        
        analysisText += ` <br/><br/><strong>Year-over-year:</strong> `;
        if (yoyChange > 0) {
            analysisText += `Revenue increased by <span style="color: #4ade80; font-weight: bold;">+${yoyPercent}%</span> compared to ${prevYearPeriod}.`;
        } else if (yoyChange < 0) {
            analysisText += `Revenue decreased by <span style="color: #f87171; font-weight: bold;">${yoyPercent}%</span> compared to ${prevYearPeriod}.`;
        } else {
            analysisText += `Revenue is unchanged compared to ${prevYearPeriod}.`;
        }
    }
    
    return analysisText;
}

// Create analysis box if it doesn't exist
let analysisBox = document.getElementById('analysisBox');
if (!analysisBox) {
    analysisBox = document.createElement('div');
    analysisBox.id = 'analysisBox';
    analysisBox.style.cssText = `
        display: none;
        margin-top: 20px;
        padding: 16px 0;
        color: #374151;
        font-size: 15px;
        line-height: 1.6;
    `;
    chartDom.parentNode.insertBefore(analysisBox, chartDom.nextSibling);
}

// Add click event listener
myChart.on('click', function(params) {
    if (params.componentType === 'series') {
        const category = params.seriesName;
        
        let period, revenue;
        
        if (params.data && Array.isArray(params.data)) {
            period = params.data[0];
            revenue = params.data[2];
        } else if (params.value) {
            if (Array.isArray(params.value)) {
                period = params.value[0];
                revenue = params.value[2];
            } else {
                period = params.name;
                revenue = params.value;
            }
        }
        
        if (!period || !revenue) {
            const dataPoint = displayData.find(d => 
                d.Category === category && 
                (d.Period === params.name || d.Period === period)
            );
            if (dataPoint) {
                period = dataPoint.Period;
                revenue = dataPoint.Revenue;
            }
        }
        
        console.log('Clicked:', { category, period, revenue, params });
        
        const analysis = generateQuarterlyAnalysis(category, period, revenue);
        
        analysisBox.innerHTML = `<strong>Analysis:</strong> ${analysis}`;
        analysisBox.style.display = 'block';
        
        analysisBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});

window.addEventListener('resize', function() {
    myChart.resize();
});

         // Trips Status Bar Chart with ECharts
var tripsChartDom = document.getElementById('tripsStatusChart');
var tripsChart = echarts.init(tripsChartDom);


const tripsData = @json($weeklyTripsData);

var tripsOption = {
    title: {
        text: 'Weekly Trips Status',
        subtext: 'Click on any bar to see analysis'
    },
    xAxis: {
        type: 'category',
        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
        type: 'value',
        name: 'Number of Trips'
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        },
        formatter: function(params) {
            return params[0].name + '<br/>' + 
                   params[0].marker + ' Trips: ' + params[0].value;
        }
    },
    series: [
    {
        data: tripsData.map(d => d.trips),
            type: 'bar',
            itemStyle: {
                color: '#667eea'
            },
            emphasis: {
                itemStyle: {
                    color: '#764ba2'
                }
            }
        }
    ]
};

tripsChart.setOption(tripsOption);

// Function to generate trips analysis
function generateTripsAnalysis(day, trips) {
    const dayIndex = tripsData.findIndex(d => d.day === day);
    
    if (dayIndex === -1) return "No data available for analysis.";
    
    // Helper function for singular/plural
    const tripWord = (count) => count === 1 ? 'trip' : 'trips';
    
    let analysisText = `On ${day}, there were <strong>${trips} ${tripWord(trips)}</strong> completed. `;
    
    // Calculate weekly average
    const totalTrips = tripsData.reduce((sum, d) => sum + d.trips, 0);
    const avgTrips = totalTrips / tripsData.length;
    
    // Only show comparison if average is meaningful (> 0)
    if (avgTrips > 0) {
        const diffFromAvg = trips - avgTrips;
        const percentDiff = ((diffFromAvg / avgTrips) * 100).toFixed(0);
        
        if (Math.abs(diffFromAvg) >= 0.5) { // Only show if difference is significant
            if (diffFromAvg > 0) {
                analysisText += `This is <span style="color: #4ade80; font-weight: bold;">${percentDiff}% above</span> the weekly average of ${avgTrips.toFixed(1)} ${tripWord(Math.round(avgTrips))}. `;
            } else if (diffFromAvg < 0) {
                analysisText += `This is <span style="color: #f87171; font-weight: bold;">${Math.abs(percentDiff)}% below</span> the weekly average of ${avgTrips.toFixed(1)} ${tripWord(Math.round(avgTrips))}. `;
            }
        } else {
            analysisText += `This is close to the weekly average of ${avgTrips.toFixed(1)} ${tripWord(Math.round(avgTrips))}. `;
        }
    }
    
    // Compare with previous day
    if (dayIndex > 0) {
        const prevDay = tripsData[dayIndex - 1];
        const change = trips - prevDay.trips;
        
        if (prevDay.trips > 0) {
            const percentChange = ((change / prevDay.trips) * 100).toFixed(0);
            
            if (change > 0) {
                analysisText += `Compared to ${prevDay.day}, trips increased by <span style="color: #4ade80; font-weight: bold;">+${percentChange}%</span> (+${change} ${tripWord(change)}). `;
            } else if (change < 0) {
                analysisText += `Compared to ${prevDay.day}, trips decreased by <span style="color: #f87171; font-weight: bold;">${percentChange}%</span> (${change} ${tripWord(Math.abs(change))}). `;
            } else {
                analysisText += `Trips remained the same as ${prevDay.day}. `;
            }
        } else {
            // Previous day had 0 trips
            if (change > 0) {
                analysisText += `Compared to ${prevDay.day} (which had no trips), activity increased with ${trips} ${tripWord(trips)}. `;
            }
        }
    }
    
    // Compare with next day if available
    if (dayIndex < tripsData.length - 1) {
        const nextDay = tripsData[dayIndex + 1];
        const change = nextDay.trips - trips;
        
        if (trips > 0) {
            const percentChange = ((change / trips) * 100).toFixed(0);
            
            if (change > 0) {
                analysisText += `On ${nextDay.day}, trips increased to <strong>${nextDay.trips} ${tripWord(nextDay.trips)}</strong> (+${percentChange}%).`;
            } else if (change < 0) {
                analysisText += `On ${nextDay.day}, trips decreased to <strong>${nextDay.trips} ${tripWord(nextDay.trips)}</strong> (${percentChange}%).`;
            } else {
                analysisText += `On ${nextDay.day}, trips remained at <strong>${nextDay.trips} ${tripWord(nextDay.trips)}</strong>.`;
            }
        } else {
            // Current day had 0 trips
            if (nextDay.trips > 0) {
                analysisText += `On ${nextDay.day}, trips increased to <strong>${nextDay.trips} ${tripWord(nextDay.trips)}</strong>.`;
            } else {
                analysisText += `On ${nextDay.day}, trips also remained at <strong>0</strong>.`;
            }
        }
    }
    
    // Find peak day
    const maxTrips = Math.max(...tripsData.map(d => d.trips));
    const peakDay = tripsData.find(d => d.trips === maxTrips);
    
    if (maxTrips > 0) {
        if (trips === maxTrips) {
            analysisText += ` <br/><br/> <strong>${day} was the busiest day this week!</strong>`;
        } else {
            analysisText += ` <br/><br/>Peak day was <strong>${peakDay.day}</strong> with ${maxTrips} ${tripWord(maxTrips)}.`;
        }
    } else {
        analysisText += ` <br/><br/>ℹ️ No trips were recorded this week.`;
    }
    
    return analysisText;
}

// Create analysis box if it doesn't exist
let tripsAnalysisBox = document.getElementById('tripsAnalysisBox');
if (!tripsAnalysisBox) {
    tripsAnalysisBox = document.createElement('div');
    tripsAnalysisBox.id = 'tripsAnalysisBox';
    tripsAnalysisBox.style.cssText = `
        display: none;
        margin-top: 20px;
        padding: 16px 0;
        color: #374151;
        font-size: 15px;
        line-height: 1.6;
    `;
    tripsChartDom.parentNode.insertBefore(tripsAnalysisBox, tripsChartDom.nextSibling);
}

// Add click event listener
tripsChart.on('click', function(params) {
    if (params.componentType === 'series') {
        const day = params.name;
        let trips = params.value;
        
        // Handle case where value might be an object
        if (typeof trips === 'object' && trips.value) {
            trips = trips.value;
        }
        
        // Fallback: search in tripsData
        if (!trips) {
            const dataPoint = tripsData.find(d => d.day === day);
            if (dataPoint) {
                trips = dataPoint.trips;
            }
        }
        
        console.log('Clicked:', { day, trips, params }); // Debug
        
        const analysis = generateTripsAnalysis(day, trips);
        
        // Display analysis below the chart
        tripsAnalysisBox.innerHTML = `<strong>Trips Analysis:</strong> ${analysis}`;
        tripsAnalysisBox.style.display = 'block';
        
        // Smooth scroll to analysis
        tripsAnalysisBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    tripsChart.resize();
});

            

            // Trips Status Bar Chart
            const tripsStatusCtx = document.getElementById('tripsStatusChart').getContext('2d');
            new Chart(tripsStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['Scheduled', 'In Transit', 'Completed', 'Cancelled'],
                    datasets: [{
                        label: 'Trips',
                        data: [45, 89, 198, 10],
                        backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }, 100);
    });
</script>
</body>
</html>