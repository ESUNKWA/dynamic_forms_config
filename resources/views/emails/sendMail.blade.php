@component('mail::message')

<h3>{{ $titre }} :</h3>

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

<div style="max-width: 320px; margin: 0 auto; padding: 20px; background: #fff;">
	{{ $data }}
</div>

Merci,<br>
{{ config('app.name') }}
@endcomponent
