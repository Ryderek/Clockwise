@extends('adminlte::page')

@section('title', 'OrderDetail')

@section('css')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
        #barCodeHolder > div{
            margin-left: auto;
            margin-right: auto;
            height: 50px!important;
        }
        #barCodeHolder > div > div{
            height: 50px!important;
        }
    </style>
@stop

@section('content_header')
<div class="row">
    <div class="col-12 col-md-6">
        <h1>Detal #{{ $detail->orderDetailId }} {{ $detail->orderDetailName }} <span class="text-secondary" style="font-size: 12px;">({{ $detail->orderDetailUniqueId }})</span></h1>
    </div>
    <div class="col-12 col-md-6 text-right">
        @if($order->orderStatus == "created")
            <a href="{{ route('detail.remove', ['id' => $detail->orderDetailId]) }}" style="text-decoration: none; color: inherit; margin-right: 5px;">
                <button type="button" class="btn btn-danger">&nbsp;<i class="fas fa-trash"></i>&nbsp;</button>
            </a>
        @endif

        <button type="button" class="btn btn-primary" data-bs-toggle="collapse" href="#detailCollapser" role="button" aria-expanded="false" data-bs-target="#details-collapse" aria-controls="details-collapse">Szczegóły detalu</button>
        <a href="{{ route('order.edit', ["id" => $detail->orderDetailOrderId ]) }}"><button type="button" class="btn btn-dark" role="button">Powrót do zamówienia</button></a>
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

