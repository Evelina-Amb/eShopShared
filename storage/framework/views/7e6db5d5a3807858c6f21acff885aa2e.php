<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div class="container mx-auto px-4 mt-10">

        
        <?php 
            $filters = array_filter($filters); 
        ?>

        <?php if(!empty($filters)): ?>
            <div class="flex flex-wrap gap-2 mb-6">
                
                <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php
                        $newFilters = $filters;
                        unset($newFilters[$key]);
                        $query = http_build_query($newFilters);

                        // Convert filter key to readable name:
                        $labels = [
                            'category_id' => 'Category',
                            'tipas'       => 'Type',
                            'min_price'   => 'Min Price',
                            'max_price'   => 'Max Price',
                            'q'           => 'Search',
                            'sort'        => 'Sort',
                            'city_id'     => 'City',
                        ];

                        if ($key === 'sort') {
                            $value = match ($value) {
                                'newest'     => 'Newest first',
                                'oldest'     => 'Oldest first',
                                'price_asc'  => 'Price: Low to High',
                                'price_desc' => 'Price: High to Low',
                                default      => $value,
                            };
                        }

                        $label = $labels[$key] ?? ucfirst($key);

                        // Convert filter values to readable options
                        if ($key === 'category_id') {
                            $value = \App\Models\Category::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'tipas') {
                            $value = $value === 'preke' ? 'Product' : 'Service';
                        }

                        if ($key === 'city_id') {
                            $value = \App\Models\City::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'sort') {
                            $value = match ($value) {
                                'newest'     => 'Newest first',
                                'oldest'     => 'Oldest first',
                                'price_asc'  => 'Price: Low to High',
                                'price_desc' => 'Price: High to Low',
                                default      => $value,
                            };
                        }
                    ?>

                    <a href="<?php echo e(route('search.listings')); ?>?<?php echo e($query); ?>"
                       class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center gap-2">
                        <span><?php echo e($label); ?>: <?php echo e($value); ?></span>
                        <span class="font-bold">✕</span>
                    </a>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <a href="<?php echo e(route('search.listings')); ?>"
                   class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold">
                    Clear all
                </a>

            </div>
        <?php endif; ?>

        <!-- Listings Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php $__empty_1 = true; $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <div class="relative">

                        <img
                            src="<?php echo e($item->ListingPhoto->first()?->failo_url ?? 'https://via.placeholder.com/300'); ?>"
                            class="w-full h-48 object-cover"
                        >

                        <!-- Favorite Button -->
                        <button
                            @click="Alpine.store('favorites').toggle(<?php echo e($item->id); ?>)"
                            class="absolute top-2 right-2"
                        >
                            <span
                                x-show="Alpine.store('favorites').list.includes(<?php echo e($item->id); ?>)"
                                class="text-red-500 text-2xl"
                            >
                                ♥️
                            </span>

                            <span
                                x-show="!Alpine.store('favorites').list.includes(<?php echo e($item->id); ?>)"
                                class="text-gray-200 drop-shadow-lg text-2xl"
                            >
                                🤍
                            </span>
                        </button>

                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-1">
                            <?php echo e($item['pavadinimas']); ?>

                        </h2>

                        <p class="text-gray-500 text-sm line-clamp-2">
                            <?php echo e($item['aprasymas']); ?>

                        </p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-lg">
                                <?php echo e($item['kaina']); ?> €
                            </span>

                            <a href="/listing/<?php echo e($item['id']); ?>"
                               class="text-blue-600 font-semibold">
                                More →
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-600 text-center w-full">No results found.</p>
            <?php endif; ?>

        </div>

    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/search-results.blade.php ENDPATH**/ ?>