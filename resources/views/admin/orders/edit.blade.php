@extends('adminlte::page')

@section('title', 'Zamówienia')

@section('content_header')
    <div class="row">
        <div class="col-12 col-md-6">
            <h1>Zamówienie #{{ $order->orderId }} {{ $order->orderName }}</h1>
        </div>
        <div class="col-12 col-md-6 text-right">
            @if($order->orderStatus == "created")
                <a href="{{ route('order.remove', ['id' => $order->orderId]) }}" style="text-decoration: none; color: inherit; margin-right: 15px;">
                    <button type="button" class="btn btn-danger">&nbsp;<i class="fas fa-trash"></i>&nbsp;</button>
                </a>
            @endif
            <a target="_blank" href="{{ route('print', ['refererSlug' => 'order-details-barcodes', 'refererArgument' => $order->orderId]) }}" style="text-decoration: none; color: inherit;">
                <button type="button" class="btn btn-dark">&nbsp;<i class="fas fa-print"></i>&nbsp;</button>
            </a>
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createDetail">Dodaj nowy detal</button>
            <button type="button" class="btn btn-primary" data-bs-toggle="collapse" href="#orderCollapser" role="button" aria-expanded="false" data-bs-target="#details-collapse" aria-controls="details-collapse" style="margin-left: 15px;">Szczegóły zamówienia</button>
            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="collapse" href="#complexCollapser" role="button" aria-expanded="false" data-bs-target="#complex-collapse" aria-controls="complex-collapse" style="margin-left: 0px;">Harmonogram</button> -->
            <a href="{{ route('order.live', ['id' => $order->orderId]) }}" style="text-decoration: none; color: inherit; margin-right: 5px;">
                <button type="button" class="btn btn-primary">&nbsp;<i class="fas fa-clock"></i>&nbsp;</button>
            </a>
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
    <div class="col-12 mb-1 p-0 collapse" id="details-collapse">
        <div class="row">
            <section class="col-12 p-2 bg-white">
                <form class="row px-2" action="{{ route('order.update') }}" method="POST" autocomplete="off">
                    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
                    <input style="display: none;" type="text" name="editOrderId" value="{{ $order->orderId }}" />
                    <input style="display: none;" type="text" name="editCustomerId" value="{{ $customer->customerId }}" />
                    @csrf                
                    <div class="col-12 py-2 mb-3 pl-4 bg-gradient rounded">
                        <h4 class="m-0"><span class="text-white">Szczegóły zamówienia</span></h4>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3 col-lg-3 col-xl-6">
                            <label for="orderName" class="form-label">Nazwa zamówienia</label>
                            <input type="text" class="form-control" id="orderName" name="orderName" value="{{ $order->orderName}}" placeholder="Podaj nazwę zamówienia" maxlength="127" minlength="3" aria-describedby="orderNameHelp" required>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3 col-xl-2 mt-md-0 mt-3">
                            <label for="orderDeadline" class="form-label">Termin wykonania</label>
                            <input type="date" value="{{ substr($order->orderDeadline, 0, 10) }}" min="{{ substr($order->orderCreatedTime, 0, 10) }}" class="form-control" id="orderDeadline" name="orderDeadline" required>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3 col-xl-2 mt-md-0 mt-3">
                            <label for="orderAdditionalField" class="form-label">AF</label>
                            <input type="text" value="{{ $order->orderAdditionalField }}" class="form-control" name="orderAdditionalField"></a>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3 col-xl-2 mt-md-0 mt-3">
                            <label for="orderDeadline" class="form-label">Dokument WZ</label>
                            <a href="{{ route('docs.generate-summary', ["orderId" => $order->orderId]) }}"><input type="button" value="Generuj dokument" class="form-control btn btn-primary" id="orderDeadline" name="orderDeadline" required></a>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3 col-xl-2 mt-3">
                            <table>
                                <tr>
                                    <td style="width: 50%;"><label for="orderStatusLight" class="form-label">Status</label><br /></td>
                                    <td style="width: 50%;"> 
                                        <label for="orderDetailCooperated" class="form-label">Kooperacja</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;">
                                        <input type="checkbox" class="" id="orderStatusLight" name="orderStatusLight"
                                            @if($order->orderStatusLight == 1)
                                                checked
                                            @endif
                                        > Status
                                    </td>
                                    <td style="width: 50%;">
                                        <input type="checkbox" class="" id="orderDetailCooperated" name="orderCooperated"
                                            @if($order->orderCooperated == 1)
                                                checked
                                            @endif
                                        > Kooperacja
                                    </td>
                                </tr>
                            </table>
                            
                           
                        </div>
                        <div class="col-12">
                            <hr />
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="customerName" class="form-label">Nazwa zamawiającego</label>
                            <input type="text" class="form-control" id="customerName" value="{{ $customer->customerName }}" name="customerName" placeholder="Wprowadź nazwę kontrahenta" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-2 mt-md-0 mt-3">
                            <label for="customerTaxIdentityNumber" class="form-label">NIP</label>
                            <div class="input-group mb-3">
                                <input style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;" type="text" class="form-control" id="customerTaxIdentityNumber" value="{{ $customer->customerTaxIdentityNumber }}" name="customerTaxIdentityNumber" placeholder="Wprowadź NIP" maxlength="16" minlength="10" required>
                                <span style="border-top-left-radius: 0; border-bottom-left-radius: 0; cursor: pointer;" class="input-group-text" id="basic-addon2" onclick="fillCustomerFieldsByNIP();"><i id="nipSearchIcon" class="fas fa-fw fa-search"></i></span>
                            </div>
                        </div>

                        <div class="w-100"></div>

                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-md-2 mt-3">
                            <label for="customerCountry" class="form-label">Kraj</label>
                            <input type="text" class="form-control" id="customerCountry" name="customerCountry" placeholder="Wprowadź kraj" value="{{ $customer->customerCountry }}" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-12 col-lg-8 col-xl-5 mt-md-2 mt-3">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                                    <label for="customerPostal" class="form-label">Kod pocztowy</label>
                                    <input type="text" class="form-control" id="customerPostal" name="customerPostal" placeholder="Kod pocztowy" value="{{ $customer->customerPostal }}" maxlength="31" minlength="3" required>
                                </div>
                                <div class="col-12 col-md-6 col-lg-7 col-xl-8 mt-md-0 mt-3">
                                    <label for="customerCity" class="form-label">Miasto</label>
                                    <input type="text" class="form-control" id="customerCity" name="customerCity" placeholder="Nazwa miejscowości" value="{{ $customer->customerCity }}" maxlength="31" minlength="3" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-8 col-xl-4 mt-md-2 mt-3">
                            <label for="customerAddress" class="form-label">Ulica</label>
                            <input type="text" class="form-control" id="customerAddress" name="customerAddress" value="{{ $customer->customerAddress }}" placeholder="ul. Przykładowa 123/15" maxlength="120" minlength="3" required>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mt-md-2 mt-3">
                                    <label for="customerDeliveryCountry" class="form-label">Kraj dostawy</label>
                                    <input type="text" class="form-control" id="customerDeliveryCountry" name="customerDeliveryCountry" value="{{ $customer->customerDeliveryCountry }}" placeholder="Wprowadź kraj" value="Polska" maxlength="127" minlength="3" required>
                                </div>
                                <div class="col-12 col-md-12 col-lg-8 col-xl-5 mt-md-2 mt-3">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                                            <label for="customerDeliveryPostal" class="form-label">Kod pocztowy dostawy</label>
                                            <input type="text" class="form-control" id="customerDeliveryPostal" name="customerDeliveryPostal" placeholder="Kod pocztowy"  value="{{ $customer->customerDeliveryPostal }}" maxlength="31" minlength="3" required>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-7 col-xl-8 mt-md-0 mt-3">
                                            <label for="customerDeliveryCity" class="form-label">Miasto dostawy</label>
                                            <input type="text" class="form-control" id="customerDeliveryCity" name="customerDeliveryCity" placeholder="Nazwa miejscowości" value="{{ $customer->customerDeliveryCity }}" maxlength="31" minlength="3" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 col-lg-8 col-xl-4 mt-md-2 mt-3">
                                    <label for="customerDeliveryAddress" class="form-label">Ulica dostawy</label>
                                    <input type="text" class="form-control" id="customerDeliveryAddress" name="customerDeliveryAddress" placeholder="ul. Przykładowa 123/15" value="{{ $customer->customerDeliveryAddress }}" maxlength="120" minlength="3" required>
                                </div>
                            </div>
                        </div>  
                        <div class="col-12">
                            <hr />
                            <label for="orderName" class="form-label">Czasy zamówienia</label>
                        </div>                  
                        @foreach($complexWTs as $complex)
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> {{ $complex['roleProcess'] }} </span>
                                    </div>
                                    <input type="number" step="1" class="form-control" style="text-align: right;" required value="{{ $complex['workTimingFinal'] }}" min="0" name="complexTime[{{ $complex['workTimingId'] }}]">
                                    <div class="input-group-append">
                                        <span class="input-group-text">h</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                        </div>
                    </div>
                </form>
            </section>
            <section class="col-12 col-md-6 p-2 pr-md-3">
                <div class="row px-2 py-2 bg-white">
                    <div class="col-12 px-0 py-0">
                        <table class="table table-noborder">
                            <tr class="bg-gradient">
                                <th class="bg-primary" style="border-top-left-radius: 0.25rem;">Notatka</th>
                                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Data modyfikacji</th>
                            </tr>
                            @foreach($notes as $note)
                                <tr>
                                    <td style="padding: 0.25rem;">
                                        <a href="{{ route('note.edit', ['id' => $note->noteId]) }}">{{ $note->noteTitle }}</a>
                                    </td>
                                    <td style="padding: 0.25rem;" class="text-right">
                                        {{ $note->updated_at }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="col-12 mt-4 ml-0 pl-0">
                        <a href="{{ route('note.create', ['relator' => 'order', 'relatorId' => $order->orderId]) }}"><button type="submit" class="btn btn-primary">Dodaj notatkę</button></a>
                    </div>
                </div>
            </section>
            <section class="col-12 col-md-6 p-2 pl-md-3">
                <div class="row px-2 py-2 bg-white">
                    <div class="col-12 px-0 py-0">
                        <table class="table table-noborder">
                            <tr class="bg-gradient">
                                <th class="bg-primary" style="border-top-left-radius: 0.25rem;">Nazwa załącznika</th>
                                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Data utworzenia</th>
                            </tr>
                            @foreach($attachments as $attachment)
                                <tr>
                                    <td style="padding: 0.25rem;">
                                        <a href="{{ $attachment->attachmentPath }}">{{ $attachment->attachmentTitle }}</a>
                                    </td>
                                    <td style="padding: 0.25rem;" class="text-right">
                                        {{ $attachment->updated_at }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="col-12 mt-4 ml-0 pl-0">
                        <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">Dodaj załącznik</button>
                    </div>
                </div>
            </section>
        </div>
    </div>

    
    <div class="col-12 mb-1 p-0 collapse" id="complex-collapse"> 
        <div class="row">
            
            {{-- <div class="col-12 mb-1 p-0">
                <table class="table table-hover table-noborder bg-white">
                    <tr class="bg-gradient">
                        <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                        <th class="bg-primary">Obróbka</th>
                        <th class="bg-primary">Jednostkowy czas obróbki</th>
                        <th class="bg-primary">Całościowy czas obróbki</th>
                        <th class="bg-primary">Obrobione detale</th>
                        <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
                    </tr>
                    @if(!isset($complexWorkTimings) || $complexWorkTimings == null)
                        <tr>
                            <td colspan="100%" class="text-center">Brak wprowadzonych obróbek</td>
                        </tr>
                    @else
                        <?php $total_rta = 0; ?>
                        @foreach($complexWorkTimings as $cWT)
                            <tr>
                                <td>{{ $cWT->workTimingId }}</td>
                                <td>{{ $cWT->roleProcess }}</td>
                                <td>{{ $cWT->workTimingFinal }} minut</td>
                                <td>{{ $cWT->totalTimeInHuman }} </td>
                                <td>{{ $cWT->realTime }}</td>
                                <td></td>
                            </tr>
                            <?php $total_rta += count($cWT->realTimesArray); ?>
                        @endforeach
                    @endif
                </table>
                <!-- <div class="col-12 col-md-6 mb-2 text-left">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorktimingModal">Dodaj obróbkę</button>
                </div> -->
            </div> --}}
        </div>
    </div>


    <div class="col-12 mb-0 py-2 pl-4">
        <h4 class="m-0">Lista detali</h4>
    </div>
    <div class="col-12 mb-1 p-0">
        <table class="table table-hover table-noborder">
            <tr class="bg-gradient">
                <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                <th class="bg-primary">Kod detalu</th>
                <th class="bg-primary">Nazwa detalu</th>
                @foreach($roles as $role)
                    <th scope="col" class="bg-primary">{{ $role->roleProcess }}</th>
                @endforeach
                <th class="bg-primary">Detale Wykonane</th>
                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
            <?php 
                $sumTimesUpTable = [
                    "real" => [],
                    "estimated" => [
                        "hours" => "0"
                    ]
                ]; 
            ?>
            @foreach($roles as $role)
                <?php
                    $sumTimesUpTable["real"][$role->roleSlug] = 0;
                    $sumTimesUpTable["estimated"][$role->roleSlug]["hours"] = 0;
                    $sumTimesUpTable["estimated"][$role->roleSlug]["minutes"] = 0;
                ?>
            @endforeach
            <?php $incrementor = 0; ?>
            @foreach($details as $detail)
            <?php $incrementor++; ?>
            <tr>
                <td>{{ $detail->orderDetailOrderNumber /*$detail->orderDetailId*/ }}</td>
                <td>{{ $detail->orderDetailUniqueId }}</td>
                <td>
                    {{ $detail->orderDetailName }}
                    @if($detail->orderDetailCooperation == 1)
                        <span class="statusLamp" style="background: rgb(204, 102, 0); border-radius: 50%; display: inline-block; transform: translateY(1px); line-height: 12px; text-align: center; color: #fff;">©</span>
                    @endif
                </td>
                @foreach($roles as $role)
                    <?php $rowDone = 0;  ?>
                    @foreach($detail->eWT as $ewt)
                        @if($ewt->workTimingRoleSlug == $role->roleSlug)
                            <?php $sumUpTimes = 0; ?>
                            @foreach($ewt->realTimesArray as $rta)
                                <?php
                                    if($rta->workTimingEnd != null){
                                        $sumUpTimes += $rta->workTimingEnd - $rta->workTimingStart; 
                                    }else{
                                        $sumUpTimes += time() - $rta->workTimingStart; 
                                    }
                                ?>
                            @endforeach
                            <?php
                                $sumTimesUpTable['real'][$role->roleSlug] += $sumUpTimes/3600;
                                $sumTimesUpTable["estimated"][$role->roleSlug]["hours"] += $ewt->totalTimeInHours;
                                $sumTimesUpTable["estimated"][$role->roleSlug]["minutes"] += $ewt->totalTimeInMinutes;
                            ?>
                            <td scope="col">{{ floor($sumUpTimes/3600) }}h {{ round(60*(($sumUpTimes/3600)-floor(($sumUpTimes/3600)))) }} min {{-- / {{ $ewt->totalTimeInHours }}h {{ $ewt->totalTimeInMinutes }}m --}}</td>
                            <?php $rowDone++; ?>
                            @if($ewt->roleSlug == 'cnc-manufacturing' && $detail->orderDetailId == 3)
                                {{-- dd($ewt) --}}
                            @endif
                        @endif
                    @endforeach
                    @if($rowDone == 0)
                        <td scope="col">n.d.</td>
                    @endif
                @endforeach
                <td>{{ $detail->orderDetailItemsDone }} / {{ $detail->orderDetailItemsTotal }} ( @if($detail->orderDetailItemsTotal != 0) {{ round(($detail->orderDetailItemsDone/$detail->orderDetailItemsTotal)*100) }}% @else 0% @endif)</td>
                <td class="text-right">
                    <a target="_blank" href="{{ route('print', ['refererSlug' => 'detail-barcode', 'refererArgument' => $detail->orderDetailId]) }}" style="text-decoration: none; color: inherit; margin-right: 5px;">
                        <i class="fas fa-print"></i>
                    </a>
                    <a style="color: inherit; margin-left: 5px;" title="Edytuj wybrany detal"  href="{{ route('detail.edit', ["id" => $detail->orderDetailId]) }}">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right;">Łącznie:</td>
                @foreach($roles as $role)
                    @foreach($complexWTs as $complex)
                        @if($complex['roleSlug'] == $role->roleSlug)
                            <td scope="col" class="bg-blue">{{ floor($sumTimesUpTable['real'][$role->roleSlug]) }}h {{ round(60*($sumTimesUpTable['real'][$role->roleSlug]-floor($sumTimesUpTable['real'][$role->roleSlug]))) }} minut / {{ $complex['workTimingFinal'] }}h</td>
                        @endif
                    @endforeach
                    {{-- <td scope="col" class="bg-primary">{{ floor($sumTimesUpTable['real'][$role->roleSlug]) }}h {{ round(60*($sumTimesUpTable['real'][$role->roleSlug]-floor($sumTimesUpTable['real'][$role->roleSlug]))) }} minut / {{ $sumTimesUpTable["estimated"][$role->roleSlug]["hours"] }}h {{ $sumTimesUpTable["estimated"][$role->roleSlug]["minutes"] }}m</td> --}}
                @endforeach
                <td colspan="2">
                </td>
            </tr>
            <tr>
                <td colspan="100%" style="text-align: right;"><sup>Ostatnia aktualizacja: <?php echo(date("H:i d.m.Y")) ?></sup></td>
            </tr>
        </table>
    </div>
</div>
@stop


@include('admin.details.partials.create-modal')
@include('admin.orders.partials.add-attachment-modal')


@section('css')
    <style>
        .table-noborder{
            border-radius: 0.25rem;
        }
        .table-noborder th,
        .table-noborder td{
            border: none!important;
        }
        /* Disable number arrows */
        /*
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        */
        /* Firefox */
        /*
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