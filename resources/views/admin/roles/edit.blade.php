@extends('adminlte::page')

@section('title', 'Role')

@section('content_header')
    <h1> Edytowanie roli </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')
    
<form action="{{ route('role.update') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    <input style="display: none;" type="hidden" value="{{ $role->roleId }}" readonly name="editRoleId" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                    <h4 class="m-0">Szczegóły roli</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-3">
                            <label for="roleName" class="form-label">Nazwa roli</label>
                            <input type="text" class="form-control" id="roleName" name="roleName" value="{{ $role->roleName }}" placeholder="Podaj nazwę roli" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleProcess" class="form-label">Nazwa procesu</label>
                            <input type="text" class="form-control" id="roleProcess" name="roleProcess" value="{{ $role->roleProcess }}" placeholder="Podaj nazwę procesu" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleSlug" class="form-label">Nazwa systemowa (slug)</label>
                            <input type="text" class="form-control" id="roleSlug" name="roleSlug"  value="{{ $role->roleSlug }}" placeholder="Podaj nazwę systemową" readonly maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="roleStations" class="form-label">Ilosć stanowisk</label>
                            <input type="number" class="form-control" id="roleStations" name="roleStations"  value="{{ $role->roleStations }}" placeholder="Podaj ilość stanowisk" max="127" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </div>
</form>
{{--
<div class="w-100 row">
    <div class="col-12 my-2">
        <div class="row bg-white pb-1 rounded">
            <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                <h4 class="m-0">Stanowiska roli</h4>
            </div>
            <div class="col-12 pt-2">
                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Stanowisko</th>
                            <th scope="col">Utworzono</th>
                            <th scope="col" class="text-right">Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mark</td>
                            <td>data</td>
                            <td class="text-right"><i class="fas fa-trash"></i></td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Jacob</td>
                            <td>data</td>
                            <td class="text-right"><i class="fas fa-trash"></i></td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Larry the Bird</td>
                            <td>data</td>
                            <td class="text-right"><i class="fas fa-trash"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
--}}

@stop

@section('css')

@stop

@section('js')
@stop


