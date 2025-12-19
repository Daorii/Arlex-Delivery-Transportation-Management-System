<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-screen">

    <!-- Logo -->
    <div class="flex items-center justify-center py-6 border-b flex-shrink-0">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-24 w-auto scale-[2]">
    </div>

    <!-- Navigation (Scrollable) -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Client & SIPA -->
        <div x-data="{ 
            open: sessionStorage.getItem('clientSipaOpen') === 'true',
            toggle() {
                this.open = !this.open;
                sessionStorage.setItem('clientSipaOpen', this.open);
            }
        }">
            <button 
                @click="toggle()" 
                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('clients.*') || request()->routeIs('sipa-requests*') || request()->routeIs('sipa.*') || request()->is('sipa-request-client/*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('clients.*') || request()->routeIs('sipa-requests*') || request()->routeIs('sipa.*') || request()->is('sipa-request-client/*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Client & SIPA</span>
                </div>
                <svg 
                    class="w-4 h-4 transform transition-transform duration-300 text-gray-400 flex-shrink-0" 
                    :class="{ 'rotate-180': open }" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="open" 
                x-transition:enter="transition-all ease-in-out duration-300" 
                x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                x-transition:leave="transition-all ease-in-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                class="overflow-hidden space-y-0.5 mt-1 ml-6"
            >
                <a href="{{ route('clients.index') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('clients.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Clients
                </a>
                <a href="{{ route('sipa-requests') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('sipa-requests*') || request()->routeIs('sipa.*') || request()->is('sipa-request-client/*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    SIPA Requests
                </a>
            </div>
        </div>

        <!-- Trips & Dispatch -->
        <div x-data="{ 
            open: sessionStorage.getItem('tripsDispatchOpen') === 'true',
            toggle() {
                this.open = !this.open;
                sessionStorage.setItem('tripsDispatchOpen', this.open);
            }
        }">
            <button 
                @click="toggle()" 
                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('TD.*') || request()->is('drivers*') || request()->is('trucks*') || request()->routeIs('td.*') || request()->routeIs('commissions.*') || request()->is('trip-client/*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('TD.*') || request()->is('drivers*') || request()->is('trucks*') || request()->routeIs('td.*') || request()->routeIs('commissions.*') || request()->is('trip-client/*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"
                        ></path>
                    </svg>
                    <span class="font-medium">Trips & Dispatch</span>
                </div>
                <svg 
                    class="w-4 h-4 transform transition-transform duration-300 text-gray-400 flex-shrink-0" 
                    :class="{ 'rotate-180': open }" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="open"
                x-transition:enter="transition-all ease-in-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                x-transition:leave="transition-all ease-in-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                class="overflow-hidden space-y-0.5 mt-1 ml-6"
            >
                <a href="{{ route('TD.TripClient') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('TD.TripClient') || request()->is('trip-client/*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Trips
                </a>
                <a href="/drivers" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->is('drivers*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Drivers
                </a>
                <a href="/trucks" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->is('trucks*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Trucks
                </a>
                <a href="{{ route('td.driver-commission') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('td.driver-commission') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Driver Commissions
                </a>
                <a href="{{ route('commissions.records') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('commissions.records') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Commission Records
                </a>
            </div>
        </div>

        <!-- Billing -->
        <div x-data="{ 
            open: sessionStorage.getItem('billingOpen') === 'true',
            toggle() {
                this.open = !this.open;
                sessionStorage.setItem('billingOpen', this.open);
            }
        }">
            <button 
                @click="toggle()" 
                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('billing.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('billing.*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <span class="font-medium">Billing</span>
                </div>
                <svg 
                    class="w-4 h-4 transform transition-transform duration-300 text-gray-400 flex-shrink-0" 
                    :class="{ 'rotate-180': open }" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="open"
                x-transition:enter="transition-all ease-in-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                x-transition:leave="transition-all ease-in-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                class="overflow-hidden space-y-0.5 mt-1 ml-6"
            >
                <a href="{{ route('billing.index') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('billing.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Billing SOA
                </a>
                <a href="{{ route('billing.records') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('billing.records') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Billing Records
                </a>
            </div>
        </div>

        <!-- Transport Orders -->
        <a href="{{ route('transport-orders.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('transport-orders.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('transport-orders.*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
            </svg>
            <span class="font-medium">Transport Orders</span>
        </a>

        <!-- Invoices & Payments -->
        <div x-data="{ 
            open: sessionStorage.getItem('invoicesPaymentsOpen') === 'true',
            toggle() {
                this.open = !this.open;
                sessionStorage.setItem('invoicesPaymentsOpen', this.open);
            }
        }">
            <button 
                @click="toggle()" 
                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
            >
                <div class="flex items-center min-w-0">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        ></path>
                    </svg>
                    <span class="font-medium leading-tight">Invoices &<br>Payments</span>
                </div>
                <svg 
                    class="w-4 h-4 transform transition-transform duration-300 text-gray-400 flex-shrink-0 ml-2" 
                    :class="{ 'rotate-180': open }" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="open"
                x-transition:enter="transition-all ease-in-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
                x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                x-transition:leave="transition-all ease-in-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
                class="overflow-hidden space-y-0.5 mt-1 ml-6"
            >
                <a href="{{ route('invoices.index') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('invoices.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Invoices
                </a>
                <a href="{{ route('payments.index') }}" 
                   class="block px-4 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('payments.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    Payments
                </a>
            </div>
        </div>

        <!-- Reports & Analytics -->
        <a href="{{ route('reports.analytics') }}" 
           class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-indigo-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                ></path>
            </svg>
            <span class="font-medium">Reports & Analytics</span>
        </a>

        <!-- Divider -->
        <div class="my-4 border-t border-gray-200"></div>

        <!-- Logout -->
        <a href="{{ route('logout') }}" class="flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span class="font-semibold">Logout</span>
        </a>

    </nav>

</aside>