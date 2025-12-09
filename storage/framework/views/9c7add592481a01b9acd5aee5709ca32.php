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

    <div class="container mx-auto px-4 mt-8">
        <!-- Listing Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php $__empty_1 = true; $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">
                    <div class="relative">

                        <img src="<?php echo e($item->ListingPhoto->first()?->failo_url ?? 'https://via.placeholder.com/300'); ?>" class="w-full h-48 object-cover">
                        <button
                            @click="Alpine.store('favorites').toggle(<?php echo e($item->id); ?>)"
                            class="absolute top-2 right-2">
                            <span x-show="Alpine.store('favorites').list.includes(<?php echo e($item->id); ?>)"
                                class="text-red-500 text-2xl">♥️</span>

                            <span x-show="!Alpine.store('favorites').list.includes(<?php echo e($item->id); ?>)" class="text-gray-200 drop-shadow-lg text-[30px] leading-none">🤍</span>
                        </button>
                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-1">
                            <?php echo e($item->pavadinimas); ?>

                        </h2>

                        <p class="text-gray-500 text-sm line-clamp-2">
                            <?php echo e($item->aprasymas); ?>

                        </p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-lg">
                                <?php echo e($item->kaina); ?> €
                            </span>

                            <a href="/listing/<?php echo e($item->id); ?>" class="text-blue-600 font-semibold">More →</a>
                        </div>
                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-600 text-center">No listings found</p>
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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/home.blade.php ENDPATH**/ ?>