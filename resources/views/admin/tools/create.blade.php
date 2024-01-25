@extends('adminlte::page')

@section('title', 'Narzędzia')

@section('content_header')
    <h1> Dodawanie narzędzia </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    
<form action="{{ route('tool.create') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                    <h4 class="m-0">Informacje o narzędziu</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-8 col-xl-8">
                            <label for="toolName" class="form-label">Nazwa narzędzia</label>
                            <input type="text" class="form-control" id="toolName" name="toolName" placeholder="Podaj nazwę narzędzia" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3 col-xl-2 mt-md-0 mt-3">
                            <label for="toolStatus" class="form-label">Status</label>
                            <select class="form-control" name="toolStatus" id="toolStatus">
                                <option value="available">Sprawne</option>
                                <option value="damaged">Uszkodzone</option>
                                <option value="workbench">W naprawie</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Utwórz narzędzie</button>
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


