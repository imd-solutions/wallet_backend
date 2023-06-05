@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ url('/images/logo/logo.png') }}" alt="">
        @endcomponent
    @endslot
    # Dear {{ $content->name }}

    You requested your password to be reset.

    Please click on the link below.

    @component('mail::button', ['url' => url('/auth/reset/'. $token->token )])
        Reset Password
    @endcomponent

    Thanks,

    {{ config('app.name') }} Support Team
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }}{{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
