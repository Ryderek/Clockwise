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
        <h1>Podgląd zlecenia na żywo</h1>
    </div>
    <div class="col-12 col-md-6 text-right">
        <a href="{{ route('order.edit', ["id" => $orderId ]) }}"><button type="button" class="btn btn-dark" role="button">Powrót do zamówienia</button></a>
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
<div class="w-100 row p-2 pr-0">
    <div class="col-12 mb-0 mt-1 py-2 pl-4">
        <h4 class="m-0">Historia działań</h4>
    </div>
    <div class="col-12 mb-1 p-0 mt-0">
        <table class="table table-hover table-noborder bg-white">
            <tr class="bg-gradient">
                <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                <th class="bg-primary">Komentarz</th>
                <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Utworzono</th>
            </tr>
            @if(!isset($historyItems ) || $historyItems == null)
                <tr>
                    <td colspan="100%" class="text-center">Nie rozpoczęto jeszcze procesu produkcji</td>
                </tr>
            @endif
            @if($historyItems != null)
                @foreach($historyItems as $historyItem)
                <tr>
                    <td>{{ $historyItem->workTimingHistoryId }}</td>
                    <td>{{ $historyItem->workTimingHistoryDescriptor }}</td>
                    <td class="text-right">{{ $historyItem->created_at }}</td>
                </tr>
                @endforeach
            @endif
        </table>
    </div>
    <div class="col-12 mb-0 mt-0 text-right">
        <span>Ostatnia aktualizacja: <?php echo(date("H:i d.m.Y")); ?></span>
    </div>
</div>
<script>
    setInterval("location.reload(true);", 60000); // Refrest each 60 sec
</script>
@stop