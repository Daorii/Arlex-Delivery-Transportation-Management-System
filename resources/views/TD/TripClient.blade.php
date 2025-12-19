<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ADTMS - Trip Dispatch Client List</title>
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


    @include('partials.admin_sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-4 h-[55px] w-auto">
                <h1 class="text-2xl font-semibold text-gray-800">Trip Dispatch - Client List</h1>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">

            <div class="bg-white rounded-lg shadow">

                <!-- Table Header -->
<div class="p-6 border-b border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <h3 class="text-lg font-semibold text-gray-800">Clients</h3>
        
        <!-- Search Bar -->
        <form action="{{ route('TD.TripClient') }}" method="GET" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search clients..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            @if($search ?? false)
                <a href="{{ route('TD.TripClient') }}" 
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

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Client ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Client Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Contact Person</th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
@forelse($clients as $client)
    <tr class="hover:bg-gray-50 cursor-pointer transition"
        onclick="window.location.href='{{ url('trip-client/company/' . $client->client_id) }}'">
        <td class="px-6 py-4 text-sm text-gray-900">{{ $client->client_id }}</td>
        <td class="px-6 py-4 text-sm text-gray-900">{{ $client->client_name }}</td>
        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->address }}</td>
        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->contact_person }}</td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-8 text-gray-500">No clients found</td>
    </tr>
@endforelse
</tbody>
                    </table>
                </div>

</table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $clients->firstItem() ?? 0 }}</span> 
                    to <span class="font-medium">{{ $clients->lastItem() ?? 0 }}</span> 
                    of <span class="font-medium">{{ $clients->total() }}</span> results
                    @if($search ?? false)
                        <span class="text-blue-600">(filtered)</span>
                    @endif
                </div>
                
                <div class="flex space-x-2">
                    {{-- Previous Button --}}
                    @if ($clients->onFirstPage())
                        <span class="px-3 py-1 border border-gray-300 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed text-sm">
                            Previous
                        </span>
                    @else
                        <a href="{{ $clients->appends(['search' => $search])->previousPageUrl() }}" 
                           class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
                        @if ($page == $clients->currentPage())
                            <span class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $clients->appends(['search' => $search])->url($page) }}" 
                               class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($clients->hasMorePages())
                        <a href="{{ $clients->appends(['search' => $search])->nextPageUrl() }}" 
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


</body>
</html>
