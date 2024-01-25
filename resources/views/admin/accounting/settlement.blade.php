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
                <th scope="col" class="bg-primary">Pracownik</th>
                <th scope="col" class="bg-primary">Wymiar pracy (etat)</th>
                <th scope="col" class="bg-primary">Wymagana liczba godzin</th>
                <th scope="col" class="bg-primary">Aktualna liczba godzin</th>
                <th scope="col" class="bg-primary" style="border-top-right-radius: 0.25rem;">Nadgodziny</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr style="cursor: pointer;" onClick="document.location.href='/{{ str_replace(".", "/", $userRoute) }}/{{ $employee->id }}?date={{ $currentDate }}'">
                    <td>{{ $employee->id }}</td>
                    <td>
                        {{ $employee->name }}
                        @if(date("Y-m", strtotime($currentDate)) == date("Y-m"))

                            @if($employee->amIAtWork)
                                <div style="width: 10px; height: 10px; background: #0f0; border-radius: 50%; display: inline-block; transform: translate(5px, 8px);">&nbsp;</div>
                            @else
                                <div style="width: 10px; height: 10px; background: #f00; border-radius: 50%; display: inline-block; transform: translate(5px, 8px);">&nbsp;</div>
                            @endif

                        @endif
                    </td>
                    <td>{{ $employee->partTimeJobHuman }}</td>
                    <td>{{ $employee->requiredWorkedTime }}</td>
                    <td>{{ $employee->totalWorkedTime[0] }}h {{ $employee->totalWorkedTime[1] }}min {{ $employee->totalWorkedTime[2] }}sek</td>
                    <td>{{ $employee->overWorkedTime[0] }}h {{ $employee->overWorkedTime[1] }}min {{ $employee->overWorkedTime[2] }}sek</td>
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