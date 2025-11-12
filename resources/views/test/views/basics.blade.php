<h1>variables and titles</h1>
<h5>variable-> {{ $name }}</h5>
<h5>vue.js variable-> @{{ name }}</h5>

@verbatim
    <p>for write js codes without @ like {{ name }}</p>
@endverbatim

<p>to write function in php -> {{ time() }}
</p>

<?php

echo 'we can also use php tag';
?>
{{-- blade comment not present in HTML document --}}
<!--html comment is presenr in html-->

<h1>Directives</h1>

@auth
    <h2>user is authenticated</h2>
@endauth

@guest
    <h2>guest mode</h2>
@endguest

@env('local')
    <h1>project on local</h1>
@endenv

@env('production')
    <h1>project on production</h1>
@endenv

@if (1 == 2)
@endif

@php
    $users = ['Ali', 'MMd', 'Reza'];
@endphp

<ul>
    @foreach ($users as $user)
        {{-- @continue($user=='Reza') --}}
        <li>{{ $user }}</li>
        {{-- @break($user=='Ali') --}}
    @endforeach
</ul>

@for ($i = 0; $i < 12; $i++)
    {{ $i }}
@endfor