<div class="w-100 row p-2 pr-0">
    <div class="col-12 mb-5 collapse" id="details-collapse">
        <div class="row">
            <section class="col-12 pb-2 bg-white">
                <form action="{{ route('detail.update') }}" method="POST" autocomplete="off" class="row px-2 mb-0" >
                    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
                    <input style="display: none;" type="hidden" value="{{ $detail->orderDetailId }}" readonly name="orderDetailId" />
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                            <h4 class="m-0 text-white">Szczegóły detalu</h4>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailName" class="form-label">Nazwa detalu</label>
                            <input type="text" class="form-control" id="orderDetailName" name="orderDetailName" value="{{ $detail->orderDetailName }}" placeholder="Podaj nazwę detalu" maxlength="127" required>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailItemsTotal" class="form-label">Ilość sztuk</label>
                            <a target="_blank"><input type="number" class="form-control" id="orderDetailItemsTotal" name="orderDetailItemsTotal" value="{{ $detail->orderDetailItemsTotal }}" maxlength="20" minlength="3" required></a>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <a href="{{ route('print', ['refererSlug' => 'detail-barcode', 'refererArgument' => $detail->orderDetailId]) }}" style="text-decoration: none; color: inherit;">
                                <div class="text-center" id="barCodeHolder" style="margin-left: auto; margin-right: auto;">
                                    <?php echo($barcode); ?>
                                    {{ $detail->orderDetailUniqueId }}
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailCooperation" class="form-label">
                                Koopreacja<br />
                                <input type="checkbox" id="orderDetailCooperation" name="orderDetailCooperation" @if($detail->orderDetailCooperation == 1) checked @endif >
                            </label>
                        </div>
                        <div class="col-12"></div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailUnitProductionCost" id="orderDetailUnitProductionCostLabel" class="form-label">Jednostkowy koszt produkcji detalu</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control text-right" step="0.01" id="orderDetailUnitProductionCost" name="orderDetailUnitProductionCost" value="{{ $detail->orderDetailUnitProductionCost }}" placeholder="Podaj koszt produkcji jednej jednostki" aria-label="Koszt produkcji jednej jednostki" aria-describedby="orderDetailUnitProductionCostDescriptor">
                                <span class="input-group-text" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" id="orderDetailUnitProductionCostDescriptor">PLN</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailUnitSellValue" class="form-label">Jednostkowa cena sprzedaży</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control text-right" step="0.01" id="orderDetailUnitSellValue" name="orderDetailUnitSellValue" value="{{ $detail->orderDetailUnitSellValue }}" placeholder="Podaj cenę sprzedaży jednej jednostki" required aria-describedby="orderDetailUnitSellValueDescriptor">
                                <span class="input-group-text" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" id="orderDetailUnitSellValueDescriptor">PLN</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailWorth" id="orderDetailWorthLabel" class="form-label">Wartość</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control text-right" id="orderDetailWorth" name="orderDetailWorth" value="{{ $detail->orderDetailItemsTotal * $detail->orderDetailUnitSellValue }}" placeholder="Podaj koszt produkcji jednej jednostki" aria-label="Koszt produkcji jednej jednostki" aria-describedby="orderDetailWorthDescriptor" disabled>
                                <span class="input-group-text" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" id="orderDetailWorthDescriptor">PLN</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="orderDetailPainting" class="form-label">Lakierowanie</label>
                            <input type="number" class="form-control" id="orderDetailPainting" name="orderDetailPainting" value="{{ $detail->orderDetailPainting }}" />
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary m-0" value="Zapisz zaminy" />
                </form>
            </section>
            <section class="col-12 col-md-6 p-2 pr-md-3">
                <div class="row px-2 py-2 bg-white rounded">
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
                        <a href="{{ route('note.create', ['relator' => 'detail', 'relatorId' => $detail->orderDetailId]) }}"><button type="submit" class="btn btn-primary">Dodaj notatkę</button></a>
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
    
    <div class="col-12 mb-0 py-2 pl-4">
        <h4 class="m-0">Zdefiniowane opcje obróbki</h4>
    </div>
    <div class="col-12 mb-1 p-0">
        <table class="table table-hover table-noborder bg-white">
            <tr class="bg-gradient">
                <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">Lp.</th>
                <th class="bg-primary" scope="col">#</th>
                <th class="bg-primary">Obróbka</th>
                {{--
                <th class="bg-primary">Jednostkowy czas obróbki</th>
                <th class="bg-primary">Całościowy czas obróbki</th>
                --}}
                <th class="bg-primary">Obrobione detale</th>
                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
            @if(!isset($estimatedWorkTimings) || $estimatedWorkTimings == null)
                <tr>
                    <td colspan="100%" class="text-center">Brak wprowadzonych obróbek</td>
                </tr>
            @else
                <?php $countId = 0; ?>
                @foreach($estimatedWorkTimings as $eWT)            
                    <?php $countId++; ?>
                    <tr>
                        <td>{{ $countId }}</td>
                        <td>{{ $eWT->workTimingId }}</td>
                        <td>{{ $eWT->roleProcess }}</td>
                        {{--
                        <td>{{ $eWT->workTimingFinal }} minut</td>
                        <td>{{ $eWT->totalTimeInHuman }} </td>
                        --}}
                        <td>{{ $eWT->realTime }} / {{ $detail->orderDetailItemsTotal }}</td>
                        <td></td>
                    </tr>
                @endforeach
            @endif
        </table>
        <div class="col-12 col-md-6 mb-2 text-left">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorktimingModal">Dodaj obróbkę</button>
        </div>
    </div>
    <?php $total_rta = 0; ?>
    @if(isset($estimatedWorkTimings) && $estimatedWorkTimings != null)
        @foreach($estimatedWorkTimings as $eWT)
            @foreach($eWT->realTimesArray as $rt)
                @if($rt->workTimingEnd == null)
                    <?php $total_rta += count($eWT->realTimesArray); ?>
                @endif
            @endforeach
        @endforeach
    @endif
    @if($total_rta != 0)
        <div class="col-12 mb-0 py-2 pl-4">
            <h4 class="m-0">Aktywne sesje pracownicze:</h4>
        </div>
        <div class="col-12 mb-5 p-0">
            <table class="table table-hover table-noborder bg-white">
                <tr class="bg-gradient">
                    <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                    <th class="bg-primary">Pracownik</th>
                    <th class="bg-primary">Typ obróbki</th>
                    <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Aktualny czas pracy</th>
                </tr>
                @if(!isset($estimatedWorkTimings) || $estimatedWorkTimings == null)
                    <tr>
                        <td colspan="100%" class="text-center">Brak aktualnie wykonywanych obróbek</td>
                    </tr>
                @else
                    <?php
                        $alreadyDisplayedIdsArray = array();
                    ?>
                    @foreach($estimatedWorkTimings as $eWT)
                        @foreach($eWT->realTimesArray as $rt)
                            @if($rt->workTimingEnd == null && !in_array($rt->workTimingId, $alreadyDisplayedIdsArray))
                                <?php array_push($alreadyDisplayedIdsArray, $rt->workTimingId) ?>
                                <tr>
                                    <td>{{ $rt->workTimingId }}</td>
                                    <td>{{ $rt->name }}</td>
                                    <td>{{ $eWT->roleProcess }}</td>
                                    <td id="workTiming{{$rt->workTimingId}}Time" class="text-right">{{ $rt->workTimingStart }}</td>
                                </tr>
                                <script> calculateTimeProcess({{ $rt->workTimingStart }}, $("#workTiming{{$rt->workTimingId}}Time")); </script>
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </table>
        </div>
    @endif
    <div class="col-12 mb-0 mt-0 py-2 pl-4">
        <h4 class="m-0">Historia działań</h4>
    </div>
    <div class="col-12 mb-1 p-0 mt-0">
        <table class="table table-hover table-noborder bg-white">
            <tr class="bg-gradient">
                <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                <th class="bg-primary">Komentarz</th>
                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Utworzono</th>
            </tr>
            @if(!isset($WorkTimeHistory ) || $WorkTimeHistory  == null || $WorkTimeHistory->count() == 0)
                <tr>
                    <td colspan="100%" class="text-center">Nie rozpoczęto jeszcze procesu produkcji</td>
                </tr>
            @endif
            @foreach($WorkTimeHistory as $WTH)
            <tr>
                <td>{{ $WTH->workTimingHistoryId }}</td>
                <td>{{ $WTH->workTimingHistoryDescriptor }}</td>
                <td class="text-right">{{ $WTH->created_at }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@stop


@include('admin.details.partials.add-attachment-modal')
@include('admin.details.partials.estimated-worktime-modal')