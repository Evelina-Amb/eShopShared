<x-app-layout>
    <div class="py-12">
        @if(auth()->user()->role === 'seller' && auth()->user()->stripe_onboarded)
    <a href="{{ route('stripe.dashboard') }}"
       class="btn btn-outline-primary"
       target="_blank">
        View Stripe earnings
    </a>
@endif
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg max-w-2xl w-full mx-auto">
                <div class="w-full">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

           <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg max-w-2xl w-full  mx-auto">
                <div class="w-full">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
           <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg max-w-2xl w-full  mx-auto">
                <div class="w-full">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

