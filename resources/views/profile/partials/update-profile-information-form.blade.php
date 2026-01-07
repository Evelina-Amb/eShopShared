<style>
    label {
        color: #111827; /* black labels */
    }
</style>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    @php
        $currentCity      = $user->address?->City;
        $currentCountryId = $currentCity?->country_id;
        $currentCityId    = $currentCity?->id;
        $hasListings      = $user->listings()->count() > 0;
    @endphp

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- NAME --}}
        <div>
            <x-input-label for="vardas" value="Name" />
            <x-text-input
                id="vardas"
                name="vardas"
                type="text"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300
                       focus:border-blue-500 focus:ring-blue-500"
                :value="old('vardas', $user->vardas)"
                autocomplete="given-name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('vardas')" />
        </div>

        {{-- LAST NAME --}}
        <div>
            <x-input-label for="pavarde" value="Last Name" />
            <x-text-input
                id="pavarde"
                name="pavarde"
                type="text"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300
                       focus:border-blue-500 focus:ring-blue-500"
                :value="old('pavarde', $user->pavarde)"
                autocomplete="family-name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('pavarde')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="el_pastas" value="Email" />
            <x-text-input
                id="el_pastas"
                name="el_pastas"
                type="email"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300
                       focus:border-blue-500 focus:ring-blue-500"
                :value="old('el_pastas', $user->el_pastas)"
                autocomplete="email"
            />
            <x-input-error class="mt-2" :messages="$errors->get('el_pastas')" />
        </div>

        {{-- SELLER TOGGLE --}}
        <div x-data="{ isSeller: {{ $user->role === 'seller' ? 'true' : 'false' }} }" class="space-y-4">

            @if (!$hasListings)
                <label class="inline-flex items-center text-gray-900">
                    <input
                        type="checkbox"
                        name="role"
                        value="seller"
                        @checked($user->role === 'seller')
                        @change="isSeller = $event.target.checked"
                    >
                    <span class="ml-2">I am a seller / business</span>
                </label>
            @else
                <div class="text-sm text-gray-600">
                    You cannot disable seller mode because you have active listings.
                </div>
            @endif

            <template x-if="isSeller">
                <div class="mt-4 space-y-4">

                    <div class="text-sm text-gray-600">
                        This information will be visible on your listings.
                    </div>

                    {{-- BUSINESS EMAIL --}}
                    <div>
                        <x-input-label for="business_email" value="Business Email (public)" />
                        <x-text-input
                            id="business_email"
                            name="business_email"
                            type="email"
                            class="mt-1 block w-full bg-white text-gray-900 border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                            :value="old('business_email', $user->business_email)"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('business_email')" />
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <x-input-label for="telefonas" value="Phone Number (public)" />
                        <x-text-input
                            id="telefonas"
                            name="telefonas"
                            type="text"
                            inputmode="numeric"
                            class="mt-1 block w-full bg-white text-gray-900 border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                            placeholder="+370xxxxxxx"
                            :value="old('telefonas', $user->telefonas)"
                            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('telefonas')" />
                    </div>

                </div>
            </template>
        </div>

        <div class="flex items-center gap-4 mt-6">
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded
                       hover:bg-blue-500 transition"
            >
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
