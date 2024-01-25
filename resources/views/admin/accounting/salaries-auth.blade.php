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
    <form method="POST" action="{{ route('salaries') }}">
        @csrf


        <div class="card mt-3 text-center" style="margin-left: auto; margin-right: auto; max-width: 500px;">
            <h5 class="card-header">Zabezpieczenie</h5>
            <div class="card-body">
                <h5 class="card-title mb-2">Wprowadź hasło</h5>
                <input class="form-control" type="password" placeholder="Wprowadź kod PIN" name="salariesPass">
                <input type="submit" value="Zatwierdź" class="btn-primary rounded p-2 mt-3" />
            </div>
        </div>

    </form>
@stop


@section('css')
<style>
</style>
@stop

@section('js')
@stop