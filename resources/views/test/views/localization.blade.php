{{ __('messages.welcome') }}

{{-- pluralization --}}

<p>{{ trans_choice('messages.apples', 3) }}:"3"</p>
{{ trans_choice('messages.apples', 1) }}:"1"

{{-- send parameters --}}
<br>
{{ __('messages.hello', ['name' => 'sina']) }}

{{-- use json to cleaner code and more readablity --}}
<br>
{{__('good bye')}}

<br>

{{trans_choice("banana|bananas",2)}}

<br>
{{ __('hello :name', ['name' => 'sina']) }}