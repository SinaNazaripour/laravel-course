@extends('components.testcomponents.inherit-layout')
@section('title', 'title')
@section('content')
    maikadfmv.a,mcvna => @parent
@endsection
@push('script')
    <script>
        alert('hello')
    </script>
@endpush

{{-- @for ($i = 0; $i < 3; $i++)
    @pushOnce('script')
    <script>
        alert('hello')
    </script>
@endpushOnce
@endfor --}}