@extends('adminlte::page')

@section('title', 'Rozliczenie pracownika')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif
@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif


@section('content')
    <div class="pb-5">
        <table class="table mb-0">
            <tr>
                <td>
                    <h4 style="opacity: 0.6; display: inline-block;"><span style="display: block; transform: translateY(5px);">Pracownik: {{ $employee->name }}</span></h4>
                </td>
                <td class="text-center">
                    <a style="display: inline-block;" href="/{{ str_replace(".", "/", $currentRoute) }}?date={{ $previousMonthDate }}"><button type="button" class="btn btn-sm btn-outline-secondary bigArrow">🠔</button></a>
                    <h4 style="opacity: 0.6; display: inline-block; padding-left: 20px; padding-right: 20px;"><span style="display: block; transform: translateY(5px);">Okres: {{ $currentDateHuman }}</span></h4>
                    <a href="/{{ str_replace(".", "/", $currentRoute) }}?date={{ $nextMonthDate }}"><button type="button" class="btn btn-sm btn-outline-secondary bigArrow">🠖</button></a>
                </td>
                <td class="text-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createHolidayOrSickLeaveModal" style="margin-left: 15px;">Zdefiniuj urlop/zwolnienie</button>
                </td>
            </tr>
        </table>
        <h3>Informacje o pracowniku</h3>
        <table class="table table-hover table-noborder bg-white">
            <thead>
                <tr class="bg-gradient">
                    <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                    <th scope="col" class="bg-primary">Pracownik</th>
                    <th scope="col" class="bg-primary">Wymiar pracy (etat)</th>
                    <th scope="col" class="bg-primary">Wymagana liczba godzin</th>
                    <th scope="col" class="bg-primary">Aktualna liczba godzin</th>
                    <th scope="col" class="bg-primary" style="border-top-right-radius: 0.25rem;">Czas ponadwymiarowy</th>
                </tr>
            </thead>
            <tbody>
                <tr style="cursor: pointer;">
                    <td>{{ $employee->id }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->partTimeJobHuman }}</td>
                    <td>{{ $employee->requiredWorkedTime }}</td>
                    <td>{{ $employee->totalWorkedTime[0] }}h {{ $employee->totalWorkedTime[1] }}min {{ $employee->totalWorkedTime[2] }}sek</td>
                    <td>{{ $employee->overWorkedTime[0] }}h {{ $employee->overWorkedTime[1] }}min {{ $employee->overWorkedTime[2] }}sek</td>
                </tr>
            </tbody>
        </table>
        <h3>Szczegółowy miesięczny czas pracy</h3>
        <table class="table table-hover table-noborder bg-white">
            <thead>
                <tr class="bg-gradient">
                    <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                    <th scope="col" class="bg-primary">Pracownik</th>
                    <th scope="col" class="bg-primary">Rozpoczęcie pracy</th>
                    <th scope="col" class="bg-primary">Zakończenie pracy</th>
                    <th scope="col" class="bg-primary">Wymagana ilość godzin</th>
                    <th scope="col" class="bg-primary">Ilość przepracowanych godzin</th>
                    <th scope="col" class="bg-primary">Płatny (%)</th>
                    <th scope="col" class="bg-primary text-right">Akcja</th>
                    {{--<th scope="col" class="bg-primary">Dzienny bilans godzinowy</th>
                    <th scope="col" class="bg-primary" style="border-top-right-radius: 0.25rem;">Ogólny bilans godzinowy</th>--}}
                </tr>
            </thead>
            <tbody>
                <?php
                    $totalWorkingInteger = 0;
                ?>
                @if(empty($employee->settlements[0]))
                    <tr>
                        <td colspan="100%" class="text-center">Brak wyników w bieżącym miesiącu</td>
                    </tr>
                @else
                    @foreach($employee->settlements as $settlement)
                        @if(isset($settlement->workTimingMeta))
                            @if($settlement->workTimingMeta->breakType == 'holiday')
                                @php
                                    $backgroundColor = '#00cc0022';
                                @endphp
                            @elseif($settlement->workTimingMeta->breakType == 'sickleave')
                                @php
                                    $backgroundColor = '#00cccc22';
                                @endphp
                            @else
                                @php
                                    $backgroundColor = '#fff';
                                @endphp
                            @endif
                        @else
                            @php
                                $backgroundColor = '#fff';
                            @endphp
                        @endif
                        <tr style="background-color: {{ $backgroundColor }}">
                            <td>{{ $settlement->workTimingId }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ date("Y-m-d H:i:s", $settlement->workTimingStart) }}</td>
                            <td>
                                @if($settlement->workTimingEnd !== null)
                                    {{ date("Y-m-d H:i:s", $settlement->workTimingEnd) }}
                                @else
                                    <form method="POST" action="{{ route("accounting.forceStopTime") }}" >
                                        @csrf
                                        <input type="hidden" name="forceStopMe" value="{{ $settlement->workTimingId }}" />
                                        <input type="submit" class="btn btn-sm btn-primary" value="Zakończ" />
                                    </form>
                                @endif
                            </td>
                            
                            @if( isset($settlement->workTimingMeta) && ($settlement->workTimingMeta->breakType == 'holiday' || $settlement->workTimingMeta->breakType == 'sickleave'))
                                {{-- If holiday or sickleave --}}
                                <td>{{ $settlement->workTimingFinalHuman[0] }}h {{ $settlement->workTimingFinalHuman[1] }}m {{ $settlement->workTimingFinalHuman[2] }}s</td>
                            @else
                                <td>{{ ($employee->partTimeJob * 8) }}</td>
                            @endif

                            <td>{{ $settlement->workTimingFinalHuman[0] }}h {{ $settlement->workTimingFinalHuman[1] }}m {{ $settlement->workTimingFinalHuman[2] }}s</td>
                            <td>
                                @if(isset($settlement->workTimingMeta->paidPercentCalculated))
                                    {{ $settlement->workTimingMeta->paidPercentCalculated }}%
                                @else
                                    n.d.
                                @endif
                            </td>
                            <td class="text-right">
                                @if($settlement->workTimingEnd !== null)
                                    <i class="fas fa-edit" data-bs-toggle="modal" data-bs-target="#editWorktimeModal" data-bs-worktimingId="{{ $settlement->workTimingId }}" data-bs-worktimingStart="{{ date("Y-m-d\TH:i:s", $settlement->workTimingStart) }}" data-bs-worktimingStop="{{ date("Y-m-d\TH:i:s", $settlement->workTimingEnd) }}" ></i>
                                @endif
                            </td>
                            <?php
                            /*
                            @if(7-$settlement->workTimingFinalHuman[0] < 0) 
                                {{-- Nadgodziny --}}
                                {{-- <td class="text-success">{{ $settlement->workTimingFinalHuman[0]-8 }}h {{ $settlement->workTimingFinalHuman[1] }}m {{ $settlement->workTimingFinalHuman[2] }}s</td> --}} {{-- Przed nadgodzinami zmiennymi --}}
                                <td class="text-success">{{ $settlement->workTimingFinalHuman[0]-(8 * $employee->partTimeJob) }}h {{ $settlement->workTimingFinalHuman[1] }}m {{ $settlement->workTimingFinalHuman[2] }}s</td>
                                <?php
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[2];
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[1]*60;
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[0]*3600;
                                    $totalWorkingInteger -= (28800 * $employee->partTimeJob);
                                ?>
                            @else 
                                {{-- Podgodziny --}}
                                <?php      
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[2];
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[1]*60;
                                    $totalWorkingInteger += $settlement->workTimingFinalHuman[0]*3600;
                                    $totalWorkingInteger -= (28800 * $employee->partTimeJob);
                                ?>
                                {{-- <td class="text-danger">{{ 7-$settlement->workTimingFinalHuman[0] }}h {{ 59-$settlement->workTimingFinalHuman[1] }}m {{ (59-$settlement->workTimingFinalHuman[2])+1 }}s</td> --}} {{-- Przed nadgodzinami zmiennymi --}}
                                @if($totalWorkingInteger >= ($employee->partTimeJob * 8) * 60 * 60)
                                    <?php
                                        $baseHours = ($settlement->workTimingFinalHuman[0]-(8*$employee->partTimeJob));
                                    ?>
                                    @if($settlement->workTimingEnd != null)
                                        <td class="@if($baseHours >= 0) text-success @else text-danger  @endif">{{ $baseHours }}h {{ $settlement->workTimingFinalHuman[1] }}m {{ ($settlement->workTimingFinalHuman[2]) }}s</td>
                                    @else
                                        <td class="text-danger">{{ ((8*$employee->partTimeJob)-1)-$settlement->workTimingFinalHuman[0] }}h {{ 59-$settlement->workTimingFinalHuman[1] }}m {{ (59-$settlement->workTimingFinalHuman[2])+1 }}s</td>
                                    @endif
                                    
                                @else
                                    <td class="text-danger">{{ ((8*$employee->partTimeJob)-1)-$settlement->workTimingFinalHuman[0] }}h {{ 59-$settlement->workTimingFinalHuman[1] }}m {{ (59-$settlement->workTimingFinalHuman[2])+1 }}s</td>
                                @endif
                            @endif
                            <?php
                                $totalWorkingHours = floor(abs($totalWorkingInteger) / 3600);
                                $totalWorkingMinutes = floor(abs($totalWorkingInteger) / 60 % 60);
                                $totalWorkingSeconds = floor(abs($totalWorkingInteger) % 60);
                            ?>
                            @if($totalWorkingInteger >= 0)  
                                {{-- Nadgodziny --}}
                                <td class="text-success">{{ $totalWorkingHours }}h {{ $totalWorkingMinutes }}m {{ $totalWorkingSeconds }}s</td>
                            @else 
                                {{-- Podgodziny --}}
                                <td class="text-danger">{{ $totalWorkingHours }}h {{ $totalWorkingMinutes }}m {{ $totalWorkingSeconds }}s</td>
                            @endif
                            */
                            ?>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @include('admin.accounting.modifyWorktimeModal')
    @include('admin.accounting.createHolidayOrSickLeaveModal')
@stop


@section('css')
<style>
    table{
        width: 100%;
    }
    .bigArrow{
        font-size: 18px;
        border-radius: 20px;
        border: none;
    }
</style>
@stop

@section('js')
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