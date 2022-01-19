@component('mail::message')
# New Order is completed !

Order : #{{ $ordere->id }} with a total : <b>{{ $ordere->admin_revenue }}$</b> has been completed

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
