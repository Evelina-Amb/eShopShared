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

<div class="max-w-4xl mx-auto mt-10">

    <h1 class="text-3xl font-bold mb-6">My Cart</h1>

    <?php if($cartItems->isEmpty()): ?>
        <div class="bg-white shadow p-6 rounded text-center">
            <p class="text-gray-600">Your cart is empty.</p>
        </div>

    <?php else: ?>

        
        <form action="<?php echo e(route('cart.clear')); ?>" method="POST"
              onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>

            <button class="mb-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Clear Cart
            </button>
        </form>


<div class="bg-white shadow rounded p-4">

    
    <div class="grid grid-cols-12 font-semibold text-gray-600 border-b pb-2 mb-4">
        <div class="col-span-6">Item</div>
        <div class="col-span-2 text-right">Price</div>
        <div class="col-span-2 text-center">Quantity</div>
    </div>

    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-12 items-center border-b py-4">

            
            <div class="col-span-6 flex items-center gap-4">
                <img 
                    src="<?php echo e($item->Listing->ListingPhoto->first()->failo_url ?? asset('no-image.png')); ?>"
                    class="w-20 h-20 object-cover rounded">
                <a href="<?php echo e(route('listing.single', $item->listing_id)); ?>"
                   class="font-semibold text-blue-600 hover:underline">
                    <?php echo e($item->Listing->pavadinimas); ?>

                </a>
            </div>

            
            <div class="col-span-2 text-right font-semibold">
                <?php echo e(number_format($item->Listing->kaina, 2)); ?> €
            </div>

            
            <div class="col-span-2 flex justify-center items-center">

                <form method="POST" action="<?php echo e(route('cart.decrease', $item->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="px-2 py-1 bg-gray-200 rounded">−</button>
                </form>

                <span class="px-4"><?php echo e($item->kiekis); ?></span>

                <form method="POST" action="<?php echo e(route('cart.increase', $item->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="px-2 py-1 bg-gray-200 rounded">+</button>
                </form>

            </div>
                
            
                <form method="POST" action="<?php echo e(route('cart.remove', $item->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="text-red-600 text-xl hover:text-red-800">Remove</button>
                </form>
            </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
        
        <div class="bg-white shadow rounded p-6 mt-6">
            <?php
                $total = $cartItems->sum(fn($i) => $i->Listing->kaina * $i->kiekis);
            ?>

            <div class="text-xl font-bold mb-4">
                Total: <?php echo e(number_format($total, 2)); ?> €
            </div>
 
            
            <form method="POST" action="<?php echo e(route('cart.checkout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 w-full">
                    Continue to Checkout
                </button>
            </form>
        </div>
    <?php endif; ?>
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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/cart.blade.php ENDPATH**/ ?>