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

<div class="max-w-4xl mx-auto mt-10 bg-white shadow p-6 rounded">

    <h1 class="text-2xl font-bold mb-6">Edit Listing</h1>

    <?php if(session('success')): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc ml-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form method="POST" action="<?php echo e(route('listing.update', $listing->id)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="mb-4">
            <label class="font-semibold">Title</label>
            <input 
                type="text" 
                name="pavadinimas"
                value="<?php echo e(old('pavadinimas', $listing->pavadinimas)); ?>"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        
        <div class="mb-4">
            <label class="font-semibold">Description</label>
            <textarea 
                name="aprasymas" 
                rows="5"
                class="w-full border rounded px-3 py-2"
                required
            ><?php echo e(old('aprasymas', $listing->aprasymas)); ?></textarea>
        </div>

        
        <div class="mb-4">
            <label class="font-semibold">Price (€)</label>
            <input 
                type="number" 
                step="0.01" 
                name="kaina"
                value="<?php echo e(old('kaina', $listing->kaina)); ?>"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        
        <div class="mb-4">
            <label class="font-semibold block">Type</label>
            <select name="tipas" class="w-full border rounded px-3 py-2" required>
                <option value="preke" <?php if($listing->tipas === 'preke'): echo 'selected'; endif; ?>>Product</option>
                <option value="paslauga" <?php if($listing->tipas === 'paslauga'): echo 'selected'; endif; ?>>Service</option>
            </select>
        </div>

        
        <div class="mb-4">
            <label class="font-semibold">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option 
                        value="<?php echo e($cat->id); ?>" 
                        <?php if(old('category_id', $listing->category_id) == $cat->id): echo 'selected'; endif; ?>
                    >
                        <?php echo e($cat->pavadinimas); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="mb-4">
            <label class="font-semibold">Available Quantity</label>
            <input 
                type="number" 
                min="1"
                name="kiekis"
                value="<?php echo e(old('kiekis', $listing->kiekis)); ?>"
                class="w-full border rounded px-3 py-2"
                required
            >
        </div>

        
        <div class="mb-4 flex items-center gap-2">
            <input 
                type="checkbox" 
                name="is_renewable"
                value="1"
                <?php if($listing->is_renewable == 1): echo 'checked'; endif; ?>
            >
            <label>Is this product renewable (can be restocked)?</label>
        </div>

        
        <div class="mb-6">
            <label class="font-semibold">Add New Photos</label>

            <input 
                type="file" 
                name="photos[]" 
                id="photoInput"
                multiple 
                class="w-full border rounded px-3 py-2"
            >

            <p class="text-gray-500 text-sm">Selected images will appear below.</p>

            <div id="previewContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
        </div>

        
        <button 
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-800"
            type="submit"
        >
            Save Changes
        </button>

    </form>
    

    
    <div class="mt-10">
        <label class="font-semibold text-lg">Existing Photos</label>

        <?php if($listing->ListingPhoto->isEmpty()): ?>
            <p class="text-gray-500 mt-2">No photos uploaded yet.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">

                <?php $__currentLoopData = $listing->ListingPhoto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative border rounded overflow-hidden">

                        <img 
                            src="<?php echo e($photo->failo_url); ?>" 
                            class="w-full h-48 object-cover"
                        >

                        
                        <form 
                            action="<?php echo e(route('listing.photo.delete', [$listing->id, $photo->id])); ?>" 
                            method="POST"
                            class="absolute top-2 right-2"
                        >
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>

                            <button 
                                type="submit"
                                class="bg-red-600 text-white text-sm px-3 py-1 rounded shadow hover:bg-red-700"
                            >
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        <?php endif; ?>
    </div>

</div>


<script>
document.getElementById('photoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('previewContainer');
    preview.innerHTML = "";

    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(event) {
            const div = document.createElement('div');
            div.classList.add("relative", "border", "rounded", "overflow-hidden");

            div.innerHTML = `
                <img src="${event.target.result}" class="w-full h-32 object-cover">
                <button 
                    type="button" 
                    class="absolute top-2 right-2 bg-red-600 text-white text-sm px-2 py-1 rounded"
                    onclick="removeSelectedFile(${index})"
                >
                    X
                </button>
            `;

            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    });
});

function removeSelectedFile(index) {
    let input = document.getElementById('photoInput');
    let files = Array.from(input.files);

    files.splice(index, 1);

    let dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));

    input.files = dt.files;

    input.dispatchEvent(new Event('change'));
}
</script>

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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/listing-edit.blade.php ENDPATH**/ ?>