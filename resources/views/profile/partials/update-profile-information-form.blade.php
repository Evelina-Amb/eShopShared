<style>
    label {
        color: #111827; /* gray-900 */
    }
</style>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profilio informacija') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Atnaujinkite savo paskyros profilio informaciją.") }}
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
            <x-input-label for="vardas" value="Vardas" />
            <x-text-input
                id="vardas"
                name="vardas"
                type="text"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                :value="old('vardas', $user->vardas)"
                autocomplete="given-name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('vardas')" />
        </div>

        {{-- LAST NAME --}}
        <div>
            <x-input-label for="pavarde" value="Pavardė" />
            <x-text-input
                id="pavarde"
                name="pavarde"
                type="text"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                :value="old('pavarde', $user->pavarde)"
                autocomplete="family-name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('pavarde')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="el_pastas" :value="__('El. paštas')" />
            <x-text-input
                id="el_pastas"
                name="el_pastas"
                type="email"
                class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                :value="old('el_pastas', $user->el_pastas)"
                autocomplete="email"
            />
            <x-input-error class="mt-2" :messages="$errors->get('el_pastas')" />
        </div>

        {{-- SELLER TOGGLE --}}
        <div x-data="{ isSeller: {{ $user->role === 'seller' ? 'true' : 'false' }} }" class="space-y-4">

            {{-- SELLER CHECKBOX --}}
            @if (!$hasListings)
                <label class="inline-flex items-center text-gray-900">
                    <input
                        type="checkbox"
                        name="role"
                        value="seller"
                        @checked($user->role === 'seller')
                        @change="isSeller = $event.target.checked"
                    >
                    <span class="ml-2">Esu pardavėjas / verslas</span>
                </label>
            @else
                <div class="text-sm text-gray-600">
                   Negalite išjungti pardavėjo režimo, nes turite aktyvių skelbimų.
                </div>
            @endif

            <template x-if="isSeller">
                <div class="mt-4 space-y-4">

                    <div class="text-sm text-gray-600">
                        Ši informacija bus matoma Jūsų skelbimuose.
                    </div>

                    {{-- BUSINESS EMAIL --}}
                    <div>
                        <x-input-label for="business_email" value="Verslo el. paštas (viešas)" />
                        <x-text-input
                            id="business_email"
                            name="business_email"
                            type="email"
                            class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            :value="old('business_email', $user->business_email)"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('business_email')" />
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <x-input-label for="telefonas" value=" Telefono numeris (viešas)" />
                        <x-text-input
                            id="telefonas"
                            name="telefonas"
                            type="text"
                            inputmode="numeric"
                            pattern="^\+?[0-9]*$"
                            class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="+370xxxxxxx"
                            :value="old('telefonas', $user->telefonas)"
                            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('telefonas')" />
                    </div>

                    <p class="text-xs text-gray-500">
                        Pateikite bent vieną viešą kontaktinį būdą (el. paštą arba telefoną).
                    </p>

                </div>
            </template>
        </div>

        {{-- COUNTRY + CITY --}}
        <div class="space-y-4 mt-6">
            <x-input-label value="Vieta (privaloma pardavėjams)" />

            <div
                x-data='{
                    countries: @json(\App\Models\Country::select("id","pavadinimas")->orderBy("pavadinimas")->get()),
                    cities:     @json(\App\Models\City::select("id","pavadinimas","country_id")->orderBy("pavadinimas")->get()),
                    countryId: "{{ $currentCountryId ?? '' }}",
                    cityId: "{{ $currentCityId }}",

                    init() {
                        if (this.countryId) {
                            this.$nextTick(() => {
                                this.cityId = {{ $currentCityId ?? "null" }};
                            });
                        }
                    },

                    get filteredCities() {
                        if (!this.countryId) return [];
                        return this.cities.filter(c => Number(c.country_id) === Number(this.countryId));
                    }
                }'
                class="space-y-4"
            >

                {{-- COUNTRY --}}
                <div>
                    <x-input-label for="country_id" value="Šalis" />
                    <select
                        id="country_id"
                        name="country_id"
                        class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                        x-model="countryId"
                    >
                        <option value="">Pasirinkite šalį</option>
                        <template x-for="country in countries" :key="country.id">
                            <option
                                :value="country.id"
                                x-text="country.pavadinimas"
                                :selected="String(country.id) === String(countryId)"
                            ></option>
                        </template>
                    </select>
                </div>

                {{-- CITY --}}
                <div>
                    <x-input-label for="city_id" value="Miestas" />
                    <select
                        id="city_id"
                        name="city_id"
                        class="mt-1 block w-full bg-white text-gray-900 border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
                        x-model="cityId"
                    >
                        <option value="">Pasirinkite miestą</option>
                        <template x-for="city in filteredCities" :key="city.id">
                            <option :value="city.id.toString()" x-text="city.pavadinimas"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        {{-- ADDRESS --}}
        <div class="space-y-4 mt-8">
            <x-input-label value="Adresas (nebūtinas)" />

            {{-- STREET --}}
            <div>
                <x-input-label for="gatve" value="Gatvė" />
                <x-text-input
                    id="gatve"
                    name="gatve"
                    placeholder="Gatvės pavadinimas"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    :value="old('gatve', $user->address->gatve ?? '')"
                />
                <x-input-error class="mt-1" :messages="$errors->get('gatve')" />
            </div>

            {{-- HOUSE NUMBER --}}
            <div>
                <x-input-label for="namo_nr" value="Namo numeris" />
                <x-text-input
                    id="namo_nr"
                    name="namo_nr"
                    placeholder="pvz. 12A"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    :value="old('namo_nr', $user->address->namo_nr ?? '')"
                />
                <x-input-error class="mt-1" :messages="$errors->get('namo_nr')" />
            </div>

            {{-- FLAT NUMBER --}}
            <div>
                <x-input-label for="buto_nr" value="Buto durų numeris (nebūtinas)" />
                <x-text-input
                    id="buto_nr"
                    name="buto_nr"
                    placeholder="e.g. 5"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    :value="old('buto_nr', $user->address->buto_nr ?? '')"
                />
                <x-input-error class="mt-1" :messages="$errors->get('buto_nr')" />
            </div>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-500 transition"
            >
                {{ __('Išsaugoti') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Išsaugota.') }}
                </p>
            @endif
        </div>
    </form>

    {{-- STRIPE CONNECT SECTION --}}
    @if ($user->role === 'seller')
        <div class="mt-8 p-4 border rounded-lg bg-gray-50">

            @if (!$user->stripe_onboarded)
                <h3 class="text-md font-semibold text-gray-900">
                    Stripe išmokėjimai neprijungti
                </h3>

                <p class="mt-1 text-sm text-gray-600">
                   Norėdami gauti mokėjimus iš pirkėjų, turite prijungti savo Stripe paskyrą.
                    <br>(platformos mokestis – 10 %)
                </p>

                <a
                    href="{{ route('stripe.connect') }}"
                    class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition"
                >
                    Prijungti Stripe
                </a>
            @else
                <h3 class="text-md font-semibold text-green-700">
                    Stripe prijungta
                </h3>

                <p class="mt-1 text-sm text-gray-600">
                   Dabar galite gauti mokėjimus ir talpinti skelbimus. (platformos mokestis – 10 %)
                </p>

                @if(auth()->user()->stripe_onboarded)
                    <a
                        href="{{ route('stripe.dashboard') }}"
                        target="_blank"
                        class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition"
                    >
                       Peržiūrėti Stripe pajamas
                    </a>
                @endif
            @endif

        </div>
    @endif
</section>
