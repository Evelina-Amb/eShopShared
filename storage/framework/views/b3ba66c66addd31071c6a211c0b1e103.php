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

<div class="max-w-3xl mx-auto bg-white shadow p-6 rounded mt-10"
     x-data="{ type: 'preke' }">

    <h1 class="text-3xl font-bold mb-6">Create New Listing</h1>

    
    <?php if($errors->any()): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <ul class="list-disc ml-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('listing.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        
        <div class="mb-4">
            <label class="block font-semibold">Title</label>
            <input type="text" name="pavadinimas"
                   class="w-full border rounded p-2"
                   required>
        </div>

        
        <div class="mb-4">
            <label class="block font-semibold">Description</label>
            <textarea name="aprasymas" rows="5"
                      class="w-full border rounded p-2"
                      required></textarea>
        </div>

        
        <div class="mb-4">
            <label class="block font-semibold">Price (€)</label>
            <input type="number" step="0.01" name="kaina"
                   class="w-full border rounded p-2"
                   required>
        </div>

        
        <div class="mb-4">
            <label class="block font-semibold">Listing Type</label>
            <select name="tipas"
                    x-model="type"
                    class="w-full border rounded p-2"
                    required>
                <option value="preke">Product</option>
                <option value="paslauga">Service</option>
            </select>
        </div>

        
        <div class="mb-4">
            <label class="block font-semibold">Category</label>
            <select name="category_id" class="w-full border rounded p-2" required>
                <?php $__currentLoopData = \App\Models\Category::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->pavadinimas); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div x-show="type === 'preke'" x-transition>
            
            <div class="mb-4">
                <label class="font-semibold">Available Quantity</label>
                <input type="number"
                       name="kiekis"
                       value="1"
                       min="1"
                       class="w-full border p-2 rounded"
                       required>
            </div>

            
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" name="is_renewable" value="1">
                <label>Is this a renewable product (can be restocked)?</label>
            </div>
        </div>

        
        <div class="mb-6">
            <label class="block font-semibold">Photos</label>

            <input 
                type="file" 
                name="photos[]" 
                id="photoInput"
                multiple
                required
                class="w-full border rounded p-2"
            >

            <small class="text-gray-600">Upload at least one photo.</small>

            <div id="previewContainer" 
                 class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
        </div>

        
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
            Publish Listing
        </button>

    </form>
</div>


<script>
document.getElementById('photoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('previewContainer');
    preview.innerHTML = "";

    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(event) {
            const wrapper = document.createElement('div');
            wrapper.classList.add("relative", "border", "rounded", "overflow-hidden");

            wrapper.innerHTML = `
                <img src="${event.target.result}" class="w-full h-32 object-cover">
                <button 
                    type="button" 
                    class="absolute top-2 right-2 bg-red-600 text-white text-sm px-2 py-1 rounded"
                    onclick="removeSelectedFile(${index})"
                >
                    X
                </button>
            `;

            preview.appendChild(wrapper);
        };

        reader.readAsDataURL(file);
    });
});

function removeSelectedFile(index) {
    let input = document.getElementById('photoInput');
    let files = Array.from(input.files);

    files.splice(index, 1);

    let dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));

    input.files = dataTransfer.files;

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
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/listing-create.blade.php ENDPATH**/ ?>