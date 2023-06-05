@component('mail::message')
# Introduction

You one time password is:

{{ $otp }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
