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
    .productionTable td{
        padding-top: 18px;
        padding-bottom: 18px;
        cursor: pointer;
    }
    .headerTable{
        width: 100%; 
        max-width: 1200px; 
        margin-left: auto;
        margin-right: auto;
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
                        <h1>Wybrano zlecenie: {{ $order->orderName }}!</h1>
                        <h5 class="mb-3">To zlecenie posiada następujące detale:</h5>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route("production.detailing") }}" class="btn btn-dark btn-lg">
                            Powrót
                        </a>
                    </td>
                </tr>
            </table>
            @if(isset($details))
                <table class="table table-hover table-noborder bg-white productionTable">
                    <thead>
                        <tr class="bg-gradient">
                            <th class="bg-primary text-white" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                            <th class="bg-primary text-white">Kod detalu</th>
                            <th class="bg-primary text-white">Nazwa detalu</th>
                            <th class="text-right bg-primary text-white" style="border-top-right-radius: 0.25rem; text-align: right;">Detale Wykonane</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr onclick='document.location.href="{{ route("production.detailing.detail", ["orderId" => $orderId, "orderDetailId" => $detail->orderDetailId]) }}"'>
                                <td>{{ $detail->orderDetailId }}</td>
                                <td>{{ $detail->orderDetailUniqueId }}</td>
                                <td>{{ $detail->orderDetailName }}</td>
                                <td class="text-right" style="text-align: right;">{{ $detail->orderDetailItemsDone }} / {{ $detail->orderDetailItemsTotal }} ({{ round(($detail->orderDetailItemsDone/$detail->orderDetailItemsTotal)*100) }}%)</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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