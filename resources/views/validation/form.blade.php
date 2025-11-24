<form action="" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="text" name="email" value="{{ old('email') }}" placeholder="email">
    @error('email')
        {{ $message }}
    @enderror

    <br>

    <input type="text" name="title" value="{{ old('title') }}" placeholder="title">
    @error('title')
        {{ $message }}
    @enderror

    <div>
        photo
        <br>
        <input type="file" name="photo" value="{{ old('photo') }}">
        @error('photo')
            {{ $message }}
        @enderror
    </div>

    <div>
        custom validation
        <br>
        <input type="text" name="custom" value="{{ old('custom') }}">
        @error('custom')
            {{ $message }}
        @enderror
    </div>

    <div>
        validation based on another field
        <br>
        <input type="text" name="card_number" value="{{ old('card_number') }}">
        @error('card_number')
            {{ $message }}
        @enderror

        <br>

        <div>
            payment method
            <select name="payment" id="payment">
                <option value="cc" @selected(old('payment') == 'cc')>pay by credit card</option>
                <option value="cash" @selected(old('payment') == 'cash')>cash</option>
            </select>
            @error('payment')
                {{ $message }}
            @enderror
        </div>
    </div>

    <div>
        published_at
        <br>
        <input type="text" name="published_at" value="{{ old('published_at') }}">
        @error('published_at')
            {{ $message }}
        @enderror
    </div>

    <div>
        start_date
        <br>
        <input type="date" name="start_date" value="{{ old('start_date') }}">
        @error('start_date')
            {{ $message }}
        @enderror
    </div>

    <div>
        v.01
        <br>
        <input type="text" name="v_01" value="{{ old('v_01') }}">
        @error('v_01')
            {{ $message }}
        @enderror
    </div>

    <div>
        password
        <br>
        <input type="password" name="password">
        @error('password')
            {{ $message }}
        @enderror
    </div>
    <input type="submit" value="submit">

    @foreach ($errors->all() as $error)
        <span style="color:blueviolet;"><br>{{ $error }}</span>
    @endforeach
</form>
