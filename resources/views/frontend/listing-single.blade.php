<x-app-layout>

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

<div class="max-w-6xl mx-auto py-6 sm:py-10 px-3 sm:px-4">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 px-0 sm:px-4">
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-0 sm:px-4">
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- LISTING CARD --}}
    <div class="bg-white rounded-lg shadow p-4 sm:p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-10">

            {{-- LEFT: IMAGE GALLERY --}}
            <div>
                <img
                    id="mainImage"
                    src="{{ $listing->photos->isNotEmpty()
                        ? asset('storage/' . $listing->photos->first()->failo_url)
                        : 'https://via.placeholder.com/600x450?text=No+Image'
                    }}"
                    class="rounded-lg shadow w-full max-h-[320px] sm:max-h-[450px] object-cover mb-4"
                />

                @if($listing->photos->count() > 1)
                    <div class="flex gap-2 sm:gap-3 overflow-x-auto">
                        @foreach($listing->photos as $photo)
                            <img
                                src="{{ asset('storage/' . $photo->failo_url) }}"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded object-cover cursor-pointer border hover:ring-2 hover:ring-blue-400"
                                onclick="document.getElementById('mainImage').src=this.src"
                            >
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="flex flex-col">

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm">
                        {{ $listing->Category->pavadinimas ?? 'Kategorija' }}
                    </span>
                </div>

                {{-- TITLE + FAVORITE BUTTON --}}
                <div class="flex items-start justify-between mb-4 gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">
                        {{ $listing->pavadinimas }}
                    </h1>

                    @if(auth()->check() && auth()->id() !== $listing->user_id)
                        <button type="button"
                            @click.prevent="toggle({{ $listing->id }})"
                            class="text-3xl shrink-0">
                            <span x-show="isFavorite({{ $listing->id }})" class="text-red-500">❤️</span>
                            <span x-show="!isFavorite({{ $listing->id }})" class="text-gray-300">🤍</span>
                        </button>
                    @endif
                </div>

                {{-- DESCRIPTION --}}
                <div class="text-gray-700 leading-relaxed mb-6 whitespace-pre-line text-sm sm:text-base">
                    {!! nl2br(e($listing->aprasymas)) !!}
                </div>

                {{-- PRICE --}}
                <div class="text-xl sm:text-2xl font-semibold text-gray-800 mb-2">
                    {{ number_format($listing->kaina, 2, ',', '.') }} €
                    <span class="text-gray-500 text-sm">
                        @if($listing->tipas === 'preke') / vnt @else / paslauga @endif
                    </span>
                </div>

                {{-- AVAILABLE --}}
               @if($listing->tipas === 'preke')
                <div class="text-gray-700 mb-4">
                    <strong>Prieinama: </strong>
                    <span class="{{ $listing->kiekis == 0 ? 'text-red-600 font-bold' : '' }}">
                    {{ $listing->kiekis }}
                        </span>
                </div>
            @endif

                {{-- RENEWABLE BADGE --}}
                @if($listing->is_renewable)
                    <div class="mb-4">
                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded text-sm">
                            Atsinaujinanti prekė – pardavėjas papildo atsargas
                        </span>
                    </div>
                @endif

                {{-- CART OR EDIT --}}
                @if(auth()->check() && auth()->id() === $listing->user_id)

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-4">
                        <a href="{{ route('listing.edit', $listing->id) }}"
                           class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center w-full sm:w-40 whitespace-nowrap">
                            Redaguoti
                        </a>

                        <form method="POST"
                              action="{{ route('listing.destroy', $listing->id) }}"
                              onsubmit="return confirm('Ar tikrai norite ištrinti šį skelbimą? Šio veiksmo atšaukti negalėsite.');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700 transition text-center w-full sm:w-40">
                                Ištrinti skelbimą
                            </button>
                        </form>
                    </div>

                @elseif($listing->tipas === 'paslauga')

                    <div class="mt-4 text-gray-700 font-semibold">
                        Tai paslaugos skelbimas. Susisiekite su pardavėju dėl detalių.
                    </div>

                @else
                    {{-- ADD TO CART --}}
                    <form method="POST" action="{{ route('cart.add', $listing->id) }}"
                          class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        @csrf

                        {{-- FIX: square quantity buttons --}}
                        <div class="flex items-center gap-1">
                            <button type="button"
                                onclick="let q=this.nextElementSibling; q.value = Math.max(1, (parseInt(q.value)||1)-1);"
                                class="w-10 h-10 border rounded hover:bg-gray-100 flex items-center justify-center">
                                −
                            </button>

                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                max="{{ $listing->kiekis }}"
                                class="w-12 h-10 text-center border rounded"
                            >

                            <button type="button"
                                onclick="let q=this.previousElementSibling; let val=parseInt(q.value)||1; if(val < {{ $listing->kiekis }}) q.value = val+1;"
                                class="w-10 h-10 border rounded hover:bg-gray-100 flex items-center justify-center">
                                +
                            </button>
                        </div>

                        <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition w-full sm:w-auto">
                           Pridėti į krepšelį
                        </button>
                    </form>
                @endif

                {{-- SELLER INFO --}}
                <div class="mt-8 sm:mt-10 border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Pardavėjas</h3>
                    <div class="bg-gray-50 p-4 rounded border text-sm">
                        <div class="text-gray-900 font-semibold text-base sm:text-lg">
                            {{ $listing->user->vardas }} {{ $listing->user->pavarde }}
                        </div>
                        @if($listing->user->business_email)
                            <div class="text-gray-600 mt-1">
                               El. paštas: {{ $listing->user->business_email }}
                            </div>
                        @endif
                        @if($listing->user->telefonas)
                            <div class="text-gray-700 mt-1">
                                Tel.: {{ $listing->user->telefonas }}
                            </div>
                        @endif
                        @if($listing->user->address?->city)
                            <div class="text-gray-700 mt-1">
                                Miestas: {{ $listing->user->address->city->pavadinimas }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

{{-- OTHER PRODUCTS --}}
@if($similar->count() > 0)
<section class="mt-14 sm:mt-20">
    <h2 class="text-xl sm:text-2xl font-bold mb-6">Kiti šio pardavėjo produktai</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($similar as $s)
            @if($s->id !== $listing->id)
                <a href="{{ route('listing.single', $s->id) }}"
                   class="bg-white shadow rounded overflow-hidden">
                    <img
                        src="{{ $s->photos->isNotEmpty()
                            ? asset('storage/' . $s->photos->first()->failo_url)
                            : 'https://via.placeholder.com/300'
                        }}"
                        class="w-full h-40 object-cover"
                    >
                    <div class="p-4">
                        <div class="font-semibold">{{ $s->pavadinimas }}</div>
                        <div class="text-green-700 font-semibold">
                            {{ number_format($s->kaina, 2, ',', '.') }} €
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</section>
@endif

