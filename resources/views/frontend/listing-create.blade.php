<x-app-layout>

<div class="max-w-3xl mx-auto bg-white shadow p-6 rounded mt-10"
     x-data="{ type: 'preke' }">

    <h1 class="text-3xl font-bold mb-6">Sukurti naują skelbimą</h1>

    {{-- ERROR DISPLAY --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('listing.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- TITLE --}}
        <div class="mb-4">
            <label class="block font-semibold">Pavadinimas</label>
            <input
                type="text"
                name="pavadinimas"
                class="w-full border rounded p-2"
                required
            >
        </div>

        {{-- DESCRIPTION --}}
        <div class="mb-4">
            <label class="block font-semibold">Aprašymas</label>
            <textarea
                name="aprasymas"
                rows="5"
                class="w-full border rounded p-2"
                required
            ></textarea>
        </div>

        {{-- PRICE --}}
        <div class="mb-4">
            <label class="block font-semibold">Kaina (€)</label>
            <input
                type="number"
                 min="0"
                step="0.01"
                name="kaina"
                class="w-full border rounded p-2"
                required
            >
        </div>

        {{-- TYPE --}}
        <div class="mb-4">
            <label class="block font-semibold">Skelbimo tipas</label>
            <select
                name="tipas"
                x-model="type"
                class="w-full border rounded p-2"
                required
            >
                <option value="preke">Prekė</option>
                <option value="paslauga">Paslauga</option>
            </select>
        </div>

        {{-- CATEGORY --}}
        <div class="mb-4">
            <label class="block font-semibold">Kategorija</label>
            <select
                name="category_id"
                class="w-full border rounded p-2"
                required
            >
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->pavadinimas }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- PRODUCT ONLY FIELDS --}}
        <div x-show="type === 'preke'" x-transition>

               {{-- PACKAGE SIZE --}}
            <div class="mb-4">
    <label class="font-semibold">Pakuotės dydis</label>
  <select
    name="package_size"
    class="w-full border p-2 rounded"
    x-bind:required="type === 'preke'"
    x-bind:disabled="type !== 'preke'"
>   
        <option value="">Pasirinkite dydį</option>
        <option value="XS">XS – Vokas</option>
        <option value="S">S – Maža dėžė</option>
        <option value="M">M – Vidutinė dėžė</option>
        <option value="L">L – Didelė dėžė</option>
    </select>
</div>
             
            {{-- QUANTITY --}}
            <div class="mb-4">
                <label class="font-semibold">Galimas kiekis</label>
                <input
                    type="number"
                    name="kiekis"
                    value="1"
                    min="1"
                    class="w-full border p-2 rounded"
                    required
                >
            </div>

            {{-- RENEWABLE --}}
            <div class="mb-4 flex items-center gap-2">
                <input type="checkbox" name="is_renewable" value="1">
                <label>Ar tai atnaujinamas produktas (galima papildyti atsargas)?</label>
            </div>
        </div>

        {{-- PHOTOS WITH LIVE PREVIEW --}}
        <div class="mb-6">
            <label class="block font-semibold">Nuotraukos</label>

            <input 
                type="file"
                name="photos[]"
                id="photoInput"
                multiple
                required
                class="w-full border rounded p-2"
            >

            <small class="text-gray-600">Įkelkite bent vieną nuotrauką.</small>

            <div
                id="previewContainer"
                class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"
            ></div>
        </div>
<div class="flex gap-4 mt-6">         
        {{-- SUBMIT --}}
        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition"
        >
            Paskelbti skelbimą
        </button>
 <a 
        href="{{ route('my.listings') }}"
        class="bg-gray-300 text-gray-800 px-6 py-3 rounded hover:bg-gray-400">
        Atšaukti
    </a>
   </div>
    </form>
</div>

{{-- LIVE PREVIEW --}}
<script>
document.getElementById('photoInput').addEventListener('change', function (e) {
    const preview = document.getElementById('previewContainer');
    preview.innerHTML = "";

    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function (event) {
            const wrapper = document.createElement('div');
            wrapper.classList.add(
                "relative", "border", "rounded", "overflow-hidden"
            );

            wrapper.innerHTML = `
                <img src="${event.target.result}" class="w-full h-32 object-cover">
                <button
                    type="button"
                    class="absolute top-2 right-2 bg-red-600 text-white text-sm px-2 py-1 rounded"
                    onclick="removeSelectedFile(${index})"
                >X</button>
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

    const dataTransfer = new DataTransfer();
    files.forEach(file => dataTransfer.items.add(file));

    input.files = dataTransfer.files;

    input.dispatchEvent(new Event('change'));
}
</script>

</x-app-layout>
