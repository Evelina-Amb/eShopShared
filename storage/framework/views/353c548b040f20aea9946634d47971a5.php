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

<style>
/* Remove number input arrows (Chrome, Safari, Edge) */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Remove number input arrows (Firefox) */
input[type=number] {
    -moz-appearance: textfield;
}
</style>

<div class="max-w-6xl mx-auto py-10 px-4">

    
    <?php if(session('success')): ?>
        <div class="mb-6 px-4">
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="mb-6 px-4">
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
            <?php echo e(session('error')); ?>

        </div>
    </div>
<?php endif; ?>

    
    <div class="bg-white rounded-lg shadow p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            
            <div>
                <img 
                    id="mainImage"
                    src="<?php echo e($listing->ListingPhoto->first()->failo_url ?? 'https://via.placeholder.com/600x450?text=No+Image'); ?>"
                    class="rounded-lg shadow w-full max-h-[450px] object-cover mb-4"
                >

                <?php if($listing->ListingPhoto->count() > 1): ?>
                    <div class="flex gap-3">
                        <?php $__currentLoopData = $listing->ListingPhoto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <img 
                                src="<?php echo e($photo->failo_url); ?>"
                                class="w-20 h-20 rounded object-cover cursor-pointer border hover:ring-2 hover:ring-blue-400"
                                onclick="document.getElementById('mainImage').src=this.src"
                            >
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="flex flex-col">

                
                <div class="mb-3">
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm">
                        <?php echo e($listing->Category->pavadinimas ?? 'Kategorija'); ?>

                    </span>
                </div>

                
                <div class="flex items-center justify-between mb-4">

                    <h1 class="text-3xl font-bold text-gray-900">
                        <?php echo e($listing->pavadinimas); ?>

                    </h1>

                    <?php if(auth()->check() && auth()->id() !== $listing->user_id): ?>
                        <div x-data="{ favorites: Alpine.store('favorites').list }">
                            <button
                                @click="
                                    Alpine.store('favorites').toggle(<?php echo e($listing->id); ?>);
                                    favorites = Alpine.store('favorites').list;
                                "
                                class="text-3xl"
                            >
                                <span x-show="favorites.includes(<?php echo e($listing->id); ?>)" class="text-red-500">❤️</span>
                                <span x-show="!favorites.includes(<?php echo e($listing->id); ?>)" class="text-gray-300">🤍</span>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>

                
                <div class="text-gray-700 leading-relaxed mb-6 whitespace-pre-line">
                    <?php echo nl2br(e($listing->aprasymas)); ?>

                </div>

                
                <div class="text-2xl font-semibold text-gray-800 mb-2">
                    <?php echo e(number_format($listing->kaina, 2, ',', '.')); ?> €
                    <span class="text-gray-500 text-sm">
                        <?php if($listing->tipas === 'preke'): ?> / vnt <?php else: ?> / Service <?php endif; ?>
                    </span>
                </div>

                
                <div class="text-gray-700 mb-4">
                    <strong>Available:</strong> 
                    <span class="<?php echo e($listing->kiekis == 0 ? 'text-red-600 font-bold' : ''); ?>">
                        <?php echo e($listing->kiekis); ?>

                    </span>
                </div>

                
                <?php if($listing->is_renewable): ?>
                    <div class="mb-4">
                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded text-sm">
                            Renewable product – seller restocks this item
                        </span>
                    </div>
                <?php endif; ?>

                
                <?php if(auth()->check() && auth()->id() === $listing->user_id): ?>

                    <a href="<?php echo e(route('listing.edit', $listing->id)); ?>" 
                       class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center w-40">
                        Edit listing
                    </a>

                <?php else: ?>

                    <?php if($listing->tipas === 'paslauga'): ?>

                        <div class="mt-4 text-gray-700 font-semibold">
                            This is a service listing. Contact the seller to arrange details.
                        </div>

                    <?php else: ?>

                        <form method="POST" action="<?php echo e(route('cart.add', $listing->id)); ?>" class="flex items-center gap-4">
                            <?php echo csrf_field(); ?>

                            <div class="flex items-center border rounded">
                                <button type="button"
                                    onclick="let q=this.nextElementSibling; q.value = Math.max(1, (parseInt(q.value)||1)-1);"
                                    class="px-3 py-2 hover:bg-gray-100"
                                >-</button>

                                <input 
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    max="<?php echo e($listing->kiekis); ?>"
                                    class="w-16 text-center border-l border-r"
                                >

                                <button type="button"
                                    onclick="let q=this.previousElementSibling; let val=parseInt(q.value)||1; if(val < <?php echo e($listing->kiekis); ?>) q.value = val+1;"
                                    class="px-3 py-2 hover:bg-gray-100"
                                >+</button>
                            </div>

                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            >
                                Add to cart
                            </button>
                        </form>

                    <?php endif; ?>
                <?php endif; ?>

                
                <div class="mt-10 border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Seller</h3>

                    <div class="bg-gray-50 p-4 rounded border">
                        <div class="text-gray-900 font-semibold text-lg">
                            <?php echo e($listing->user->vardas); ?> <?php echo e($listing->user->pavarde); ?>

                        </div>

                        <?php if($listing->user->business_email): ?>
                            <div class="text-gray-600 text-sm mt-1">
                                Email: <?php echo e($listing->user->business_email); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($listing->user->telefonas): ?>
                            <div class="text-gray-700 text-sm mt-1">
                                Tel: <?php echo e($listing->user->telefonas); ?>

                            </div>
                        <?php endif; ?>

                        <?php if($listing->user->address?->city): ?>
                            <div class="text-gray-700 text-sm mt-1">
                                City: <?php echo e($listing->user->address->city->pavadinimas); ?>

                            </div>
                        <?php endif; ?>

                        <?php if(!$listing->user->business_email && !$listing->user->telefonas): ?>
                            <div class="text-red-600 text-sm mt-2">
                                No public contact information available.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
   
