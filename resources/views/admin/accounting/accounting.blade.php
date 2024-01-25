@extends('adminlte::page')

@section('title', 'Rozliczenia Kadrowe')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif
@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif


@section('content')
<table class="table mb-0">
    <tr>
        <td>
            <a href="/{{ str_replace(".", "/", $currentRoute) }}?date={{ $previousMonthDate }}"><button type="button" class="btn btn-sm btn-outline-secondary bigArrow">🠔</button></a>
        </td>
        <td class="text-center">
            <h4 style="opacity: 0.6;">{{ $currentDateHuman }}</h4>
        </td>
        <td class="text-right">
            <a href="/{{ str_replace(".", "/", $currentRoute) }}?date={{ $nextMonthDate }}"><button type="button" class="btn btn-sm btn-outline-secondary bigArrow">🠖</button></a>
        </td>
    </tr>
</table>
<table class="table table-hover table-noborder bg-white">
    <thead>
        <tr class="bg-gradient">
            <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
            <th scope="col" class="bg-primary">Zamówienie</th>
            <th scope="col" class="bg-primary">Kontrahent</th>
            <th scope="col" class="bg-primary">Zakończono</th>
            <th scope="col" class="bg-primary">Ilość detali</th>
            <th scope="col" class="bg-primary">Wartość</th>
            <th scope="col" class="bg-primary" style="border-top-right-radius: 0.25rem; text-align: right;">Akcja</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr style="cursor: pointer;" onClick="document.location.href='{{ route('order.edit', ["id" => $order->orderId ]) }}'">
                <td>{{ $order->orderId }}</td>
                <td>{{ $order->orderName }}</td>
                <td>{{ $order->customerName }}</td>
                <td>{{ $order->orderDoneTime }}</td>
                <td>{{ $order->detailsCount }}</td>
                <td>{{ $order->orderValue }}</td>
                <td style="text-align: right;">
                    <a style="color: inherit;" title="Edytuj wybrane zlecenie" href="{{ route('order.edit', ["id" => $order->orderId ]) }}">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
        @endforeach  
    </tbody>
</table> 
@stop


@section('css')
<style>
    table{
        width: 100%;
    }
    .bigArrow{
        font-size: 18px;
    }
</style>
@stop

@section('js')
@stop