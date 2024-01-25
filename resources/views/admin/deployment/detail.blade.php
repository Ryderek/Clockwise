@extends('adminlte::page')

@section('title', 'Zamówienia')

@section('content_header')
    <div class="row">
        <div class="col-12 col-md-6">
            <table>
                <tr>
                    <th><h5 style="font-weight: bold; font-size: 16px; line-height: 0;">Zamówienie #{{ $Order->orderId }}</h5></th>
                    <th rowspan="2" style="width: 70px; text-align: center; vertical-align: middle; font-size: 32px; line-height: 50%;">→<br />&nbsp;</th>
                    <th><h5 style="font-weight: bold; font-size: 16px; line-height: 0;">Detal #{{ $Detail->orderDetailId }}</h5></th>
                </tr>
                <tr>
                    <th><span style="opacity: 0.6; font-size: 24px;">{{ $Order->orderName }}</span></th>
                    <th><span style="opacity: 0.6; font-size: 24px;">{{ $Detail->orderDetailName }}</span></th>
                </tr>
            </table>
        </div>
        <div class="col-12 col-md-6 text-right">
            <input id="inputScannedHere" type="text" style="opacity: 0; cursor: default;" />

            <a href="{{ route('admin.deployment', ["orderId" => $Order->orderId ]) }}"><button type="button" class="btn btn-secondary" id="afterscanBackButton">Powrót</button></a>
            <button type="button" class="btn btn-primary" id="scanningButton" onclick="toggleScaning();" style="margin-left: 15px;">Wydawanie</button>
        </div>
    </div>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif
@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')
<div class="w-100 row m-0">
    <table class="table table-noborder bg-white">
        <tr class="bg-gradient">
            <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">LP</th>
            <th class="bg-primary">Detal (EAN13)</th>
            <th class="bg-primary">Nazwa Detalu</th>
            <th class="bg-primary">Detal (EAN8)</th>
            <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Sztuka</th>
        </tr>
        @if(!isset($SingleDetails) || $SingleDetails == null)
            <tr>
                <td colspan="100%" class="text-center">Brak wprowadzonych obróbek</td>
            </tr>
        @else
            @foreach($SingleDetails as $SingleDetail)
                @if($SingleDetail["LP"] > $Detail->orderDetailItemsDone)
                    @php
                        $additionalCss = 'opacity: 0.4;';
                    @endphp
                @else
                    @php
                        $additionalCss = '';
                    @endphp
                @endif
                @if(in_array($SingleDetail["EanEightId"], $DetailsDeployed))
                    @php
                        $additionalClasses = "greenBackground";
                    @endphp   
                @else
                    @php
                        $additionalClasses = "";
                    @endphp 
                @endif
                <tr style="{{ $additionalCss }}" id="rowWith{{ $SingleDetail["EanEightId"] }}Ean" class="{{ $additionalClasses }}">
                    <td>{{ $SingleDetail["LP"] }}</td>
                    <td>{{ $Detail->orderDetailUniqueId }}</td>
                    <td>{{ $Detail->orderDetailName }}</td>
                    <td>{{ $SingleDetail["EanEightId"] }}</td>
                    <td style="text-align: right;">{{ $SingleDetail["LP"] }}/{{ $Detail->orderDetailItemsTotal }}</td>
                </tr>
            @endforeach
        @endif
    </table>
</div>
@stop

@section('css') 
    <style>
        .greenBackground{
            background: rgba(0, 200, 0, 0.2);
        }
        .scanningEnabled{
        }

        /* Dodajemy style dla elementu z klasą "scanningEnabled" */
        .scanningEnabled {
            position: relative;
            background: #0a0!important;
            border: none;
        }

        /* Definiujemy animację */
        @keyframes glowAnimation {
            0% {
                transform: scale(1); /* Skalowanie 100% */
                box-shadow: 0 0 10px 0 #00c800; /* Cień początkowy */
            }
            50% {
                transform: scale(1.1); /* Skalowanie do 120% */
                box-shadow: 0 0 20px 10px #00c800; /* Cień środkowy */
            }
            100% {
                transform: scale(1); /* Powrót do skalowania 100% */
                box-shadow: 0 0 10px 0 #00c800; /* Cień końcowy (taki jak na początku) */
            }
        }

        /* Dodajemy animację do elementu z klasą "scanningEnabled" */
        .scanningEnabled {
            animation: glowAnimation 1s ease infinite; /* 1s - czas trwania animacji, infinite - animacja będzie trwała w nieskończoność */
        }
    </style>
@stop

@section('js')
<script>
    function scaningDetailEnabled() {
        //console.log("Funkcja scaningDetailEnabled() została uruchomiona!");
        $("#inputScannedHere").click();
        $("#inputScannedHere").focus();
    }

    let intervalId = null;
    let isScaningEnabled = false;

    function toggleScaning() {
        if (isScaningEnabled) {
            //console.log("Wyłączono skanowanie.");
            $("#scanningButton").removeClass("scanningEnabled");
            clearInterval(intervalId);
        } else {
            //console.log("Włączono skanowanie.");
            scaningDetailEnabled();
            $("#scanningButton").addClass("scanningEnabled");
            intervalId = setInterval(scaningDetailEnabled, 1000);
        }
        isScaningEnabled = !isScaningEnabled;
    }

    function markScannedDetail(insertEanId){
        if(insertEanId.toString().length == 8){
            console.log("Sending EAN:"+insertEanId);
            $.ajax({
            url: '{{ route("admin.deployment.insertDeploy") }}',
            data: {
                deployedDetailOrderId: '{{ $Order->orderId }}',
                deployedDetailDetailId: '{{ $Detail->orderDetailId }}',
                deployedDetailEAN: insertEanId,
                _token: '{{ csrf_token() }}'
            },
            type: 'post',
            dataType: "json",
            success: function(jsonOutput) {
                if(jsonOutput.success == false){
                    alert("Wystąpił błąd: "+jsonOutput.errorMsg);
                }else{
                    $("#rowWith"+insertEanId+"Ean").addClass("greenBackground");
                }
            }
        }); 
        }
    }


    // Get button
    var input = document.getElementById("inputScannedHere");

    // Wait for button to press enter
    input.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        savedEanEight = $(input).val();
        markScannedDetail(savedEanEight);
        $(input).val("");
    }
    }); 
</script>

@stop