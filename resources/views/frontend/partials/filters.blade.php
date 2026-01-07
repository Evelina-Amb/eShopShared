<form method="GET" action="{{ route('search.listings') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">

    <input type="hidden" name="q" value="{{ request('q') }}">

    <!-- Category -->
    <select name="category_id" class="border rounded px-3 py-2">
        <option value="">Kategorija</option>
        @foreach(\App\Models\Category::all() as $cat)
            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                {{ $cat->pavadinimas }}
            </option>
        @endforeach
    </select>

    <!-- Type -->
    <select name="tipas" class="border rounded px-3 py-2">
        <option value="">Tipas</option>
        <option value="preke" @selected(request('tipas') == 'preke')>Prekė</option>
        <option value="paslauga" @selected(request('tipas') == 'paslauga')>Paslauga</option>
    </select>

    <!-- Min Price -->
    <input 
        type="number" 
        name="min_price" 
        class="border rounded px-3 py-2"
        placeholder="Min. kaina"
        value="{{ request('min_price') }}"
        min="0"
    >

    <!-- Max Price -->
    <input 
        type="number" 
        name="max_price" 
        class="border rounded px-3 py-2"
        placeholder="Maks. kaina"
        value="{{ request('max_price') }}"
        min="0"
    >

    <!-- City  -->
    <select name="city_id" class="border rounded px-3 py-2">
        <option value="">Miestas</option>
        @foreach(\App\Models\City::orderBy('pavadinimas')->get() as $city)
            <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>
                {{ $city->pavadinimas }}
            </option>
        @endforeach
    </select>

    <!-- Submit -->
    <button class="bg-blue-600 text-white px-4 py-2 rounded col-span-full w-32">
        Taikyti
    </button>

</form>