{{-- REVIEWS SECTION --}}
<section class="mt-12 sm:mt-16">

    @php
        $user = auth()->user();
        $isOwner = $user && $user->id === $listing->user_id;
        $reviewsAllowed = $listing->is_renewable || $listing->kiekis >= 1;
        $sort = request('sort', 'newest');

        $sortedReviews = match($sort) {
            'oldest'  => $listing->review->sortBy('created_at'),
            'highest' => $listing->review->sortByDesc('ivertinimas'),
            'lowest'  => $listing->review->sortBy('ivertinimas'),
            default   => $listing->review->sortByDesc('created_at'),
        };

        $avgRating = round($listing->review->avg('ivertinimas'), 1);
        $totalReviews = $listing->review->count();

        $otherReviews = $sortedReviews->filter(fn($r) => !$user || $r->user_id !== $user->id);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 items-start">

        <div>
            <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Atsiliepimai</h3>

            @if($totalReviews > 0)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="text-2xl sm:text-3xl text-yellow-500">
                            {{ str_repeat('⭐', floor($avgRating)) }}
                        </div>
                        <div>
                            <strong>{{ $avgRating }}</strong> / 5
                            <span class="text-gray-500 text-sm">
                                ({{ $totalReviews }} atsiliepimai)
                            </span>
                        </div>
                    </div>

                    <form method="GET" class="w-full sm:w-48">
                        <select
                            name="sort"
                            onchange="this.form.submit()"
                            class="border rounded px-3 py-2 w-full"
                        >
                            <option value="newest">Naujausi</option>
                            <option value="oldest">Seniausi</option>
                            <option value="highest">Geriausi</option>
                            <option value="lowest">Blogiausi</option>
                        </select>
                    </form>
                </div>
            @endif

            {{-- LEFT: REVIEWS --}}
            <div class="space-y-4">
                @forelse($otherReviews as $review)
                    <div class="bg-white p-4 rounded border">
                        <div class="flex items-center gap-2 mb-1">
                            <strong>{{ $review->user->vardas }}</strong>
                            <span class="text-yellow-500 text-sm">
                                {{ str_repeat('⭐', $review->ivertinimas) }}
                            </span>
                        </div>
                        <p class="text-gray-700 text-sm sm:text-base">
                            {{ $review->komentaras }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Atsiliepimų dar nėra.</p>
                @endforelse
            </div>
        </div>

        {{-- RIGHT: REVIEW FORM --}}
        @if(!$isOwner && $reviewsAllowed)
            <div>
                <h4 class="font-semibold mb-2">Palikti atsiliepimą</h4>

                <form method="POST" action="{{ route('review.store', $listing->id) }}"
                      class="space-y-3">
                    @csrf

                    <select
                        name="ivertinimas"
                        class="border rounded px-3 py-2 w-full"
                    >
                        @foreach([1,2,3,4,5] as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                        @endforeach
                    </select>

                    <textarea
                        name="komentaras"
                        rows="4"
                        class="w-full border rounded p-3"
                        placeholder="Parašykite atsiliepimą..."
                    ></textarea>

                    <button
                        class="bg-blue-600 text-white px-4 py-2 rounded w-full"
                    >
                       Pateikti atsiliepimą
                    </button>
                </form>
            </div>
        @endif

    </div>
</section>


</div>
</x-app-layout>
