@extends('adminlte::page')

@section('title', 'Wydawanie Zleceń')

@section('content_header')
    @if(!$displayDetials)
        <h1>Wydawanie Zleceń</h1>
    @else
        <div class="row">
            <div class="col-12 col-md-6">
                <h1>Wydawanie Zleceń</h1>
            </div>
            <div class="col-12 col-md-6 text-right">
                <a href="{{ route('admin.deployment') }}"><button type="button" class="btn btn-secondary" id="afterscanBackButton">Powrót</button></a>
            </div>
        </div>
    @endif
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif


@section('content')
    @if($displayDetials)
        <form method="POST" action="{{ route('admin.deployment.generate', ["orderId" => $OrderId]) }}">
            @csrf
            <table class="table table-hover table-noborder bg-white">
                <thead>
                    <tr class="bg-gradient">
                        <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem; width: 15px;"><input type="checkbox" onclick="checkUncheck(this, 'selectedDetails');"  /></th>
                        <th scope="col" class="bg-primary" >#</th>
                        <th scope="col" class="bg-primary" >Kod detalu</th>
                        <th scope="col" class="bg-primary" >Nazwa detalu</th>
                        <th scope="col" class="bg-primary" >Wyprodukowanych</th>
                        <th scope="col" class="bg-primary" >Wszystkich</th>
                        <th scope="col" class="bg-primary" >Wydanych</th>
                        <th scope="col" class="text-right bg-primary" style="border-top-right-radius: 0.25rem;" >Wydawanie</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- DISPLAY DETAILS LIST --}}
                    @foreach($Details as $Detail)
                    <tr>
                        <td style="width: 15px;">
                            <input type="checkbox" name="selectDetail[{{ $Detail->orderDetailId }}]" class="selectedDetails" />
                        </td>
                        <td>{{ $Detail->orderDetailOrderNumber }}</td>
                        <td>{{ $Detail->orderDetailUniqueId }}</td>
                        <td>{{ $Detail->orderDetailName }}</td>
                        <td>{{ $Detail->orderDetailItemsDone }}</td>
                        <td>{{ $Detail->orderDetailItemsTotal }}</td>
                        <td>{{ $Detail->orderDetailItemsDeployed }}</td>
                        <td class="text-right">
                            <a style="color: #000;" href="{{ route('admin.deployment.deployDetail', ["orderId" => $OrderId, "detailId" => $Detail->orderDetailId]) }}"<i class="fas fa-paper-plane"></i>
                        </td>
                    
                    </tr>
                    @endforeach
                            
                </tbody>
            </table>
            <div class="text-center">
                <input type="submit" formtarget="_blank" class="btn btn-primary" style="margin-left: 15px;" name="generateForm" value="Wygeneruj WZ" /> 
                <input type="submit" formtarget="_blank" class="btn btn-primary" style="margin-left: 15px;" name="generateLabels" value="Wygeneruj Etykiety" /> 
            </div>
        </form>
        
    @else
    
        {{-- DISPLAY ORDERS LIST --}}
        <table class="table table-hover table-noborder bg-white">
            <thead>
                <tr class="bg-gradient">
                    <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                    <th scope="col" class="bg-primary" >Zamówienie</th>
                    <th scope="col" class="text-right bg-primary" style="border-top-right-radius: 0.25rem;" >Akcja</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Orders as $Order)
                {{-- dd($Order); --}}
                    <tr class="{{ $Order->orderClasses }}">
                        <th scope="row">{{ $Order->orderId }}</th>
                        <td>
                            {{ $Order->orderName }}
                            @if($Order->orderCooperated == 1)
                                <span class="statusLamp" style="background: rgb(204, 102, 0); display: inline-block; transform: translateY(1px); line-height: 12px; text-align: center; color: #fff;">©</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a style="color: inherit;" title="Edytuj wybrane zlecenie" href="{{ route('admin.deployment', ["orderId" => $Order->orderId ]) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    @endif
@stop

@section('css')
@stop

@section('js')
<script>
    function checkUncheck(checkbox, checkboxName) {
        get = document.getElementsByClassName(checkboxName);
        for(var i=0; i<get.length; i++) {
            get[i].checked = checkbox.checked;
        }

    }
    </script>
@stop