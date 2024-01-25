@extends('layouts.production')

@section('title', 'Czas pracy')

@section('css')
    <meta http-equiv="refresh" content="1800;url={{ route("time-tracking") }}" />
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
    #unhideInputButton {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 40px;
        height: 40px;
        margin-top: 20px;
        background-color: rgba(0,0,0,0);
        background-image: url('/build/images/displayInputButton.png');
        background-size: 40px 40px;
        background-repeat: no-repeat;
        background-position: center center;
        border: none;
    }
    </style>
    <style>
        /* Style do wyśrodkowania napisu na ekranie */
        #message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            background-color: #f0f0f0;
            padding: 10px 20px;
            border-radius: 5px;
            opacity: 1; /* Ustawiamy przezroczystość na 0 */
        }

        /* Definicja animacji fadeInOut */
        @keyframes fadeInOut {
            0% { opacity: 0; }
            25% { opacity: 1; }
            75% { opacity: 1; }
            100% { opacity: 0; }
        }

        /* Style dla ciemnego tła */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Ustawiamy przezroczystość tła */
            display: none; /* Ukrywamy na początku */
            z-index: 999; /* Upewniamy się, że tło znajduje się na górze innych elementów */
            transition: 0.4s;
            opacity: 0; /* Ustawiamy przezroczystość na 0 */
            animation: fadeInOut 2s ease-in-out; /* Dodajemy animację fadeInOut na 3 sekundy */
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
        function replaceInputWithMe(whi, inp){
            $(whi).css("opacity", "0");
            $(whi).fadeOut(350, function (){
                $("#"+inp).css("transition", "0.3");
                setTimeout(() => {
                    $("#"+inp).css("opacity", "1");
                    $("#"+inp).fadeIn(300, function(){
                        $("#"+inp).focus();
                    });
                }, 50);
            });
        }
        $(document).ready(function() {
            if (!($("#authCardCode").is(":focus"))) {
                $("#authCardCode").trigger("focus");
            }
        });	

        function keepFocused(){
            if (!($("#authCardCode").is(":focus"))) {
                $("#authCardCode").trigger("focus");
            }
            setTimeout(function(){
                keepFocused();
            }, 500)
        }
        keepFocused();
    </script>
    @php
        if(isset($_GET['amIAtWork'])){ 
            $amIAtWork = $_GET['amIAtWork'];
        }else{
            $amIAtWork = -1; 
        } 
    @endphp
    <script>
        // Funkcja do pokazywania komunikatu na ekranie
        function showMessage(message) {
        const overlay = document.getElementById("overlay");
        const messageElement = document.getElementById("message");

        // Ustawiamy treść napisu
        messageElement.textContent = message;

        // Pokazujemy tło i napis
        overlay.style.display = "block";
        messageElement.style.display = "block";

        // Po 3 sekundach ukrywamy tło i napis
        setTimeout(function () {
            overlay.style.display = "none";
            messageElement.style.display = "none";
        }, 3000);
        }

        // Pobieramy wartość zmiennej 'amIAtWork' z adresu URL
        window.amIAtWork = {{ $amIAtWork }}

        // Wywołujemy funkcję showMessage z odpowiednim komunikatem w zależności od wartości zmiennej
        if (window.amIAtWork === 1) {
        showMessage("Rozpoczęto pracę!");
        } else if (window.amIAtWork === 0) {
        showMessage("Zakończono pracę!");
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
        <div style="display: table-cell; vertical-align: middle; max-width: 400px; width: 100%;" class="disableChildAnchor">
            <form method="POST" action="{{ route('time-tracking.switch-worktime') }}" style="min-height: 186px;">
                @csrf
                <div>
                    <h1 class="modal-title fs-2" id="logWorkTimeModalLabel">Rejestracja czasu pracownika</h1>
                </div>
                <div style="text-align: center;">
                    <span class="identityCardSecondStep form-label" style="display: inline;">Zeskanuj kartę aby zatwierdzić operację.</span><br /><br />
                    <input style="opacity: 0;" id="authCardCode" type="text" value="" name="authCardCode" autofocus="" required="" minlength="6" maxlength="127"><br />
                    <input type="button" id="unhideInputButton" onclick="replaceInputWithMe(this, 'authCardCode')" class="identityCardSecondStep" />
                </div>
            </form>
        </div>
    </div>
@stop


<div id="overlay">
    <div id="message">Rozpoczęto pracę!</div>    
</div> <!-- Dodajemy tło jako osobny div -->
