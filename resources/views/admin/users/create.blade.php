@extends('adminlte::page')

@section('title', 'Dodawanie pracownika')

@section('content_header')
    <h1> Dodawanie pracownika </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

<form action="{{ route('employee.store') }}" method="POST" autocomplete="off">
    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
    @csrf
    <div class="w-100 row">
        <div class="col-12 my-2">
            <div class="row bg-white pb-3 rounded">
                <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                    <h4 class="m-0">Dodawanie nowego pracownika</h4>
                </div>
                <div class="col-12 p-2">
                    <div class="row">
                        <input style="display: none" type="text" name="fakeUsernameAutofill" />
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="name" class="form-label">Imie i nazwisko</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Podaj imię i nazwisko" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Podaj adres email" maxlength="127" minlength="3" required>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="address" class="form-label">Adres korespondencyjny</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Podaj adres do koresponedncji" maxlength="127" minlength="3">
                        </div>
                        <div class="col-12 col-md-3 col-xl-2 mb-3">
                            <label for="birthDate" class="form-label">Data urodzenia</label>
                            <input type="date" class="form-control" id="birthDate" name="birthDate" placeholder="Podaj datę urodzenia">
                        </div>

                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="password" class="form-label">Hasło</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="password" value="{{ $randomPassword }}" name="password" style="<?php /*border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0; */ ?>" placeholder="Podaj hasło" maxlength="127" aria-label="Hasło" aria-describedby="passwordd" minlength="8" required>
                                <!-- <span class="input-group-text" id="passwordd" style="border-top-left-radius: 0; border-bottom-left-radius: 0; cursor: pointer;">👁️</span> -->
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="groupId" class="form-label">Grupa</label>
                            <select class="form-select form-control" id="groupId" name="groupId" aria-label="" required>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                              </select>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3 mb-3">
                            <label for="partTimeJob" class="form-label">Wymiar godzinowy</label>
                            <select class="form-select form-control" id="partTimeJob" name="partTimeJob" aria-label="" required>
                                <option value="0.25">1/4 etatu</option>
                                <option value="0.5">1/2 etatu</option>
                                <option value="0.75">3/4 etatu</option>
                                <option value="1">Pełny etat</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary">Utwórz pracownika</button>
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


