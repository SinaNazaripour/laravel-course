<h1>Work with HTML</h1>
@php
    $isActive = true;
    $hasError = false;
    $version = '1.2.3';
@endphp

<p @class(['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError])>
    conditional dynamic class
</p>

<p @style(['p-4', 'font-bold' => $isActive, 'background-color:red' => $hasError])>
    conditional dynamic style
</p>


<label for="#check">dynamic input with @@checked(old('fiedl', default)) </label>
<input id="check" name="active" type="checkbox" @checked(old('active',$isActive))>

<select name="partner" id="">
    <option value="reza" @selected(false)>reza</option>
    <option value="melika" @selected(true)>melika</option>
</select>

<form action=""method="POST">
    @csrf
    @method('PUT')
    <input type="submit" @disabled($hasError)>
</form>

<div class="" >

    @include('test.views.basics')
</div>
