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

<div class="max-w-6xl mx-auto py-10 px-4">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-6 px-4">
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
    <div class="mb-6 px-4">
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    </div>
@endif

    {{-- LISTING CARD --}}
    <div class="bg-white rounded-lg shadow p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- LEFT: IMAGE GALLERY --}}
            <div>
<img
    id="mainImage"
    src="{{ $listing->photos->isNotEmpty()
        ? asset('storage/' . $listing->photos->first()->failo_url)
        : 'https://via.placeholder.com/600x450?text=No+Image'
    }}"
    class="rounded-lg shadow w-full max-h-[450px] object-cover mb-4"
/>



                @if($listing->photos->count() > 1)
    <div class="flex gap-3">
       @foreach($listing->photos as $photo)
    <img
        src="{{ asset('storage/' . $photo->failo_url) }}"
        class="w-20 h-20 rounded object-cover cursor-pointer border hover:ring-2 hover:ring-blue-400"
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
                <div class="flex items-center justify-between mb-4">

                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $listing->pavadinimas }}
                    </h1>

                    @if(auth()->check() && auth()->id() !== $listing->user_id)
                       <button
    type="button"
    @click.prevent="toggle({{ $listing->id }})"
    class="text-3xl"
>
    <span
        x-show="isFavorite({{ $listing->id }})"
        class="text-red-500"
    >❤️</span>

    <span
        x-show="!isFavorite({{ $listing->id }})"
        class="text-gray-300"
    >🤍</span>
</button>

                    @endif

                </div>

                {{-- DESCRIPTION --}}
                <div class="text-gray-700 leading-relaxed mb-6 whitespace-pre-line">
                    {!! nl2br(e($listing->aprasymas)) !!}
                </div>

                {{-- PRICE --}}
                <div class="text-2xl font-semibold text-gray-800 mb-2">
                    {{ number_format($listing->kaina, 2, ',', '.') }} €
                    <span class="text-gray-500 text-sm">
                        @if($listing->tipas === 'preke') / vnt @else / Service @endif
                    </span>
                </div>

                {{-- AVAILABLE --}}
                <div class="text-gray-700 mb-4">
                    <strong>Available:</strong> 
                    <span class="{{ $listing->kiekis == 0 ? 'text-red-600 font-bold' : '' }}">
                        {{ $listing->kiekis }}
                    </span>
                </div>

                {{-- RENEWABLE BADGE --}}
                @if($listing->is_renewable)
                    <div class="mb-4">
                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded text-sm">
                            Renewable product – seller restocks this item
                        </span>
                    </div>
                @endif

                {{-- CART OR EDIT --}}
                @if(auth()->check() && auth()->id() === $listing->user_id)

                    <a href="{{ route('listing.edit', $listing->id) }}" 
                       class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center w-40">
                        Edit listing
                    </a>

                @else

                    @if($listing->tipas === 'paslauga')

                        <div class="mt-4 text-gray-700 font-semibold">
                            This is a service listing. Contact the seller to arrange details.
                        </div>

                    @else

                        <form method="POST" action="{{ route('cart.add', $listing->id) }}" class="flex items-center gap-4">
                            @csrf

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
                                    max="{{ $listing->kiekis }}"
                                    class="w-16 text-center border-l border-r"
                                >

                                <button type="button"
                                    onclick="let q=this.previousElementSibling; let val=parseInt(q.value)||1; if(val < {{ $listing->kiekis }}) q.value = val+1;"
                                    class="px-3 py-2 hover:bg-gray-100"
                                >+</button>
                            </div>

                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                            >
                                Add to cart
                            </button>
                        </form>

                    @endif
                @endif

                {{-- SELLER INFO --}}
                <div class="mt-10 border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Seller</h3>

                    <div class="bg-gray-50 p-4 rounded border">
                        <div class="text-gray-900 font-semibold text-lg">
                            {{ $listing->user->vardas }} {{ $listing->user->pavarde }}
                        </div>

                        @if($listing->user->business_email)
                            <div class="text-gray-600 text-sm mt-1">
                                Email: {{ $listing->user->business_email }}
                            </div>
                        @endif

                        @if($listing->user->telefonas)
                            <div class="text-gray-700 text-sm mt-1">
                                Tel: {{ $listing->user->telefonas }}
                            </div>
                        @endif

                        @if($listing->user->address?->city)
                            <div class="text-gray-700 text-sm mt-1">
                                City: {{ $listing->user->address->city->pavadinimas }}
                            </div>
                        @endif

                        @if(!$listing->user->business_email && !$listing->user->telefonas)
                            <div class="text-red-600 text-sm mt-2">
                                No public contact information available.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
   {{-- REVIEWS SECTION --}}