<div class="mt-10 border-t pt-6">

    <?php
        $user = auth()->user();
        $isOwner = $user && $user->id === $listing->user_id;

        // REVIEW ELIGIBILITY RULES
        $reviewsAllowed = $listing->is_renewable || $listing->kiekis >= 1;

        // SORT OPTION
        $sort = request('sort', 'newest');

        $sortedReviews = match($sort) {
            'oldest'  => $listing->review->sortBy('created_at'),
            'highest' => $listing->review->sortByDesc('ivertinimas'),
            'lowest'  => $listing->review->sortBy('ivertinimas'),
            default   => $listing->review->sortByDesc('created_at'),
        };

        // AVG + COUNT
        $avgRating = round($listing->review->avg('ivertinimas'), 1);
        $totalReviews = $listing->review->count();

        // USER'S OWN REVIEW
        $userReview = (!$isOwner && $user && $reviewsAllowed)
            ? $listing->review->where('user_id', $user->id)->first()
            : null;

        $otherReviews = $sortedReviews->filter(fn($r) => !$user || $r->user_id !== $user->id);
    ?>

        
    <h3 class="font-semibold text-gray-800 mb-4">Reviews</h3>

    
    <?php if(!$reviewsAllowed): ?>
        <p class="text-gray-600 italic">
            Reviews are only available for renewable items or non-renewable items with quantity ≥ 1.
        </p>
        <?php if($totalReviews > 0): ?>
            <p class="text-sm text-gray-500 mt-2">
                (Existing reviews below are visible but no new reviews can be posted.)
            </p>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if($totalReviews > 0): ?>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="text-3xl text-yellow-500">
                    <?php echo e(str_repeat('⭐', floor($avgRating))); ?>

                    <?php if($avgRating - floor($avgRating) >= 0.5): ?> ⭐ <?php endif; ?>
                </div>

                <div class="text-gray-700 text-lg">
                    <strong><?php echo e($avgRating); ?></strong> / 5
                    <span class="text-gray-500">(<?php echo e($totalReviews); ?> reviews)</span>
                </div>
            </div>

            
            <form method="GET">
                <select 
                    name="sort"
                    onchange="this.form.submit()"
                    class="border rounded px-2 py-1 text-sm"
                >
                    <option value="newest" <?php if($sort === 'newest'): echo 'selected'; endif; ?>>Newest</option>
                    <option value="oldest" <?php if($sort === 'oldest'): echo 'selected'; endif; ?>>Oldest</option>
                    <option value="highest" <?php if($sort === 'highest'): echo 'selected'; endif; ?>>Highest rated</option>
                    <option value="lowest" <?php if($sort === 'lowest'): echo 'selected'; endif; ?>>Lowest rated</option>
                </select>
            </form>
        </div>
    <?php endif; ?>

    
    <?php if($isOwner || !$reviewsAllowed): ?>

        <div class="border rounded p-4 bg-gray-50 space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $sortedReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white p-4 rounded border shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong><?php echo e($review->user->vardas); ?></strong>
                        <span class="text-yellow-500"><?php echo e(str_repeat('⭐', $review->ivertinimas)); ?></span>
                    </div>

                    <?php if($review->komentaras): ?>
                        <p class="text-gray-700 mt-2"><?php echo e($review->komentaras); ?></p>
                    <?php endif; ?>

                    <p class="text-gray-400 text-xs mt-1"><?php echo e($review->created_at->diffForHumans()); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-600 italic">No reviews yet.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        
        <div class="border rounded p-4 bg-gray-50 max-h-96 overflow-y-auto space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $otherReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white p-4 rounded border shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong><?php echo e($review->user->vardas); ?></strong>
                        <span class="text-yellow-500"><?php echo e(str_repeat('⭐', $review->ivertinimas)); ?></span>
                    </div>

                    <?php if($review->komentaras): ?>
                        <p class="text-gray-700 mt-2"><?php echo e($review->komentaras); ?></p>
                    <?php endif; ?>

                    <p class="text-gray-400 text-xs mt-1"><?php echo e($review->created_at->diffForHumans()); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-600 italic">No reviews yet.</p>
            <?php endif; ?>
        </div>

        
        <div>

            
            <?php if($userReview): ?>

                <h4 class="text-lg font-semibold mb-2">Your review</h4>

                <div class="bg-white border rounded p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong><?php echo e($userReview->user->vardas); ?></strong>
                        <span class="text-yellow-500"><?php echo e(str_repeat('⭐', $userReview->ivertinimas)); ?></span>
                    </div>

                    <?php if($userReview->komentaras): ?>
                        <p class="text-gray-700 mt-2"><?php echo e($userReview->komentaras); ?></p>
                    <?php endif; ?>

                    <p class="text-gray-400 text-xs mt-1"><?php echo e($userReview->created_at->diffForHumans()); ?></p>
                </div>

            
            <?php else: ?>

                <h4 class="text-lg font-semibold mb-2">Leave a review</h4>

                <form method="POST" action="<?php echo e(route('review.store', $listing->id)); ?>">
                    <?php echo csrf_field(); ?>

                    <label class="block mb-2">
                        Rating:
                        <select name="ivertinimas" class="border rounded w-16 h-9 text-center">
                            <?php $__currentLoopData = [1,2,3,4,5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($n); ?>"><?php echo e($n); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>

                    <textarea
                        name="komentaras"
                        rows="4"
                        class="w-full border rounded p-2"
                        placeholder="Write a review..."
                    ></textarea>

                    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Submit Review
                    </button>
                </form>

            <?php endif; ?>

        </div>

    </div>
    <?php endif; ?>

</div>

    
    <?php if($similar->count() > 0): ?>
        <div class="mt-14">
            <h2 class="text-2xl font-bold mb-6">Other products from this seller</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php $__currentLoopData = $similar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($s->id !== $listing->id): ?>
                        <a href="<?php echo e(route('listing.single', $s->id)); ?>" 
                           class="bg-white shadow rounded overflow-hidden hover:shadow-md transition">
                            <img src="<?php echo e($s->ListingPhoto->first()->failo_url ?? 'https://via.placeholder.com/300'); ?>"
                                 class="w-full h-40 object-cover">
                            <div class="p-4">
                                <div class="font-semibold mb-1"><?php echo e($s->pavadinimas); ?></div>
                                <div class="text-green-700 font-semibold">
                                    <?php echo e(number_format($s->kaina, 2, ',', '.')); ?> €
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/listing-single.blade.php ENDPATH**/ ?>