<nav x-data="{ open: false }" class="bg-white border-b shadow sticky top-0 z-50">
    <!-- TOP BAR — Logo + Main Links -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
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

            <!-- RIGHT SIDE -->
            <div class="hidden md:flex items-center space-x-4 lg:space-x-6">

                @auth
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

                    <!-- CART LINK -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-blue-600">
                        Cart
                        @if(session('cart_count', 0) > 0)
                            <span class="absolute -top-2 -right-3 bg-red-600 text-white text-xs rounded-full px-1">
                                {{ session('cart_count') }}
                            </span>
                        @endif
                    </a>

                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="text-blue-600 font-medium">Register</a>
                @endauth

            </div>

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

    <!-- MOBILE MENU -->
    <div x-show="open" x-transition class="md:hidden border-t bg-white">
        <div class="px-4 py-3 space-y-1 text-gray-700 font-medium">

            <a href="{{ route('home', ['tipas' => 'preke']) }}" class="block px-2 py-2 rounded hover:bg-gray-100">Products</a>
            <a href="{{ route('home', ['tipas' => 'paslauga']) }}" class="block px-2 py-2 rounded hover:bg-gray-100">Services</a>
            <a href="{{ route('favorites.page') }}" class="block px-2 py-2 rounded hover:bg-gray-100">My Favorites</a>

            @auth
                <a href="{{ route('my.listings') }}" class="block px-2 py-2 rounded hover:bg-gray-100">My Listings</a>
                <a href="{{ route('listing.create') }}" class="block px-2 py-2 rounded hover:bg-gray-100">Post a Listing</a>
                <a href="{{ route('buyer.orders') }}" class="block px-2 py-2 rounded hover:bg-gray-100">My purchases</a>

                @if(auth()->user()->role === 'seller')
                    <a href="{{ route('seller.orders') }}" class="block px-2 py-2 rounded hover:bg-gray-100">My sales</a>
                @endif

                <a href="{{ route('cart.index') }}" class="block px-2 py-2 rounded hover:bg-gray-100">Cart</a>
                <a href="{{ route('profile.edit') }}" class="block px-2 py-2 rounded hover:bg-gray-100">Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full text-left px-2 py-2 rounded hover:bg-gray-100">
                        Log out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-2 py-2 rounded hover:bg-gray-100">Log in</a>
                <a href="{{ route('register') }}" class="block px-2 py-2 rounded hover:bg-gray-100 text-blue-600">Register</a>
            @endauth

        </div>
    </div>

@php
    $showSearchNav = request()->routeIs('home', 'search.listings');
@endphp

@if($showSearchNav)
    <!-- BOTTOM BAR — Search + Filters -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row gap-3 sm:items-center">

            <!-- SEARCH BAR -->
            <form action="{{ route('search.listings') }}" method="GET" class="flex flex-grow max-w-3xl">
                <input 
                    type="text"
                    name="q"
                    class="flex-grow border rounded-l px-4 py-2"
                    placeholder="Search for listing..."
                    value="{{ request('q') }}"
                >
                <button class="bg-blue-600 text-white px-4 py-2 rounded-r">
                    Search
                </button>
            </form>

            <!-- FILTERS BUTTON -->
            <button 
                @click="$dispatch('toggle-filters')"
                class="border px-4 py-2 rounded hover:bg-gray-100"
            >
                Filters
            </button>

            <!-- SORT -->
            <form method="GET" action="{{ url()->current() }}">
                @foreach(request()->except('sort') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <select 
                    name="sort" 
                    onchange="this.form.submit()" 
                    class="border px-3 py-2 rounded"
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
@endif
</nav>