<div class="mt-10 border-t pt-6">

    @php
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
    @endphp

    {{-- HEADER --}}    
    <h3 class="font-semibold text-gray-800 mb-4">Reviews</h3>

    {{-- IF REVIEWS NOT ALLOWED --}}
    @if(!$reviewsAllowed)
        <p class="text-gray-600 italic">
            Reviews are only available for renewable items or non-renewable items with quantity ≥ 1.
        </p>
        @if($totalReviews > 0)
            <p class="text-sm text-gray-500 mt-2">
                (Existing reviews below are visible but no new reviews can be posted.)
            </p>
        @endif
    @endif

    {{-- SHOW AVERAGE IF ANY REVIEWS EXIST --}}
    @if($totalReviews > 0)
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="text-3xl text-yellow-500">
                    {{ str_repeat('⭐', floor($avgRating)) }}
                    @if($avgRating - floor($avgRating) >= 0.5) ⭐ @endif
                </div>

                <div class="text-gray-700 text-lg">
                    <strong>{{ $avgRating }}</strong> / 5
                    <span class="text-gray-500">({{ $totalReviews }} reviews)</span>
                </div>
            </div>

            {{-- SORT DROPDOWN --}}
            <form method="GET">
                <select 
                    name="sort"
                    onchange="this.form.submit()"
                    class="border rounded px-2 py-1 text-sm"
                >
                    <option value="newest" @selected($sort === 'newest')>Newest</option>
                    <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                    <option value="highest" @selected($sort === 'highest')>Highest rated</option>
                    <option value="lowest" @selected($sort === 'lowest')>Lowest rated</option>
                </select>
            </form>
        </div>
    @endif

    {{-- OWNER OR REVIEWS DISABLED = FULL WIDTH --}}
    @if($isOwner || !$reviewsAllowed)

        <div class="border rounded p-4 bg-gray-50 space-y-4">
            @forelse($sortedReviews as $review)
                <div class="bg-white p-4 rounded border shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong>{{ $review->user->vardas }}</strong>
                        <span class="text-yellow-500">{{ str_repeat('⭐', $review->ivertinimas) }}</span>
                    </div>

                    @if($review->komentaras)
                        <p class="text-gray-700 mt-2">{{ $review->komentaras }}</p>
                    @endif

                    <p class="text-gray-400 text-xs mt-1">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-gray-600 italic">No reviews yet.</p>
            @endforelse
        </div>

    @else
    {{-- USER CAN REVIEW = TWO COLUMN LAYOUT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- LEFT: OTHER REVIEWS --}}
        <div class="border rounded p-4 bg-gray-50 max-h-96 overflow-y-auto space-y-4">
            @forelse($otherReviews as $review)
                <div class="bg-white p-4 rounded border shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong>{{ $review->user->vardas }}</strong>
                        <span class="text-yellow-500">{{ str_repeat('⭐', $review->ivertinimas) }}</span>
                    </div>

                    @if($review->komentaras)
                        <p class="text-gray-700 mt-2">{{ $review->komentaras }}</p>
                    @endif

                    <p class="text-gray-400 text-xs mt-1">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-gray-600 italic">No reviews yet.</p>
            @endforelse
        </div>

        {{-- RIGHT: USER REVIEW/FORM --}}
        <div>

            {{-- User already reviewed --}}
            @if($userReview)

                <h4 class="text-lg font-semibold mb-2">Your review</h4>

                <div class="bg-white border rounded p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <strong>{{ $userReview->user->vardas }}</strong>
                        <span class="text-yellow-500">{{ str_repeat('⭐', $userReview->ivertinimas) }}</span>
                    </div>

                    @if($userReview->komentaras)
                        <p class="text-gray-700 mt-2">{{ $userReview->komentaras }}</p>
                    @endif

                    <p class="text-gray-400 text-xs mt-1">{{ $userReview->created_at->diffForHumans() }}</p>
                </div>

            {{-- Form to leave a review --}}
            @else

                <h4 class="text-lg font-semibold mb-2">Leave a review</h4>

                <form method="POST" action="{{ route('review.store', $listing->id) }}">
                    @csrf

                    <label class="block mb-2">
                        Rating:
                        <select name="ivertinimas" class="border rounded w-16 h-9 text-center">
                            @foreach([1,2,3,4,5] as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
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

            @endif

        </div>

    </div>
    @endif

</div>

    {{-- OTHER PRODUCTS --}}
    @if($similar->count() > 0)
        <div class="mt-14">
            <h2 class="text-2xl font-bold mb-6">Other products from this seller</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($similar as $s)
                    @if($s->id !== $listing->id)
                        <a href="{{ route('listing.single', $s->id) }}" 
                           class="bg-white shadow rounded overflow-hidden hover:shadow-md transition">
                            <img
    src="{{ $s->photos->isNotEmpty()
        ? asset('storage/' . $s->photos->first()->failo_url)
        : 'https://via.placeholder.com/300'
    }}"
    class="w-full h-40 object-cover"
/>

                            <div class="p-4">
                                <div class="font-semibold mb-1">{{ $s->pavadinimas }}</div>
                                <div class="text-green-700 font-semibold">
                                    {{ number_format($s->kaina, 2, ',', '.') }} €
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

</div>
</x-app-layout>
