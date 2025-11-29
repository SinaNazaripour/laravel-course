<form action="/form-exception-handling" method="POST">
    @csrf
    <input type="text" name="user" value="{{ old('user') }}">
    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif
    <input type="submit">
</form>
