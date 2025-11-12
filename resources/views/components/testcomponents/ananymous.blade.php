<button @required(true) >
    ananymous components
</button {{$attributes}}>
@props(['propName'=>12,'message'])
<p>{{$attributes}}and {{$propName}} and {{$message}}</p>



<p>attributes merge: {{$attributes->merge(['class'=>'merged'.$propName])}}</p>

<p {{$attributes->merge(['class'=>'merged'.$propName])}}>use attributes and props to make classes </p>