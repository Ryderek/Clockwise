@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif
@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif


@section('content')
    <style>
        body{
            overflow-y: auto!important;
        }
    </style>
    <div class="row">

        @if($Notifications != null)
        <div class="col-12 col-md-6">
            <h3 class="mt-3">Najnowsze powiadomienia:</h3>
            <table class="table table-hover table-noborder bg-white">
                <tr class="bg-gradient">
                    <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                    <th class="bg-primary">Zgłaszający</th>
                    <th class="bg-primary" style="border-top-right-radius: 0.25rem;">Treść</th>
                </tr>
                
                @if(!isset($Notifications ) || $Notifications  == null || count($Notifications) == 0)
                    <tr>
                        <td colspan="100%" class="text-center">
                            <span>Brak powiadomień na ten moment</span><br />
                        </td>
                    </tr>
                @else
                    @foreach($Notifications as $notifis)
                        <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#dismissNotificationModal" data-bs-dismissId="{{ $notifis->notificationId }}"  data-bs-claimant="{{ $notifis->name }}" data-bs-date="{{ $notifis->created_at }}" data-bs-content="{{ html_entity_decode($notifis->notificationContent, ENT_QUOTES, "UTF-8") }}">
                            <td>{{ $notifis->notificationId }}</td>
                            <td>{{ $notifis->name }}</td>
                            <td>{{ $notifis->notificationContent }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="100%" class="text-center">
                            <a href="{{ route('notifications') }}"><input type="button" class="btn-sm btn-primary mt-1" value="Zobacz więcej" /></a>
                        </td>
                    </tr>
                @endif
                
            </table>
        </div>
        @endif

        @if($orders != null)
        <div class="col-12 col-md-6">
            <h3 class="mt-3">Nadchodzące deadline'y:</h3>
            <table class="table table-hover table-noborder bg-white">
                <thead>
                    <tr class="bg-gradient">
                        <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                        <th scope="col" class="bg-primary" >Zamówienie</th>
                        <th scope="col" class="bg-primary" >Kontrahent</th>
                        <th scope="col" class="bg-primary" >Deadline</th>
                        <th scope="col" class="text-right bg-primary" style="border-top-right-radius: 0.25rem;" >Akcja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="{{ $order->orderClasses }}">
                            <th scope="row">{{ $order->orderId }}</th>
                            <td>{{ $order->orderName }}</td>
                            <td>{{ $order->customer->customerName }}</td>
                            <td>{{ substr($order->orderDeadline, 0, 10) }} (@if($order->orderDateDiff >= 0) za {{ $order->orderDateDiff }} dni @else {{ abs($order->orderDateDiff) }} dni temu @endif)</td>
                            <td class="text-right">
                                <!-- <a style="color: inherit;" title="Podgląd" href="/admin/overview/{{ $order->orderId }}">
                                    <i class="fas fa-table"></i>
                                </a> &nbsp; &nbsp; -->
                                <a style="color: inherit;" title="Wykres Gantta" href="{{ route('gantt', [ 'id' => $order->orderId ]) }}">
                                    <i class="fas fa-chart-pie"></i>
                                </a> &nbsp; &nbsp; 
                                @if($order->orderConfirmedBy == NULL)
                                    <a style="color: inherit;" href="{{ route('order.edit', ["id" => $order->orderId ]) }}"  data-bs-toggle="modal" data-bs-target="#pushOrder" data-bs-order-id="{{ $order->orderId }}" data-bs-order-name="{{ $order->orderName }}" data-bs-customer-name="{{ $order->customer->customerName }}" data-bs-push-header="Zatwierdzanie zlecenia" data-bs-tab-name="<?php //Zatwierdzone ?>W produkcji" ><i class="fas fa-check"></i></a> &nbsp; &nbsp; 
                                @elseif($order->orderPublishedBy == NULL)
                                    <a style="color: inherit;" href="{{ route('order.edit', ["id" => $order->orderId ]) }}"  data-bs-toggle="modal" data-bs-target="#pushOrder" data-bs-order-id="{{ $order->orderId }}" data-bs-order-name="{{ $order->orderName }}" data-bs-customer-name="{{ $order->customer->customerName }}" data-bs-push-header="Rozpoczęcie produkcji" data-bs-tab-name="W produkcji" ><i class="fas fa-wrench"></i></a> &nbsp; &nbsp; 
                                @elseif($order->orderDoneBy == NULL)
                                    <a style="color: inherit;" href="{{ route('order.edit', ["id" => $order->orderId ]) }}"  data-bs-toggle="modal" data-bs-target="#pushOrder" data-bs-order-id="{{ $order->orderId }}" data-bs-order-name="{{ $order->orderName }}" data-bs-customer-name="{{ $order->customer->customerName }}" data-bs-push-header="Zakończenie produkcji" data-bs-tab-name="Zakończone" ><i class="fas fa-check-square"></i></a> &nbsp; &nbsp; 
                                @endif
                                <a style="color: inherit;" title="Edytuj wybrane zlecenie" href="{{ route('order.edit', ["id" => $order->orderId ]) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($tools != null)
        <div class="col-12 col-md-6">
            <h3 class="mt-3">Ostatnio uszkodzone narzędzia:</h3>
            <table class="table table-hover table-noborder bg-white">
                <thead>
                    <tr class="bg-gradient">
                        <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                        <th scope="col" class="bg-primary">Narzędzie</th>
                        <th scope="col" class="bg-primary">Status</th>
                        <th scope="col" class="bg-primary">Opis</th>
                        <th scope="col" class="bg-primary">Ostatnia aktualizacja</th>
                        <th scope="col" class="text-end bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tools as $tool)
                        <tr class="{{ $tool->toolClasses }}">
                            <th scope="row">{{ $tool->toolId }}</th>
                            <td>{{ $tool->toolName }}</td>
                            <td>{{ $tool->toolStatusMnemonic }}</td>
                            <td>
                                @if($tool->toolStatus == "damaged") 
                                    Uszkodzony od {{ abs($tool->toolLastRepaired) }} dni 
                                @elseif($tool->toolStatus == "workbench") 
                                    W naprawie od {{ abs($tool->toolLastRepaired) }} dni
                                @elseif($tool->toolStatus == "available") 
                                    Dni od ostatniej naprawy: {{ abs($tool->toolLastRepaired) }}
                                @endif
                            </td>
                            <td>{{ substr($tool->updated_at, 0, 32) }}</td>
                            <td class="text-right">
                                <a style="color: inherit;" title="Usuń wybrane narzędzie" data-bs-toggle="modal" data-bs-target="#removeToolModal" data-bs-deleteToolId="{{ $tool->toolId }}" data-bs-removeToolName="{{ $tool->toolName }}">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a style="color: inherit;" title="Edytuj wybrane narzędzie" href="{{ route('tool.edit', [ 'id' => $tool->toolId ]) }}"> &nbsp; &nbsp; 
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($WorkTimeHistory != null)
        <div class="col-12 col-md-6">
            <h3 class="mt-3">Ostatnie operacje:</h3>
            <table class="table table-hover table-noborder bg-white">
                <tr class="bg-gradient">
                    <th class="bg-primary" scope="col" style="border-top-left-radius: 0.25rem;">#</th>
                    <th class="bg-primary">Komentarz</th>
                    <th class="text-right bg-primary" style="border-top-right-radius: 0.25rem;">Utworzono</th>
                </tr>
                @if(!isset($WorkTimeHistory ) || $WorkTimeHistory  == null)
                    <tr>
                        <td colspan="100%" class="text-center">Nie rozpoczęto jeszcze procesu produkcji</td>
                    </tr>
                @endif
                @foreach($WorkTimeHistory as $WTH)
                <tr>
                    <td>{{ $WTH->workTimingHistoryId }}</td>
                    <td>{{ $WTH->workTimingHistoryDescriptor }}</td>
                    <td class="text-right">{{ $WTH->updated_at }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
        
    </div>
    
@include('admin.orders.partials.push-modal')
@stop

@include('admin.partials.dismiss-notification')

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    const pushOrder = document.getElementById('pushOrder')
    pushOrder.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const customer = button.getAttribute('data-bs-customer-name')
        const order = button.getAttribute('data-bs-order-name')
        const orderId = button.getAttribute('data-bs-order-id')
        const orderHeader = button.getAttribute('data-bs-push-header')
        const orderTabName = button.getAttribute('data-bs-tab-name')
        pushOrder.querySelector('#pushOrderOrderName').textContent = order
        pushOrder.querySelector('#pushOrderCustomerName').textContent = customer
        pushOrder.querySelector('#pushOrderWithId').value = orderId
        pushOrder.querySelector('#pushOrderLabel').textContent = orderHeader
        pushOrder.querySelector('#pushOrderTabName').textContent = orderTabName
    })
</script>
@stop
<?php

/*
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

*/ 
?>