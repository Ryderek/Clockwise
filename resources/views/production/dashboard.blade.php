@extends('layouts.production')

@section('title', 'Produkcja')


@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

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
    .disableChildAnchor a{
        text-decoration: none;
        color: inherit;
    }
    .boxLimiter{
        width: 100%;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
    table td{
        text-align: left;
        vertical-align: top;
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

            /*
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
            */
            output = "";
            if(sekund < 10){
                sekund = "0"+sekund;
            }
            output = sekund;
            if(minut < 10){
                minut = "0"+minut;
            }
            output = minut+":"+output;
            if(godzin < 10){
                godzin = "0"+godzin;
            }
            output = godzin+":"+output;
            if(dni > 1){
                if(dni < 10){
                    dni = "0"+dni;
                }
                output = dni+":"+output;
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

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif
@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif


@section('content')
    <div class="h-100" style="display: table; text-align: center; width: 100%;">
        <div style="display: table-cell; vertical-align: middle;" class="disableChildAnchor">
            <table class="boxLimiter">
                <tr>
                    <td style="text-align: center;"><h1>Cześć {{ explode(" ", $user->name)[0]; }}!</h1></td>
                    {{-- <td rowspan="2" style="text-align: right; vertical-align:middle;">
                        @if($amIAtWork == false)
                            <button type="button" class="btn btn-outline-success btn-lg mb-5" id="logModalTrigger" data-bs-toggle="modal" data-bs-target="#logWorkTimeModal">CZAS<br />START</button>
                        @elseif($amIAtWork == true)
                            <button type="button" class="btn btn-outline-danger btn-lg mb-5" id="logModalTrigger" data-bs-toggle="modal" data-bs-target="#logWorkTimeModal">CZAS<br />STOP</button>
                        @endif
                    </td> --}}
                </tr>
                <tr>
                    <td style="text-align: center;">
                        @if($amIAtWork == false)
                            <h6 class="mb-5">
                                Nie zapomnij rozpocząć czasu pracy! 
                            </h6>
                        @elseif($amIAtWork == true)
                            <h4 class="mb-5">
                                Czas pracy: <span id="workTimingTotalTime">nan</span>
                            </h4>
                            <script> calculateTimeProcess({{ $amIAtWork->workTimingStart }}, $("#workTimingTotalTime")); </script>
                            
                        @endif
                    </td>
                </tr>
            </table>
            <form method="POST" class="boxLimiter" action="{{ route("production.dashboard"); }}">
                @csrf
                <input type="hidden" name="authCardId" value="{{ $authCardId }}" readonly />
                <div class="row">
                    <div class="col-12 col-md-12">
                        <input type="submit" class="btn btn-success dashboardButton" name="processing" value="Aktywne zlecenia" />
                    </div>
                    <div class="col-12 col-md-12">
                        <input type="button" class="btn btn-primary dashboardButton" id="scanEanModalClicker" value="Obróbka detali" data-bs-toggle="modal" data-bs-target="#scanDetailModal" />
                    </div>
                    <div class="col-12 col-md-12">
                        <input type="button" class="btn btn-primary dashboardButton" name="toolshop" value="Narzędzia" data-bs-toggle="modal" data-bs-target="#createNotificationModal" />
                    </div>
                    @if($canDeployOrders)
                    <div class="col-12 col-md-12">
                        <input type="submit" class="btn btn-primary dashboardButton" name="deployment" value="Wydawanie zleceń" />
                    </div>
                    @endif
                    <div class="col-12 col-md-12">
                        <input type="submit" class="btn btn-primary dashboardButton" name="adminpanel" value="Panel Administratora" />
                    </div>
                    <div class="col-12 col-md-12">
                        <input type="submit" class="btn btn-dark dashboardButton" name="logout" value="Wyloguj" />
                    </div>
                </div>
                
                
                
                
                
            </form>
        </div>
    </div>
    {{-- @include('production.partials.worktime-modal') --}}
    @include('production.partials.create-notification')
    @include('production.detailing.partials.scanDetailModal')
    @include('production.detailing.partials.scanDetailModal')
    <script>
        $('#scanEanModalClicker').click(function(){
            setTimeout(function(){
                $('#detailUniqueCode').focus();
                $('#detailUniqueCode').click();
            }, 500);
        });
    </script>
@stop

