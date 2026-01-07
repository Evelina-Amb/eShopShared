{{-- REVIEWS --}}
<div class="mt-10 border-t pt-6">
    <h3 class="font-semibold text-gray-800 mb-4">Atsiliepimai</h3>

    {{-- Existing reviews --}}
    @foreach($listing->review as $review)
        <div class="bg-gray-50 p-4 rounded border mb-3">
            <div class="flex items-center gap-2">
                <strong>{{ $review->user->vardas }}</strong>
                <span class="text-yellow-500">
                    {{ str_repeat('⭐', $review->ivertinimas) }}
                </span>
            </div>

            <p class="text-gray-700 mt-2">{{ $review->komentaras }}</p>

            <p class="text-gray-400 text-xs mt-1">
                {{ $review->created_at->diffForHumans() }}
            </p>
        </div>
    @endforeach

    {{-- Review form --}}
    @auth
        @if(auth()->id() !== $listing->user_id)
            <div class="mt-6">
                <h4 class="text-lg font-semibold mb-2">Palikti atsiliepimą</h4>

                @if($errors->has('review'))
                    <div class="text-red-600 mb-2">{{ $errors->first('review') }}</div>
                @endif

                @if(session('success'))
                    <div class="text-green-700 mb-2">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('review.store', $listing->id) }}">
                    @csrf

                    <label class="block mb-2">
                        Įvertinimas:
                        <select name="ivertinimas" class="border px-2 py-1 rounded">
                            <option value="1">⭐ 1</option>
                            <option value="2">⭐ 2</option>
                            <option value="3">⭐ 3</option>
                            <option value="4">⭐ 4</option>
                            <option value="5">⭐ 5</option>
                        </select>
                    </label>

                    <textarea name="komentaras" rows="3" class="w-full border rounded p-2"
                        placeholder="Parašykite atsiliepimą..."></textarea>

                    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded">
                       Pateikti atsiliepimą
                    </button>
                </form>
            </div>
        @endif
    @endauth
</div>
