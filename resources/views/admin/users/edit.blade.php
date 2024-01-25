@extends('adminlte::page')

@section('title', 'Edytowanie pracownika')

@section('content_header')
    <h1> Edytowanie pracownika </h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

<div class="w-100 row">
    <form class="col-12 my-2" action="{{ route('employee.update') }}" method="POST" autocomplete="off">
        <input style="display: none;" type="text" name="fakeUsernameAutofill" />
        <input style="display: none;" type="hidden" value="{{ $user->id }}" name="id" />
        @csrf
        <div class="row bg-white pb-3 rounded">
            <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                <h4 class="m-0">{{ $user->name }}</h4>
            </div>
            <div class="col-12 p-2 pb-0">
                <div class="row">
                    <input style="display: none" type="text" name="fakeUsernameAutofill" />
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <label for="name" class="form-label">Imie i nazwisko</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" placeholder="Podaj imię i nazwisko" maxlength="127" minlength="3" required>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}" placeholder="Podaj adres email" maxlength="127" minlength="3" required>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <label for="address" class="form-label">Adres korespondencyjny</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}" placeholder="Podaj adres do koresponedncji" maxlength="127" minlength="3">
                    </div>
                    <div class="col-12 col-md-3 col-xl-2 mb-3">
                        <label for="birthDate" class="form-label">Data urodzenia</label>
                        <input type="date" class="form-control" id="birthDate" name="birthDate" value="{{ substr($user->birthDate, 0, 10) }}" placeholder="Podaj datę urodzenia">
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <label for="password" class="form-label">Hasło</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="password" name="password" style="<?php /*border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 0; */ ?>" placeholder="Podaj hasło" maxlength="127" aria-label="Hasło" aria-describedby="passwordd" minlength="8">
                            <!-- <span class="input-group-text" id="passwordd" style="border-top-left-radius: 0; border-bottom-left-radius: 0; cursor: pointer;">👁️</span> -->
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label for="groupId" class="form-label">Grupa</label>
                        <select class="form-select form-control" id="groupId" name="groupId" aria-label="" required>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" <?php if($user->groupId == $group->id){ echo("selected"); }  ?>>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <label for="partTimeJob" class="form-label">Wymiar godzinowy</label>
                        <select class="form-select form-control" id="partTimeJob" name="partTimeJob" aria-label="" required>
                            <option value="0.25" @if($user->partTimeJob == 0.25) selected @endif>1/4 etatu</option>
                            <option value="0.5" @if($user->partTimeJob == 0.5) selected @endif>1/2 etatu</option>
                            <option value="0.75" @if($user->partTimeJob == 0.75) selected @endif>3/4 etatu</option>
                            <option value="1" @if($user->partTimeJob == 1) selected @endif>Pełny etat</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="col-12 ml-0 pl-0">
                    <button type="submit" class="btn btn-primary">Aktualizuj</button>
                </div>
            </div>
        </div>
    </form>
    <div class="col-12 my-2">
        <div class="row bg-white pb-3 rounded">
            <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                <h4 class="m-0">Role pracownika</h4>
            </div>
            <div class="col-12 p-2 pb-0">
                <div class="d-md-flex">
                    @foreach($userRoles as $role)
                        <form method="POST" class="mr-3 mb-3 d-flex" action="{{ route('role.release') }}">
                            @csrf
                            <input type="hidden" name="backToUserId" value="{{ $user->id }}" />
                            <input type="hidden" name="deleteRelationId" value="{{ $role->userRoleRelationId }}" />
                            <input type="submit" value="{{ $role->roleName }}" class="btn border-primary" />
                        </form>
                    @endforeach
                </div>
            </div>
            <div class="col-12">
                <div class="col-12 ml-0 pl-0">
                    <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">Dodaj rolę</button>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.users.partials.add-role-modal')
@stop

@section('css')

@stop

@section('js')
@stop


