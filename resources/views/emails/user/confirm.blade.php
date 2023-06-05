@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ url('/images/logo/logo.png') }}" alt="">
        @endcomponent
    @endslot
# Dear {{ $content['name'] }}

Please click the button below to validate your email.

@component('mail::button', ['url' => url('/confirm/email/' . $content['encrypt'])])
    Validate My Email
@endcomponent

@if( isset($content['password']) )
    *** IMPORTANT NOTICE *** Please reset your password before you sign in.
@endif

Thanks,<br>
{{ config('app.name') }} Support Team
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
