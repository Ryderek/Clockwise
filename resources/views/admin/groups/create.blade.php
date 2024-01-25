@extends('adminlte::page')

@section('title', 'Dodawanie grupy')

@section('content_header')
    <h1> Dodawanie grupy </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

<form action="{{ route('group.store') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                    <h4 class="m-0">Dodawanie nowej grupy</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="groupName" class="form-label">Nazwa grupy</label>
                            <input type="text" class="form-control" id="groupName" name="groupName" placeholder="Podaj nazwę grupy" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="groupPrefix" class="form-label">Guard grupy</label>
                            <input type="text" class="form-control" id="groupPrefix" name="groupPrefix" placeholder="Podaj guard grupy" maxlength="127" minlength="3" required>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Utwórz grupę</button>
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


