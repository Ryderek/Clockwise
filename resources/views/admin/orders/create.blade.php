@php 
    $currentDate = date("Y-m-d");
    $dateInTwoWeeks = date("Y-m-d", strtotime('+2 weeks'));  
@endphp

@extends('adminlte::page')

@section('title', 'Zamówienia')

@section('content_header')
    <h1> Zlecenia </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('datalists')
    <datalist id="contractors">
        @foreach($contractors as $contractor)
            <option value="{{ $contractor->customerName }}">
        @endforeach
    </datalist>
@stop

@section('content')
    
<form action="{{ route('order.create') }}" method="POST" autocomplete="off">
    <input style="display: none" type="text" name="fakeUsernameAutofill" />
    @csrf
    <div class="w-100 row">
        
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                    <h4 class="m-0"><span>Krok 1</span> - Informacje o zamówieniu</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <div class="col-12 col-md-6 col-xl-8">
                            <div class="row">
                                <input style="display: none" type="text" name="fakeUsernameAutofill" />
                                <div class="col-12 col-xl-8">
                                    <label for="orderName" class="form-label">Nazwa zamówienia</label>
                                    <input type="text" class="form-control" id="orderName" name="orderName" placeholder="Podaj nazwę zamówienia" maxlength="127" minlength="3" aria-describedby="orderNameHelp" required>
                                    <div id="orderNameHelp" class="form-text">Nazwa zamówienia może zawierać nazwę firmy lub numer faktury.</div>
                                </div>
                                <div class="col-12 col-xl-4 mt-md-0 mt-3">
                                    <label for="orderDeadline" class="form-label">Termin wykonania</label>
                                    <input type="date" min="{{ $currentDate }}" value="{{ $dateInTwoWeeks }}" class="form-control" id="orderDeadline" name="orderDeadline" required>
                                </div>
                                <div class="col-12 col-xl-4 mt-md-0 mt-3">
                                    <label for="orderAdditionalField" class="form-label">AF</label>
                                    <input type="text" class="form-control" id="orderAdditionalField" name="orderAdditionalField">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-4">
                            <label for="orderDeadline" class="form-label">Czasy obróbki</label>
                            @foreach($roles as $role)
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> {{ $role->roleProcess }} </span>
                                    </div>
                                    <input type="number" step="1" class="form-control" style="text-align: right;" required value="0" min="0" name="complexTime[{{ $role->roleSlug }}]">
                                    <div class="input-group-append">
                                        <span class="input-group-text">h</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                    <h4 class="m-0"><span>Krok 2</span> - Informacje o zamawiającym</h4>
                </div>
                <div class="col-12 col-md-6">
                    <label for="customerName" class="form-label">Nazwa zamawiającego</label>
                    <input onchange="fillCustomerFieldsByCustomerName(this);" type="text" list="contractors" class="form-control" id="customerName" name="customerName" placeholder="Wprowadź nazwę kontrahenta" maxlength="127" minlength="3" required>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-2 mt-md-0 mt-3">
                    <label for="customerTaxIdentityNumber" class="form-label">NIP</label>
                    <div class="input-group mb-3">
                        <input style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;" type="text" class="form-control" id="customerTaxIdentityNumber" name="customerTaxIdentityNumber" placeholder="Wprowadź NIP" maxlength="16" minlength="10" required>
                        <span style="border-top-left-radius: 0; border-bottom-left-radius: 0; cursor: pointer;" class="input-group-text" id="basic-addon2" onclick="fillCustomerFieldsByNIP();"><i id="nipSearchIcon" class="fas fa-fw fa-search"></i></span>
                    </div>
                </div>

                <div class="w-100"></div>

                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-md-2 mt-3">
                    <label for="customerCountry" class="form-label">Kraj</label>
                    <input type="text" class="form-control" id="customerCountry" name="customerCountry" placeholder="Wprowadź kraj" value="Polska" maxlength="127" minlength="3" required>
                </div>
                <div class="col-12 col-md-12 col-lg-8 col-xl-5 mt-md-2 mt-3">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                            <label for="customerPostal" class="form-label">Kod pocztowy</label>
                            <input type="text" class="form-control" id="customerPostal" name="customerPostal" placeholder="Kod pocztowy" maxlength="31" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-6 col-lg-7 col-xl-8 mt-md-0 mt-3">
                            <label for="customerCity" class="form-label">Miasto</label>
                            <input type="text" class="form-control" id="customerCity" name="customerCity" placeholder="Nazwa miejscowości" maxlength="31" minlength="3" required>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8 col-xl-4 mt-md-2 mt-3">
                    <label for="customerAddress" class="form-label">Ulica</label>
                    <input type="text" class="form-control" id="customerAddress" name="customerAddress" placeholder="ul. Przykładowa 123/15" maxlength="120" minlength="3" required>
                </div>

                <div class="col-12 mt-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="sameDeliveryAddressCheckChecked" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" checked>
                        <label class="form-check-label" for="sameDeliveryAddressCheckChecked">Adres dostawy taki sam jak zamawiającego </label>
                    </div>
                </div>

                <div class="collapse col-12" id="collapseExample">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-md-2 mt-3">
                            <label for="customerDeliveryCountry" class="form-label">Kraj dostawy</label>
                            <input type="text" class="form-control" id="customerDeliveryCountry" name="customerDeliveryCountry" placeholder="Wprowadź kraj" value="Polska" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-12 col-lg-8 col-xl-5 mt-md-2 mt-3">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                                    <label for="customerDeliveryPostal" class="form-label">Kod pocztowy dostawy</label>
                                    <input type="text" class="form-control" id="customerDeliveryPostal" name="customerDeliveryPostal" placeholder="Kod pocztowy" maxlength="31" minlength="3" required>
                                </div>
                                <div class="col-12 col-md-6 col-lg-7 col-xl-8 mt-md-0 mt-3">
                                    <label for="customerDeliveryCity" class="form-label">Miasto dostawy</label>
                                    <input type="text" class="form-control" id="customerDeliveryCity" name="customerDeliveryCity" placeholder="Nazwa miejscowości" maxlength="31" minlength="3" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-8 col-xl-4 mt-md-2 mt-3">
                            <label for="customerDeliveryAddress" class="form-label">Ulica dostawy</label>
                            <input type="text" class="form-control" id="customerDeliveryAddress" name="customerDeliveryAddress" placeholder="ul. Przykładowa 123/15" maxlength="120" minlength="3" required>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Utwórz zamówienie</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
    <style>
        /* Disable number arrows */
        /*
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
        -moz-appearance: textfield;
        }
        */
    </style>
    <style>
        /* Rotating hourglass */
        @-webkit-keyframes rotating /* Safari and Chrome */ {
        from {
            -webkit-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
        }
        @keyframes rotating {
        from {
            -ms-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -ms-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -webkit-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
        }
        .rotating {
        -webkit-animation: rotating 2s linear infinite;
        -moz-animation: rotating 2s linear infinite;
        -ms-animation: rotating 2s linear infinite;
        -o-animation: rotating 2s linear infinite;
        animation: rotating 2s linear infinite;
        }
    </style>
@stop

@section('js')
    <script>
        function cloneFieldsObserver(observeElement){
            isSDA = $( "#sameDeliveryAddressCheckChecked" ).prop( "checked" );
            if(isSDA == true){
                observeDeliveryElement = observeElement.replace("customer", "customerDelivery");
                $("#"+observeDeliveryElement).val($("#"+observeElement).val());
            }
        }

        // Add listeners:
        document.getElementById("customerCountry").addEventListener( "keyup", function(){
            cloneFieldsObserver("customerCountry");
        });
        document.getElementById("customerPostal").addEventListener( "keyup", function(){
            cloneFieldsObserver("customerPostal");
        });
        document.getElementById("customerCity").addEventListener( "keyup", function(){
            cloneFieldsObserver("customerCity");
        });
        document.getElementById("customerAddress").addEventListener( "keyup", function(){
            cloneFieldsObserver("customerAddress");
        });
        document.getElementById("sameDeliveryAddressCheckChecked").addEventListener( "change", function(){
            cloneFieldsObserver("customerCountry");
            cloneFieldsObserver("customerPostal");
            cloneFieldsObserver("customerCity");
            cloneFieldsObserver("customerAddress");
        });
        function fillCustomerFieldsByCustomerName(whi){
            contractorsList = [
                @foreach($contractors as $contractor)
                    "{{ $contractor->customerName }}",
                @endforeach
            ];
            if(contractorsList.includes(whi.value)){
                $.ajax({
                    url: '/admin/ajax/getContractorDataByName',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customerName: whi.value
                    },
                    type: 'post',
                    success: function(output) {
                        if ($('#sameDeliveryAddressCheckChecked').is(':checked')) {
                            $("#sameDeliveryAddressCheckChecked").click();
                        }
                        $("#customerTaxIdentityNumber").val(output.customerTaxIdentityNumber);
                        $("#customerCountry").val(output.customerCountry);
                        $("#customerPostal").val(output.customerPostal);
                        $("#customerCity").val(output.customerCity);
                        $("#customerAddress").val(output.customerAddress);
                        $("#customerDeliveryCountry").val(output.customerDeliveryCountry);
                        $("#customerDeliveryPostal").val(output.customerDeliveryPostal);
                        $("#customerDeliveryCity").val(output.customerDeliveryCity);
                        $("#customerDeliveryAddress").val(output.customerDeliveryAddress);
                    }
                }); 
            }
        }
    </script>
    <script>
        // Fill customer by nip function (ajax)
        function fillCustomerFieldsByNIP(){
            nipno = $("#customerTaxIdentityNumber").val();
            console.log(nipno);
            $.ajax({
                url: '/api/gus/getNipData',
                data: {
                    _token: '{{ csrf_token() }}',
                    nip: nipno
                },
                type: 'post',
                beforeSend: function() {
                    $("#nipSearchIcon").removeClass('fa-search');
                    $("#nipSearchIcon").addClass('fa-hourglass');
                    $("#nipSearchIcon").addClass('rotating');
                },
                success: function(output) {
                    output = JSON.parse(output);
                    if(output.status == "critical" || output.status == 'failure'){
                        alert(output.errorMessage)
                    }else{
                        output = output.output;
                        if ($('#sameDeliveryAddressCheckChecked').is(':checked')) {
                            $("#sameDeliveryAddressCheckChecked").click();
                        }
                        $("#customerName").val(output.customerName);
                        $("#customerTaxIdentityNumber").val(output.customerTaxIdentityNumber);
                        $("#customerCountry").val(output.customerCountry);
                        $("#customerPostal").val(output.customerPostal);
                        $("#customerCity").val(output.customerCity);
                        $("#customerAddress").val(output.customerAddress);
                        $("#customerDeliveryCountry").val(output.customerDeliveryCountry);
                        $("#customerDeliveryPostal").val(output.customerDeliveryPostal);
                        $("#customerDeliveryCity").val(output.customerDeliveryCity);
                        $("#customerDeliveryAddress").val(output.customerDeliveryAddress);
                    }
                    $("#nipSearchIcon").removeClass('fa-hourglass');
                    $("#nipSearchIcon").removeClass('rotating');
                    $("#nipSearchIcon").addClass('fa-search');
                },
                error: function(output) {
                    alert("Critical error appeared. Check te console.");
                    console.error(output);
                }
            }); 
        }
    </script>
@stop


