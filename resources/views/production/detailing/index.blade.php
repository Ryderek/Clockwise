@extends('layouts.production')

@section('title', 'Produkcja')

@section('css')
    <style>
    html,
    body,
    #app,
    #app > main{
        height: 100%
    }
    body {
        background: #eee;
    }
    .dashboardButton{
        padding: 20 28px;
        font-size: 32px;
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        margin-bottom: 20px;
        max-width: 400px;
    }
    .productionTable{
        width: 100%; 
        max-width: 1200px; 
        margin-left: auto;
        margin-right: auto;
        font-size: 1.25rem;
    }
    .headerTable{
        width: 100%; 
        max-width: 1200px; 
        margin-left: auto;
        margin-right: auto;
    }
    .productionTable td{
        padding-top: 18px;
        padding-bottom: 18px;
        cursor: pointer;
    }
    </style>
@stop

@section('js')
    
@stop

@section('content')
    <div class="h-100" style="display: table; text-align: center; width: 100%;">
        <div style="display: table-cell; vertical-align: middle;" class="disableChildAnchor">
            <table class="headerTable">
                <tr>
                    <td style="text-align: left;">
                        <h1>Wytwórzmy kilka nowych detali!</h1>
                        <h5 class="mb-3">Oto lista zleceń opublikowanych na liście "do zrobienia":</h5>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route("production") }}" class="btn btn-dark btn-lg">
                            Powrót
                        </a>
                    </td>
                </tr>
            </table>
            @if(isset($orders))
                @if(1==0)
                <table class="table table-hover table-noborder bg-white productionTable">
                    <thead>
                        <tr class="bg-gradient">
                            <th scope="col" class="bg-primary text-white" style="border-top-left-radius: 0.25rem;">#</th>
                            <th scope="col" class="bg-primary text-white" >Zamówienie</th>
                            <th scope="col" class="bg-primary text-white" >Deadline</th>
                            <th scope="col" class="bg-primary text-white" >Utworzono</th>
                            <th scope="col" class="text-right bg-primary text-white" style="border-top-right-radius: 0.25rem;">Zaktualizowano</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr onclick="document.location.href='{{ route("production.detailing.order", ["orderId" => $order->orderId]) }}'">
                                <td scope="row">{{ $order->orderId }}</td>
                                <td>{{ $order->orderName }}</td>
                                <td>{{ substr($order->orderDeadline, 0, 10) }} (@if($order->orderDateDiff >= 0) za {{ $order->orderDateDiff }} dni @else {{ abs($order->orderDateDiff) }} dni temu @endif)</td>
                                <td>{{ substr($order->created_at, 0, 10) }}</td>
                                <td>{{ substr($order->updated_at, 0, 10) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    Ten feature jest nieaktywny!
                @endif
            @else
                <h6>Ta lista jest pusta, hurra!</h6>
            @endif
        </div>
    </div>
    
@stop
<?php

/*
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

*/ 
?>