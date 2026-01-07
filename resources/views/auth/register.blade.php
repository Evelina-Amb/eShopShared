<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="vardas" :value="__('Vardas')" class="text-gray-900" />
            <x-text-input
                id="vardas"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="text"
                name="vardas"
                :value="old('vardas')"
                required
                autofocus
                autocomplete="given-name"
            />
            <x-input-error :messages="$errors->get('vardas')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="pavarde" :value="__('Pavardė')" class="text-gray-900" />
            <x-text-input
                id="pavarde"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="text"
                name="pavarde"
                :value="old('pavarde')"
                required
                autocomplete="family-name"
            />
            <x-input-error :messages="$errors->get('pavarde')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="el_pastas" :value="__('El. paštas')" class="text-gray-900" />
            <x-text-input
                id="el_pastas"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="email"
                name="el_pastas"
                :value="old('el_pastas')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('el_pastas')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="slaptazodis" :value="__('Slaptažodis')" class="text-gray-900" />
            <x-text-input
                id="slaptazodis"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="password"
                name="slaptazodis"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('slaptazodis')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="slaptazodis_confirmation" :value="__('Pakartokite slaptažodį')" class="text-gray-900" />
            <x-text-input
                id="slaptazodis_confirmation"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="password"
                name="slaptazodis_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('slaptazodis_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a
                href="{{ route('login') }}"
                class="underline text-sm text-gray-900 hover:text-blue-600"
            >
                {{ __('Jau turite paskyrą?') }}
            </a>

            <x-primary-button class="bg-blue-600 hover:bg-blue-500">
                {{ __('Registruotis') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
