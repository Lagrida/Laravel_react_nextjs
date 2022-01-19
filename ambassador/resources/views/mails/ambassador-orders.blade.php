@component('mail::message')
# New Order is completed !

You earned <b>{{ $ordere->ambassador_revenue }}$</b> from the link : {{ $ordere->link_code }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
