<nav x-data="{ open: false }" class="bg-white border-b shadow z-50">
    <!-- TOP BAR — Logo + Main Links -->
    <div class="bg-white border-b">
        <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center">
            
            <!-- LEFT: LOGO + MAIN LINKS -->
            <div class="flex items-center space-x-6 lg:space-x-8">
                
                <!-- LOGO -->
                <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                    eShop
                </a>

                <!-- MAIN NAVIGATION -->
                <div class="hidden md:flex items-center space-x-4 lg:space-x-6 text-gray-700 font-medium">

                    <!-- products -->
                    <a href="{{ route('home', ['tipas' => 'preke']) }}" class="hover:text-blue-600">
                        Products
                    </a>

                    <!-- services -->
                    <a href="{{ route('home', ['tipas' => 'paslauga']) }}" class="hover:text-blue-600">
                        Services
                    </a>

                    <a href="{{ route('favorites.page') }}" class="hover:text-blue-600">
                        My Favorites
                    </a>

                    @auth
                        <a href="{{ route('my.listings') }}" class="hover:text-blue-600">
                            My Listings
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-blue-600">
                            My Listings
                        </a>
                    @endauth

                    @auth
                        <a href="{{ route('listing.create') }}" class="hover:text-blue-600">
                            Post a Listing
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-blue-600">
                            Post a Listing
                        </a>
                    @endauth
                    
                    <a href="{{ route('buyer.orders') }}" class="hover:text-blue-600">
                        My purchases
                    </a>

@auth
    @if(auth()->user()->role === 'seller')
        <a href="{{ route('seller.orders') }}"
           class="hover:text-blue-600">
            My sales
        </a>
    @endif
@endauth

                </div>
            </div>

            <div class="flex-1"></div>
            <!-- RIGHT SIDE -->
            <div class="flex items-center space-x-4">

                @auth
                    <!-- CART LINK -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-blue-600">
                        Cart
                        @if(session('cart_count', 0) > 0)
                            <span class="absolute -top-2 -right-3 bg-red-600 text-white text-xs rounded-full px-1">
                                {{ session('cart_count') }}
                            </span>
                        @endif
                    </a>

                    <!-- USER DROPDOWN -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700">
                                <span>{{ Auth::user()->vardas }}</span>
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.4a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="text-blue-600 font-medium">Register</a>
                @endauth

                <!-- MOBILE MENU BUTTON -->
                <button
                    @click="open = !open"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded text-gray-700 hover:bg-gray-100"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>

        </div>
    </div>

@php
    $showSearchNav = request()->routeIs('home', 'search.listings');
@endphp

@if($showSearchNav)
    <!-- BOTTOM BAR — Search + Filters -->
    <div class="bg-gray-50 sticky top-16 z-40">
        <div class="px-4 sm:px-6 lg:px-8 py-3">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row gap-3 sm:items-center justify-center">

                <!-- SEARCH BAR -->
               <form action="{{ route('search.listings') }}" method="GET" class="grid grid-cols-3 sm:flex w-full sm:flex-grow sm:max-w-3xl mx-auto">
                    <input 
    type="text"
    name="q"
    class="col-span-2 sm:flex-grow border rounded-l px-4 py-2"
    placeholder="Search for listing..."
    value="{{ request('q') }}"
>
                  <button class="bg-blue-600 text-white px-4 py-2 rounded-r">
                        Search
                    </button>
                </form>

                <!-- FILTERS + SORT -->
                <div class="grid grid-cols-2 gap-3 sm:flex sm:gap-3 justify-center">
                    <button 
                        @click="$dispatch('toggle-filters')"
                        class="border px-4 py-2 rounded hover:bg-gray-100 w-full sm:w-auto"
                    >
                        Filters
                    </button>

                    <form method="GET" action="{{ url()->current() }}" class="w-full sm:w-auto">
                        @foreach(request()->except('sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <select 
                            name="sort" 
                            onchange="this.form.submit()" 
                            class="border px-3 py-2 rounded w-full sm:w-auto"
                        >
                            <option value="">Sort</option>
                            <option value="newest" @selected(request('sort')=='newest')>Newest first</option>
                            <option value="oldest" @selected(request('sort')=='oldest')>Oldest first</option>
                            <option value="price_asc" @selected(request('sort')=='price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected(request('sort')=='price_desc')>Price: High to Low</option>
                        </select>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endif
</nav>
