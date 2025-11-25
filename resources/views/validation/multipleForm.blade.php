<form action="/multiple-forms" name="formOne" method="POST">
    @csrf
    <input type="text" name="firsFormField">

    {{-- <p>{{ $errors->formOne->first('firstFormField') }}</p> --}}
    @foreach ($errors->formOne->all() as $e)
        {{$e}}
    @endforeach
    <input name ="formOne" type="submit">
</form>

<form action="/multiple-forms" name="formTwo" method="POST">
    {{ csrf_field() }}
    {{ $errors->formTwo->first('secoundFormField') }}
    <input type="text" name="secoundFormField">
    <input name ="formTwo" type="submit">
</form>
