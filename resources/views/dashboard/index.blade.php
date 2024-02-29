@extends('layouts.master')
@section('content')
    <div class="container">
        {!! $customerChart->container() !!}

    </div>
    
    <div class="container">
        {!! $salesChart->container() !!}

    </div>
    {{-- <div class="container">
        {!! $itemChart->container() !!}

    </div> --}}
    {!! $customerChart->script() !!}
     {!! $salesChart->script() !!}
    {{-- {!! $itemChart->script() !!}  --}}
@endsection