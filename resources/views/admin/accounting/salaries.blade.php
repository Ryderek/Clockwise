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
            <form action="/{{ str_replace(".", "/", $currentRoute) }}/{{ $previousMonthDate }}" method="POST">
                @csrf
                <input type="hidden" name="salariesPass" value="{{ $salariesPass }}" />
                <button type="submit" class="btn btn-sm btn-outline-secondary bigArrow">🠔</button>
            </form>
        </td>
        <td class="text-center">
            <h4 style="opacity: 0.6;">{{ $currentDateHuman }}</h4>
        </td>
        <td class="text-right">
            <form action="/{{ str_replace(".", "/", $currentRoute) }}/{{ $nextMonthDate }}" method="POST">
                @csrf
                <input type="hidden" name="salariesPass" value="{{ $salariesPass }}" />
                <button type="submit" class="btn btn-sm btn-outline-secondary bigArrow">🠖</button>
            </form>
        </td>
    </tr>
</table>
<form method="POST" action="{{ route('salaries-update') }}">
    @csrf
<table class="table table-hover table-noborder bg-white">
    <thead>
        <tr class="bg-gradient">
            <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
            <th scope="col" class="bg-primary">Pracownik<br />(etat : wymiar godzinowy)</th>
            <th scope="col" class="bg-primary" style="text-align: center;">Stawka godzinowa<br />(netto)</th>
            <th scope="col" class="bg-primary" style="text-align: center;">Stawka nadgodzinowa<br />(netto)</th>
            <th scope="col" class="bg-primary" style="text-align: center;">Stawka weekendowa<br />i świąteczna (netto)</th>
            <th scope="col" class="bg-primary">Obowiązkowe</th>
            <th scope="col" class="bg-primary">Nadgodziny</th>
            <th scope="col" class="bg-primary">Weekendy i święta</th>
            <th scope="col" class="bg-primary text-right" style="border-top-right-radius: 0.25rem;">Wypłata (netto)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <tr style="cursor: pointer;">
                <td class="pt-2">{{ $employee->id }}</td>
                <td class="pt-2">
                    {{ $employee->name }}
                    ({{ $employee->partTimeJobHuman }} : {{ $employee->requiredWorkedTime }} h)
                </td>
                <td style="text-align: center;">
                    <div class="input-group">
                        <input style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0;" type="text" class="form-control text-right" aria-label="DefaultWage" aria-describedby="default{{ $employee->id }}Wage" type="number" step="0.01" value="{{ $employee->employeeDefaultWage }}" name="employeeWage[{{ $employee->id }}][employeeDefaultWage]" placeholder="Wprowadź stawkę" min="0">
                        <span style="border-top-left-radius: 0; border-bottom-left-radius: 0;" class="input-group-text" id="default{{ $employee->id }}Wage">zł</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <div class="input-group">
                        <input style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0;" type="text" class="form-control text-right" aria-label="DefaultWage" aria-describedby="overtime{{ $employee->id }}Wage" type="number" step="0.01" value="{{ $employee->employeeOvertimeWage }}" name="employeeWage[{{ $employee->id }}][employeeOvertimeWage]" placeholder="Wprowadź stawkę" min="0" >
                        <span style="border-top-left-radius: 0; border-bottom-left-radius: 0;" class="input-group-text" id="overtime{{ $employee->id }}Wage">zł</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <div class="input-group">
                        <input style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0;" type="text" class="form-control text-right" aria-label="DefaultWage" aria-describedby="special{{ $employee->id }}Wage" type="number" step="0.01" value="{{ $employee->employeeSpecialWage }}" name="employeeWage[{{ $employee->id }}][employeeSpecialWage]" placeholder="Wprowadź stawkę" min="0" >
                        <span style="border-top-left-radius: 0; border-bottom-left-radius: 0;" class="input-group-text" id="special{{ $employee->id }}Wage">zł</span>
                    </div>
                </td>
                <td>
                    {{ $employee->employeeDefaultTime["hours"] }}h {{ $employee->employeeDefaultTime["minutes"] }}m {{ $employee->employeeDefaultTime["seconds"] }}s <br />
                    @if($employee->employeeDefaultSalary != 0)
                        <span style="color: #0c0;">
                            {{ $employee->employeeDefaultSalary }} zł
                        </span>
                    @else
                        <span style="color: #999;">
                            {{ $employee->employeeDefaultSalary }} zł
                        </span>
                    @endif
                </td>
                <td>
                    {{ $employee->employeeOverTime["hours"] }}h {{ $employee->employeeOverTime["minutes"] }}m {{ $employee->employeeOverTime["seconds"] }}s <br />
                    @if($employee->employeeDefaultSalary != 0)
                        <span style="color: #0c0;">
                            {{ $employee->employeeOverTimeSalary }} zł
                        </span>
                    @else
                        <span style="color: #999;">
                            {{ $employee->employeeOverTimeSalary }} zł
                        </span>
                    @endif
                </td>
                <td>
                    {{ $employee->employeeSpecialTime["hours"] }}h {{ $employee->employeeSpecialTime["minutes"] }}m {{ $employee->employeeSpecialTime["seconds"] }}s <br />
                    @if($employee->employeeDefaultSalary != 0)
                        <span style="color: #0c0;">
                            {{ $employee->employeeSpecialSalary }} zł
                        </span>
                    @else
                        <span style="color: #999;">
                            {{ $employee->employeeSpecialSalary }} zł
                        </span>
                    @endif
                </td>
                <td class="text-right">
                    @if($employee->employeeFinalSalary != 0)
                        <span style="color: #0c0; display: block; margin-top: 12px; line-height: 100%; font-size: 18px; text-decoration: underline;">
                            {{ $employee->employeeFinalSalary }} zł
                        </span>
                    @else
                        <span style="color: #999; display: block; margin-top: 12px; line-height: 100%; font-size: 18px; text-decoration: underline;">
                            {{ $employee->employeeFinalSalary }} zł
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach  
    </tbody>
</table>
<div class="row">
    <div class="col-12 text-center">
        <input type="submit" class="btn-primary rounded" value="Zapisz zmiany" />
    </div>
</div>
</form>
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