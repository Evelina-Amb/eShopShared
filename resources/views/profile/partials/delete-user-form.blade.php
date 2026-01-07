<section class="space-y-6 bg-white text-gray-900 dark:bg-white dark:text-gray-900">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ištrinti paskyrą') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ištrynus paskyrą, visi jos duomenys ir ištekliai bus negrįžtamai pašalinti.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="dark:bg-red-600 dark:hover:bg-red-700"
    >
        {{ __('Ištrinti paskyrą') }}
    </x-danger-button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form method="post"
              action="{{ route('profile.destroy') }}"
              class="p-6 bg-white text-gray-900 dark:bg-white dark:text-gray-900">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ar tikrai norite ištrinti savo paskyrą?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Ištrynus paskyrą, visi jos duomenys ir ištekliai bus negrįžtamai pašalinti. Įveskite savo slaptažodį, kad patvirtintumėte, jog norite visam laikui ištrinti paskyrą.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Slaptažodis') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 bg-white text-gray-900"
                    placeholder="{{ __('Slaptažodis') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
               <button
                type="button"
                x-on:click="$dispatch('close')"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                >
                {{ __('Atšaukti') }}
                </button>
                <x-danger-button class="ms-3 dark:bg-red-600 dark:hover:bg-red-700">
                    {{ __('Ištrinti paskyrą') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
