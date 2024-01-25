@extends('adminlte::page')

@section('title') {{$pageHeader}} @stop

@section('content_header')
    @if($displayGanttChart)
        <div class="row">
            <div class="col-12 col-md-6">
                <h1>{{ $pageHeader }}</h1>
            </div>
            <div class="col-12 col-md-6 text-right">
                <a href="{{ route('gantt-reasume') }}">
                    <button class="btn btn-primary mt-1">
                        <i class="fas fa-chart-pie" ></i>
                    </button>
                </a>
            </div>
        </div>
    @else
        <h1>{{ $pageHeader }}</h1>
    @endif
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')
    <table class="table table-hover table-noborder bg-white">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary" >Zamówienie (Kontrahent)</th>
                <th scope="col" class="bg-primary" >Operator</th>
                <th scope="col" class="bg-primary" >AF</th>
                @foreach($roles as $role)
                    <th scope="col" class="bg-primary">{{ $role->roleProcess }}</th>
                @endforeach
                {{-- DISABLE COLUMNS 
                    <th scope="col" class="bg-primary" >Deadline</th>
                <th scope="col" class="bg-primary" >Wartość</th>
                <th scope="col" class="bg-primary" >Utworzono</th>
                <th scope="col" class="bg-primary" >Zaktualizowano</th> --}}
                <th scope="col" class="bg-primary text-center" >Status</th>
                <th scope="col" class="text-right bg-primary" style="border-top-right-radius: 0.25rem;" >Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            {{-- dd($order); --}}
                @if($ignoreColors)
                    @php
                        $order->orderClasses = str_replace("bg-dark", "", $order->orderClasses)
                    @endphp
                @endif
                <tr class="{{ $order->orderClasses }}">
                    <th scope="row">{{ $order->orderId }}</th>
                    <td>
                        {{ $order->orderName }} ({{ $order->customer->customerName }})
                        @if($order->orderCooperated == 1)
                            <span class="statusLamp" style="background: rgb(204, 102, 0); display: inline-block; transform: translateY(1px); line-height: 12px; text-align: center; color: #fff;">©</span>
                        @endif
                    </td>
                    <td>{{ $order->currentOperator }} ({{ $order->currentOperatorOperation }})</td>
                    <td><span class="copyClick" style="cursor: pointer;">{{ $order->orderAdditionalField }}</span></td>
                    @foreach($roles as $role)
                        @foreach($order->complexWTs as $complex)
                            @if($complex['roleSlug'] == $role->roleSlug)
                                <?php
                                    $roleSlugReal = $role->roleSlug."Real";
                                    $roleSlugEstimated = $role->roleSlug."Estimated";
                                ?>
                                <td>
                                    {{ floor($order->$roleSlugReal/3600) }}h {{ round(60*(($order->$roleSlugReal/3600) - floor($order->$roleSlugReal/3600))) }}m / {{ $complex['workTimingFinal'] }}h
                                </td>
                            @endif
                        @endforeach
                        {{-- <td scope="col" class="bg-primary">{{ floor($sumTimesUpTable['real'][$role->roleSlug]) }}h {{ round(60*($sumTimesUpTable['real'][$role->roleSlug]-floor($sumTimesUpTable['real'][$role->roleSlug]))) }} minut / {{ $sumTimesUpTable["estimated"][$role->roleSlug]["hours"] }}h {{ $sumTimesUpTable["estimated"][$role->roleSlug]["minutes"] }}m</td> --}}
                    @endforeach
                    {{-- // DISABLE COLUMNS
                    <td>{{ substr($order->orderDeadline, 0, 10) }} (@if($order->orderDateDiff >= 0) za {{ $order->orderDateDiff }} dni @else {{ abs($order->orderDateDiff) }} dni temu @endif)</td>
                    <td>{{ $order->orderValue }}</td>
                    <td>{{ substr($order->created_at, 0, 10) }}</td>
                    <td>{{ substr($order->updated_at, 0, 10) }}</td>
                    --}}
                    <td>
                        @if($order->orderStatusLight == 1)
                            <div class="statusLamp" style="background: #0f0;"></div>
                        @else
                            <div class="statusLamp" style="background: #f00;"></div>
                        @endif
                    </td>
                    <td class="text-right">
                        <!-- <a style="color: inherit;" title="Podgląd" href="/admin/overview/{{ $order->orderId }}">
                            <i class="fas fa-table"></i>
                        </a> &nbsp; &nbsp; -->
                        @if($order->orderConfirmedBy == NULL)
                            <a style="color: inherit;" href="{{ route('order.edit', ["id" => $order->orderId ]) }}"  data-bs-toggle="modal" data-bs-target="#pushOrder" data-bs-order-id="{{ $order->orderId }}" data-bs-order-name="{{ $order->orderName }}" data-bs-customer-name="{{ $order->customer->customerName }}" data-bs-push-header="Zatwierdzanie zlecenia" data-bs-tab-name="<?php //Zatwierdzone ?>W produkcji" ><i class="fas fa-check"></i></a> &nbsp; &nbsp; 
                        @elseif($order->orderPublishedBy == NULL)
                            <a style="color: inherit;" href="{{ route('order.edit', ["id" => $order->orderId ]) }}"  data-bs-toggle="modal" data-bs-target="#pushOrder" data-bs-order-id="{{ $order->orderId }}" data-bs-order-name="{{ $order->orderName }}" data-bs-customer-name="{{ $order->customer->customerName }}" data-bs-push-header="Rozpoczęcie produkcji" data-bs-tab-name="W produkcji" ><i class="fas fa-wrench"></i></a> &nbsp; &nbsp; 
                        @elseif($order->orderDoneBy == NULL)
                            {{-- ALTER VERSION: 
                            <a style="color: inherit;" data-bs-toggle="modal" data-bs-target="#reasumeGantt" title="Wykres Gantta" href="#">
                                <i class="fas fa-chart-pie" ></i>
                            </a> &nbsp; &nbsp;
                            --}}  
                            <a style="color: inherit;" title="Wykres Gantta" href="{{ route('gantt', [ 'id' => $order->orderId ]) }}">
                                <i class="fas fa-chart-pie"></i>
                            </a> &nbsp; &nbsp; 
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
    @include('layouts.partials.pagination') 
@stop

@include('admin.orders.partials.push-modal')
@include('admin.orders.partials.gantt-modal')

@section('css')
    <style>
        .table-noborder{
            border-radius: 0.25rem;
        }
        .table-noborder th,
        .table-noborder td{
            border: none!important;
        }
        .statusLamp{
            width: 14px; 
            height: 14px; 
            border-radius: 50%; 
            margin-left: auto; 
            margin-right: auto; 
            margin-top: 4px; 
        }
        .content-wrapper{
            overflow: hidden;
        }
    </style>
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
@section("js-footer")
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
            <strong class="me-auto">Clockwise</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Pomyślnie skopiowano do schowka
            </div>
        </div>
    </div>     
    <script>
        const copyClicks = document.querySelectorAll(".copyClick");

        copyClicks.forEach((copyClick) => {
            copyClick.addEventListener('click', () => { 
                document.execCommand("copy");
            });
            copyClick.addEventListener("copy", function(event) {
                event.preventDefault();
                if (event.clipboardData) {
                    event.clipboardData.setData("text/plain", copyClick.textContent);
                    console.log(event.clipboardData.getData("text"));
                }
            });
        });
       
    </script>
@endsection