<nav x-data="{ open: false }" class="bg-white border-b shadow sticky top-0 z-50">
    <!-- TOP BAR — Logo + Main Links -->
    <div class="bg-white border-b">
        <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- LEFT: LOGO + MAIN LINKS -->
            <div class="flex items-center space-x-8">
                
                <!-- LOGO -->
                <a href="<?php echo e(route('home')); ?>" class="text-2xl font-bold text-blue-600">
                    eShop
                </a>

                <!-- MAIN NAVIGATION -->
                <div class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">

                    <!-- products -->
                    <a href="<?php echo e(route('home', ['tipas' => 'preke'])); ?>" class="hover:text-blue-600">
                        Products
                    </a>

                    <!-- services -->
                    <a href="<?php echo e(route('home', ['tipas' => 'paslauga'])); ?>" class="hover:text-blue-600">
                        Services
                    </a>

                    <a href="<?php echo e(route('favorites.page')); ?>" class="hover:text-blue-600">
                        My Favorites
                    </a>

                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('my.listings')); ?>" class="hover:text-blue-600">
                            My Listings
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="hover:text-blue-600">
                            My Listings
                        </a>
                    <?php endif; ?>

                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('listing.create')); ?>" class="hover:text-blue-600">
                            Post a Listing
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="hover:text-blue-600">
                            Post a Listing
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="hidden md:flex items-center space-x-6">

                <?php if(auth()->guard()->check()): ?>
                    <!-- CART LINK -->
                    <a href="<?php echo e(route('cart.index')); ?>" class="relative text-gray-700 hover:text-blue-600">
                        Cart
                        <?php if(session('cart_count', 0) > 0): ?>
                            <span class="absolute -top-2 -right-3 bg-red-600 text-white text-xs rounded-full px-1">
                                <?php echo e(session('cart_count')); ?>

                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- USER DROPDOWN -->
                    <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                         <?php $__env->slot('trigger', null, []); ?> 
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700">
                                <span><?php echo e(Auth::user()->vardas); ?></span>
                                <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.4a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                         <?php $__env->endSlot(); ?>

                         <?php $__env->slot('content', null, []); ?> 
                            <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                                Profile
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']); ?>
                                    Log out
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                            </form>
                         <?php $__env->endSlot(); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>

                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="<?php echo e(route('register')); ?>" class="text-blue-600 font-medium">Register</a>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- BOTTOM BAR — Search + Filters -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center space-x-4">

            <!-- SEARCH BAR -->
            <form action="<?php echo e(route('search.listings')); ?>" method="GET" class="flex flex-grow max-w-3xl">
                <input 
                    type="text"
                    name="q"
                    class="flex-grow border rounded-l px-4 py-2"
                    placeholder="Search for listing..."
                    value="<?php echo e(request('q')); ?>"
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
            <form method="GET" action="<?php echo e(url()->current()); ?>">
                <?php $__currentLoopData = request()->except('sort'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <select 
                    name="sort" 
                    onchange="this.form.submit()" 
                    class="border px-3 py-2 rounded"
                >
                    <option value="">Sort</option>
                    <option value="newest" <?php if(request('sort')=='newest'): echo 'selected'; endif; ?>>Newest first</option>
                    <option value="oldest" <?php if(request('sort')=='oldest'): echo 'selected'; endif; ?>>Oldest first</option>
                    <option value="price_asc" <?php if(request('sort')=='price_asc'): echo 'selected'; endif; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php if(request('sort')=='price_desc'): echo 'selected'; endif; ?>>Price: High to Low</option>
                </select>
            </form>

        </div>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>