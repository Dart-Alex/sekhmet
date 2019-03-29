@component('mail::message')
Email envoyé par <a href="mailto:{{$from}}">{{$fromName}}&lt;{{$from}}&gt;</a> {{$to}}

@component('mail::panel')
{!!nl2br(e($body))!!}
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
