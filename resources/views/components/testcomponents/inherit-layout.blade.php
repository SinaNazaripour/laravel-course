<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document- @yield('title')</title>
    @stack('script')
</head>


<body>
    {{-- @yield('content') --}}

    @section('content')
        lkdflsdfk
    @show

    {{-- for css or js codes to render by pagelayout --}}

    
</body>

</html>
