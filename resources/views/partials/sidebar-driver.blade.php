<aside x-cloak
       class="fixed top-0 left-0 h-full bg-white shadow-2xl transition-all duration-300 z-50 border-r border-slate-200"
       :class="{
           'w-16': collapsed && !mobileOpen,
           'w-64': !collapsed || mobileOpen,
           '-translate-x-full lg:translate-x-0': !mobileOpen
       }">

    <!-- Sidebar content -->
    <div class="flex flex-col h-full relative">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-5 border-b border-slate-200">
            <div class="flex items-center space-x-3" x-show="!collapsed || mobileOpen">
                <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-700">Driver Hub</h2>
            </div>

            <!-- Collapse Button Desktop -->
            <button @click="collapsed = !collapsed"
                    class="hidden lg:block p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          :d="collapsed ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 'M11 19l-7-7 7-7m8 14l-7-7 7-7'"/>
                </svg>
            </button>

            <!-- Close Button Mobile -->
            <button @click="mobileOpen = false"
                    class="lg:hidden p-2 rounded-lg hover:bg-slate-200 transition-colors text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('driver.dashboard') ?? '#' }}"
               class="flex items-center px-4 py-3 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 group"
               :class="collapsed && !mobileOpen ? 'justify-center' : ''">
                <svg class="w-5 h-5 flex-shrink-0" :class="(!collapsed || mobileOpen) ? 'mr-3' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="!collapsed || mobileOpen" class="font-medium">Dashboard</span>
            </a>

            <!-- My Profile -->
            <a href="{{ route('driver.profile') ?? '#' }}"
               class="flex items-center px-4 py-3 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 group"
               :class="collapsed && !mobileOpen ? 'justify-center' : ''">
                <svg class="w-5 h-5 flex-shrink-0" :class="(!collapsed || mobileOpen) ? 'mr-3' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="!collapsed || mobileOpen" class="font-medium">My Profile</span>
            </a>

            <!-- Commission -->
            <a href="{{ route('driver.commission') ?? '#' }}"
               class="flex items-center px-4 py-3 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 group"
               :class="collapsed && !mobileOpen ? 'justify-center' : ''">
                <svg class="w-5 h-5 flex-shrink-0" :class="(!collapsed || mobileOpen) ? 'mr-3' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!collapsed || mobileOpen" class="font-medium">Commission</span>
            </a>

            <!-- Trip Details -->
            <a href="{{ route('driver.dispatches') ?? '#' }}"
               class="flex items-center px-4 py-3 text-slate-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 group"
               :class="collapsed && !mobileOpen ? 'justify-center' : ''">
                <svg class="w-5 h-5 flex-shrink-0" :class="(!collapsed || mobileOpen) ? 'mr-3' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <span x-show="!collapsed || mobileOpen" class="font-medium">Trip Details</span>
            </a>

            <!-- Divider -->
            <div x-show="!collapsed || mobileOpen" class="py-2">
                <hr class="border-slate-200">
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-all duration-200"
                        :class="collapsed && !mobileOpen ? 'justify-center' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" :class="(!collapsed || mobileOpen) ? 'mr-3' : ''" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="!collapsed || mobileOpen" class="font-medium">Logout</span>
                </button>
            </form>
        </nav>

        <!-- Footer -->
        <div x-show="!collapsed || mobileOpen" class="px-4 py-3 border-t border-slate-200">
            <p class="text-xs text-slate-500 text-center">© 2025 Driver Hub</p>
        </div>
    </div>
</aside>