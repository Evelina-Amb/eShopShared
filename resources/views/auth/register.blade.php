<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="vardas" :value="__('First name')" />
            <x-text-input id="vardas" class="block mt-1 w-full" type="text" name="vardas" :value="old('vardas')" required autofocus autocomplete="vardas" />
            <x-input-error :messages="$errors->get('vardas')" class="mt-2" />
        </div>

        <!-- Last Name -->
<div class="mt-4">
    <x-input-label for="pavarde" :value="__('Last Name')" />
    <x-text-input id="pavarde" class="block mt-1 w-full"
                  type="text" name="pavarde"
                  :value="old('pavarde')" required />
    <x-input-error :messages="$errors->get('pavarde')" class="mt-2" />
</div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="el_pastas" :value="__('Email')" />
            <x-text-input id="el_pastas" class="block mt-1 w-full" type="email" name="el_pastas" :value="old('el_pastas')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('el_pastas')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="slaptazodis" :value="__('Password')" />
            <x-text-input id="slaptazodis" class="block mt-1 w-full"
                            type="password"
                            name="slaptazodis"
                            required autocomplete="new-slaptazodis" />

            <x-input-error :messages="$errors->get('slaptazodis')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="slaptazodis_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="slaptazodis_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="slaptazodis_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('slaptazodis_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
