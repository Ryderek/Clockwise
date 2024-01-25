@extends('adminlte::page')

@section('title', 'Role')

@section('content_header')
    <h1> Role </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

<form action="{{ route('role.create') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                    <h4 class="m-0">Dodawanie nowej roli</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-3">
                            <label for="roleName" class="form-label">Nazwa roli</label>
                            <input type="text" class="form-control" id="roleName" name="roleName" placeholder="Podaj nazwę roli" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleProcess" class="form-label">Nazwa procesu</label>
                            <input type="text" class="form-control" id="roleProcess" name="roleProcess" placeholder="Podaj nazwę procesu" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleSlug" class="form-label">Nazwa systemowa (slug)</label>
                            <input type="text" class="form-control" id="roleSlug" name="roleSlug" placeholder="Podaj nazwę systemową" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleStations" class="form-label">Ilosć stanowisk</label>
                            <input type="text" class="form-control" id="roleStations" name="roleStations"  value="1" placeholder="Podaj ilość stanowisk" readonly maxlength="127" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Utwórz rolę</button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')

@stop

@section('js')
@stop


