@extends('layouts.production')

@section('title', 'Czas pracy')

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
            <h1>Cześć {{ explode(" ", $user->name)[0]; }}!</h1>
            @if($amIAtWork == false)
                <h6 class="mb-5">
                    Nie zapomnij rozpocząć czasu pracy! 
                </h6>
            @elseif($amIAtWork == true)
                <h4 class="mb-5">
                    Aktualny czas pracy: <span id="workTimingTotalTime"></span>
                </h4>
                <script> calculateTimeProcess({{ $amIAtWork->workTimingStart }}, $("#workTimingTotalTime")); </script>
                
            @endif
            <form method="POST" class="modal-content" action="{{ route('time-tracking.switch-worktime') }}">
                @csrf
                @if($amIAtWork == false)
                    <button type="submit" style="max-width: 400px; width: 100%; margin-left: auto; margin-right: auto; display: block; font-size: 24px;" class="btn btn-outline-success btn-lg mb-4" id="logModalTrigger" data-bs-toggle="modal" data-bs-target="#logWorkTimeModal">CZAS START</button>
                @elseif($amIAtWork == true)
                    <button type="submit" style="max-width: 400px; width: 100%; margin-left: auto; margin-right: auto; display: block; font-size: 24px;" class="btn btn-outline-danger btn-lg mb-4" id="logModalTrigger" data-bs-toggle="modal" data-bs-target="#logWorkTimeModal">CZAS STOP</button>
                @endif
                <input style="opacity: 0;" id="authCardCode" type="hidden" value="{{ $authCardId }}" name="authCardCode" autofocus="" required="" minlength="6" maxlength="127">
            </form>
            <form method="POST" class="boxLimiter" action="{{ route("time-tracking.logout"); }}">
                @csrf
                <input type="hidden" name="authCardId" value="{{ $authCardId }}" readonly />
                <div class="row">
                    <div class="col-12 col-md-12">
                        <input type="submit" class="btn btn-dark dashboardButton" id="logoutBtn" name="logout" value="Wyloguj" />
                    </div>
                </div>
            </form>
            
            @if(isset($_GET['autologout']))
                <span>Automatyczne wylogowanie nastąpi za kilka sekund...</span>
            @endif
        </div>
    </div>
    {{-- @include('time-tracking.partials.worktime-modal') --}}
    <script>
        $('#scanEanModalClicker').click(function(){
            setTimeout(function(){
                console.log("poof!");
                $('#detailEanClicker').focus();
            }, 500);
        });
    </script>
    @if(isset($_GET['autologout']))
        <script>
            setTimeout(() => {
                $("#logoutBtn").click();
            }, 3500);
        </script>
    @else
        <script>
            setTimeout(() => {
                $("#logoutBtn").click();
            }, 30000);
        </script>
    @endif
@stop

