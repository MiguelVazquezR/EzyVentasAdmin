@component('mail::message')
# {{ $greeting }}

{!! $description !!}

@component('mail::button', ['url' => $url])
Ver en sistema
@endcomponent

{{ $salutation }}
Equipo de Ezy Ventas. 

@isset($subcopy)
@component('mail::subcopy')
{!! $subcopy !!}
@endcomponent
@endisset

@endcomponent