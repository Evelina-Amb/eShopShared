<form method="GET" action="<?php echo e(route('search.listings')); ?>" class="grid grid-cols-1 sm:grid-cols-5 gap-4">

    <input type="hidden" name="q" value="<?php echo e(request('q')); ?>">

    <!-- Category -->
    <select name="category_id" class="border rounded px-3 py-2">
        <option value="">Category</option>
        <?php $__currentLoopData = \App\Models\Category::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat->id); ?>" <?php if(request('category_id') == $cat->id): echo 'selected'; endif; ?>>
                <?php echo e($cat->pavadinimas); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <!-- Type -->
    <select name="tipas" class="border rounded px-3 py-2">
        <option value="">Type</option>
        <option value="preke" <?php if(request('tipas') == 'preke'): echo 'selected'; endif; ?>>Product</option>
        <option value="paslauga" <?php if(request('tipas') == 'paslauga'): echo 'selected'; endif; ?>>Service</option>
    </select>

    <!-- Min Price -->
    <input 
        type="number" 
        name="min_price" 
        class="border rounded px-3 py-2"
        placeholder="Min price"
        value="<?php echo e(request('min_price')); ?>"
    >

    <!-- Max Price -->
    <input 
        type="number" 
        name="max_price" 
        class="border rounded px-3 py-2"
        placeholder="Max price"
        value="<?php echo e(request('max_price')); ?>"
    >

    <!-- City  -->
    <select name="city_id" class="border rounded px-3 py-2">
        <option value="">City</option>
        <?php $__currentLoopData = \App\Models\City::orderBy('pavadinimas')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($city->id); ?>" <?php if(request('city_id') == $city->id): echo 'selected'; endif; ?>>
                <?php echo e($city->pavadinimas); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <!-- Submit -->
    <button class="bg-blue-600 text-white px-4 py-2 rounded col-span-full w-32">
        Apply
    </button>

</form>
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/partials/filters.blade.php ENDPATH**/ ?>