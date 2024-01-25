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
        text-align: left;
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
    <script>
        
        window.serverTime = {{ time() }};
        window.localTime = (new Date())/1000;
        window.timeSyncDifference = window.serverTime-(window.localTime-1);

        function timeSince(date) {

            var seconds = Math.floor((new Date() - date) / 1000);
            seconds = seconds + window.timeSyncDifference;

            sekund = Math.floor(seconds % 60);
            minut = Math.floor((seconds % 3600)/60);
            godzin = Math.floor((seconds % 86400)/3600)
            dni = Math.floor(seconds/86400)
            output = sekund+" sek";
            if (minut > 0 || godzin > 0) {
                output = minut+" min "+output;
            }
            if (godzin > 0) {
                output = godzin+" h "+output;
            }
            if (dni > 0) {
                output = dni+" dni "+output;
            }
            
            return output;
        }
        function calculateTimeProcess(tim, targ){
            difference = timeSince(tim*1000);
            targ.html(difference);
            setTimeout(function(){
                calculateTimeProcess(tim, targ);
            },  1000)
        }
    </script>
@stop

@section('content')
    <div class="h-100" style="display: table; text-align: center; width: 100%;">
        <div style="display: table-cell; vertical-align: middle;" class="disableChildAnchor">
            <table class="headerTable">
                <tr>
                    <td style="text-align: left;">
                        <h1>Detale</h1>
                        <h5 class="mb-3">Poniżej znajduje się lista detali, nad którymi pracujesz:</h5>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route("production") }}" class="btn btn-dark btn-lg">
                            Powrót
                        </a>
                    </td>
                </tr>
            </table>
            @if(isset($details))
                <table class="table table-hover table-noborder bg-white productionTable">
                    <thead>
                        <tr class="bg-gradient">
                            <th class="bg-primary text-white" scope="col" style="border-top-left-radius: 0.25rem; text-align: center;">#</th>
                            <th class="bg-primary text-white">Nazwa detalu (ID/Zlecenie)</th>
                            <th class="bg-primary text-white">Kod detalu</th>
                            <th class="bg-primary text-white">Typ obróbki</th>
                            <th class="bg-primary text-white">Czas trwania</th>
                            <th class="text-right bg-primary text-white" style="border-top-right-radius: 0.25rem; text-align: right;">Detale Wykonane</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr data-bs-toggle="modal" data-bs-target="#doneWorkTimeModal" data-bs-maxDetails="{{ $detail->orderDetailItemsTotal-$detail->orderDetailItemsGrouped }}" data-bs-detailName="{{ $detail->orderDetailName }}" data-bs-workTimingId="{{ $detail->workTimingId }}">
                                <td style="text-align: center;" >{{ $detail->workTimingId }}</td>
                                <td>{{ $detail->orderDetailName }} ({{ $detail->orderDetailId }}/{{ $detail->orderDetailOrderId }})</td>
                                <td>{{ $detail->orderDetailUniqueId }}</td>
                                <td>{{ $detail->roleProcess }}</td>
                                <td id="workTiming{{$detail->workTimingId}}Time"></td>
                                <td class="text-right" style="text-align: right;"> {{ $detail->orderDetailItemsGrouped }} / {{ $detail->orderDetailItemsTotal }} ({{ round(($detail->orderDetailItemsDone/$detail->orderDetailItemsTotal)*100) }}%)</td>
                            </tr>
                            <script> calculateTimeProcess({{ $detail->workTimingStart }}, $("#workTiming{{$detail->workTimingId}}Time")); </script>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h6>Ta lista jest pusta, hurra!</h6>
            @endif
        </div>
    </div>
    
@stop

@include('production.processing.partials.done-worktiming')
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