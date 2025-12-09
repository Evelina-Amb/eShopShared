<x-app-layout>

    <div class="max-w-4xl mx-auto mt-10 bg-white shadow p-6 rounded">

        <h1 class="text-2xl font-bold mb-6">Edit Listing</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('listing.update', $listing->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- TITLE --}}
            <div class="mb-4">
                <label class="font-semibold">Title</label>
                <input 
                    type="text" 
                    name="pavadinimas"
                    value="{{ old('pavadinimas', $listing->pavadinimas) }}"
                    class="w-full border rounded px-3 py-2"
                    required>
            </div>

            {{-- DESCRIPTION --}}
            <div class="mb-4">
                <label class="font-semibold">Description</label>
                <textarea 
                    name="aprasymas" 
                    rows="5"
                    class="w-full border rounded px-3 py-2"
                    required>{{ old('aprasymas', $listing->aprasymas) }}</textarea>
            </div>

            {{-- PRICE --}}
            <div class="mb-4">
                <label class="font-semibold">Price (€)</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="kaina"
                    value="{{ old('kaina', $listing->kaina) }}"
                    class="w-full border rounded px-3 py-2"
                    required>
            </div>

            {{-- TYPE --}}
            <div class="mb-4">
                <label class="font-semibold block">Type</label>
                <select name="tipas" class="w-full border rounded px-3 py-2" required>
                    <option value="preke" @selected($listing->tipas === 'preke')>Product</option>
                    <option value="paslauga" @selected($listing->tipas === 'paslauga')>Service</option>
                </select>
            </div>

            {{-- CATEGORY --}}
            <div class="mb-4">
                <label class="font-semibold">Category</label>
                <select name="category_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($categories as $cat)
                        <option 
                            value="{{ $cat->id }}" 
                            @selected(old('category_id', $listing->category_id) == $cat->id)>
                            {{ $cat->pavadinimas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- QUANTITY --}}
            <div class="mb-4">
                <label class="font-semibold">Available Quantity</label>
                <input 
                    type="number" 
                    min="1"
                    name="kiekis"
                    value="{{ old('kiekis', $listing->kiekis) }}"
                    class="w-full border rounded px-3 py-2"
                    required>
            </div>

            {{-- RENEWABLE --}}
            <div class="mb-4 flex items-center gap-2">
                <input 
                    type="checkbox" 
                    name="is_renewable"
                    value="1"
                    @checked($listing->is_renewable == 1)>
                <label>Is this product renewable (can be restocked)?</label>
            </div>

            {{-- NEW PHOTO UPLOAD + PREVIEW --}}
            <div class="mb-6">
                <label class="font-semibold">Add New Photos</label>

                <input 
                    type="file" 
                    name="photos[]" 
                    id="photoInput"
                    multiple 
                    class="w-full border rounded px-3 py-2">

                <p class="text-gray-500 text-sm">Selected images will appear below.</p>

                <div id="previewContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
            </div>

            {{-- SAVE BUTTON --}}
            <button 
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-800"
                type="submit">
                Save Changes
            </button>

        </form>

        {{-- EXISTING PHOTOS (OUTSIDE FORM) --}}
        <div class="mt-10">
            <label class="font-semibold text-lg">Existing Photos</label>

            @if($listing->ListingPhoto->isEmpty())
                <p class="text-gray-500 mt-2">No photos uploaded yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                    @foreach($listing->ListingPhoto as $photo)
                        <div class="relative border rounded overflow-hidden">

                            <img src="{{ $photo->failo_url }}" class="w-full h-48 object-cover">

                            {{-- DELETE BUTTON --}}
                            <form 
                                action="{{ route('listing.photo.delete', [$listing->id, $photo->id]) }}" 
                                method="POST"
                                class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')

                                <button 
                                    type="submit"
                                    class="bg-red-600 text-white text-sm px-3 py-1 rounded shadow hover:bg-red-700">
                                    Delete
                                </button>
                            </form>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- JS PREVIEW --}}
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
                            onclick="removeSelectedFile(${index})">
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

</x-app-layout>
