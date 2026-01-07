<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
       Išsiuntėme patvirtinimo nuorodą į jūsų el. paštą.
       Norėdami užbaigti registraciją, paspauskite ją.
    </div>

    @if (session('status') == 'link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
           Nauja patvirtinimo nuoroda buvo išsiųsta.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">

        <form action="{{ route('verify.resend') }}" method="POST">
            @csrf
            <x-primary-button>Siųsti el. laišką dar kartą</x-primary-button>
        </form>

        <a class="underline text-sm text-gray-600" href="{{ route('register') }}">
           Grįžti į registraciją
        </a>
    </div>
</x-guest-layout>
