<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="el_pastas" :value="__('El. paštas')" class="text-gray-900" />
            <x-text-input
                id="el_pastas"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="text"
                name="el_pastas"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('el_pastas')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Slaptažodis')" class="text-gray-900" />
            <x-text-input
                id="password"
                class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-900">
                    {{ __('Prisiminti mane') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">

            <!-- Create Account -->
            <a
                href="{{ route('register') }}"
                class="underline text-sm text-gray-900 hover:text-blue-600"
            >
                {{ __('Sukurti paskyrą') }}
            </a>

            <div class="flex items-center gap-4">

                <!-- Forgot Password -->
                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="underline text-sm text-gray-900 hover:text-blue-600"
                    >
                        {{ __('Pamiršote slaptažodį?') }}
                    </a>
                @endif

                <x-primary-button class="bg-blue-600 hover:bg-blue-500">
                    {{ __('Prisijungti') }}
                </x-primary-button>

            </div>
        </div>
    </form>
</x-guest-layout>
