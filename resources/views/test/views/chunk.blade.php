<p>chunk is a basket to carry data when we have not greate hardware we choose smaller bascket</p>

@foreach ($products->chunk($chunk) as $chunk)
    <div style="border: 5px solid red">
        @foreach ($chunk as $product )
            <a href="">{{ $product['name'] }} : $ {{$product['price']}}</a>
        @endforeach
    </div>
@endforeach
