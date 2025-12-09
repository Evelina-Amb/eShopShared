<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        We’ve sent a verification link to your email.  
        Please click it to complete your registration.
    </div>

    @if (session('status') == 'link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">

        <form action="{{ route('verify.resend') }}" method="POST">
            @csrf
            <x-primary-button>Resend email</x-primary-button>
        </form>

        <a class="underline text-sm text-gray-600" href="{{ route('register') }}">
            Back to Register
        </a>
    </div>
</x-guest-layout>
