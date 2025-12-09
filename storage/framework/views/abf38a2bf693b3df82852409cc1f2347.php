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

    <h1 class="text-3xl font-bold mb-6">My Listings</h1>

    <?php if($listings->isEmpty()): ?>
        <p class="text-gray-600">You haven't posted any listings yet.</p>
    <?php endif; ?>

    <div 
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        x-data="{
            listings: <?php echo e($listings->toJson()); ?>,

            deleteListing(id) {
                if (!confirm('Are you sure you want to delete this listing?')) return;

                fetch('/api/listing/' + id, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(() => {
                    this.listings = this.listings.filter(l => l.id !== id);
                });
            }
        }"
    >

        <template x-for="item in listings" :key="item.id">

    <div class="bg-white shadow rounded overflow-hidden">

        <!-- IMAGE -->
        <img 
            :src="item.listing_photo?.[0]?.failo_url || 'https://via.placeholder.com/300'"
            class="w-full h-48 object-cover"
        />

        <div class="p-4">

            <!-- TITLE -->
            <h2 class="text-lg font-semibold mb-2" x-text="item.pavadinimas"></h2>

            <!-- DESCRIPTION -->
            <p class="text-gray-500 text-sm line-clamp-2" x-text="item.aprasymas"></p>

            <!-- PRICE -->
            <div class="flex justify-between items-center mt-3">
                <span class="text-green-600 font-bold text-lg" x-text="item.kaina + ' €'"></span>
            </div>

            <!-- QUANTITY + RENEWABLE STATUS -->
            <div class="mt-2 text-sm">
                <template x-if="item.tipas === 'preke'">
                    <div>
                        <strong>Stock:</strong>
                        <span 
                            :class="item.kiekis == 0 ? 'text-red-600 font-bold' : ''"
                            x-text="item.kiekis"
                        ></span>

                        <template x-if="item.is_renewable == 1">
                            <span class="text-green-600 ml-1">(renewable)</span>
                        </template>
                    </div>
                </template>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex justify-between items-center mt-4">

                <a 
                    :href="'/listing/' + item.id + '/edit'" 
                    class="text-blue-600 font-semibold hover:underline"
                >
                    Edit
                </a>

                <button 
                    @click="deleteListing(item.id)"
                    class="text-red-600 font-semibold hover:underline"
                >
                    Delete
                </button>

            </div>

        </div>

    </div>

</template>


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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/my-listings.blade.php ENDPATH**/ ?>