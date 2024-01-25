@extends('adminlte::page')

@section('title', 'Edytowanie grupy')

@section('content_header')
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

<div class="w-100 row">
    <div class="col-12 my-2">
        <form action="{{ route('group.update') }}" method="POST" autocomplete="off" class="row bg-white pb-3 rounded">
            @csrf
            <input style="display: none;" type="text" name="fakeUsernameAutofill" />
            <input style="display: none;" type="hidden" value="{{ $group->id }}" name="groupId" />
            <div class="col-12 mb-2 py-2 pl-4 bg-primary rounded">
                <h4 class="m-0">Edytowanie grupy</h4>
            </div>
            <div class="col-12 p-2">
                <div class="row">
                    <input style="display: none" type="text" name="fakeUsernameAutofill" />
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <label for="name" class="form-label">Nazwa grupy</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $group->name }}" placeholder="Podaj nazwę grupy" maxlength="127" minlength="3" required>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </div>
        </form>
    </div>

    <div class="col-12 my-2">
        <form action="{{ route('group.update-permissions') }}" method="POST" autocomplete="off" class="row bg-white pb-3 rounded">
            <div class="col-12 mb-2 py-2 pl-4 bg-gradient rounded">
                <h4 class="m-0">Edytowanie uprawnień</h4>
            </div>
            <div class="col-12 p-2">
                <div class="row">
                    @csrf
                    <input style="display: none;" type="text" name="fakeUsernameAutofill" />
                    <input style="display: none;" type="hidden" value="{{ $group->id }}" name="groupId" />
                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Zlecenia</label>
                        <div class="form-check">
                            <input class="form-check-input" name="view_orders" type="checkbox" value="" id="view_orders"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'view_orders')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="view_orders">
                                Podgląd zleceń
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="create_orders" type="checkbox" value="" id="create_orders"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'create_orders')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="create_orders">
                                Dodawanie zleceń
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="edit_orders" type="checkbox" value="" id="edit_orders"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'edit_orders')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="edit_orders">
                                Edycja zleceń
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="deploy_orders" type="checkbox" value="" id="deploy_orders"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'deploy_orders')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="deploy_orders">
                                Wydawanie zleceń
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Pracownicy</label>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_employees" type="checkbox" value="" id="manage_employees"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_employees')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_employees">
                                Zarządzanie pracownikami
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_employees_authcards" type="checkbox" value="" id="manage_employees_authcards"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_employees_authcards')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_employees_authcards">
                                Zarządzenie kartami pracowników
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Grupy i role</label>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_groups" type="checkbox" value="" id="manage_groups"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_groups')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_groups">
                                Zarządzanie grupami i uprawnieniami
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_roles" type="checkbox" value="" id="manage_roles"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_roles')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_roles">
                                Zarządzanie rolami
                            </label>
                        </div>
                    </div>


                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Narzędzia</label>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_tools" type="checkbox" value="" id="manage_tools"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_tools')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_tools">
                                Zarządzanie narzędziami
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Księgowość</label>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_accounting" type="checkbox" value="" id="manage_accounting"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_accounting')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_accounting">
                                Dostęp do rozliczeń zleceń
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" name="manage_settlement" type="checkbox" value="" id="manage_settlement"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'manage_settlement')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="manage_settlement">
                                Dostęp do rozliczeń kadr
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3 mb-3 px-4 ">
                        <label class="form-label">Powiadomienia</label>
                        <div class="form-check">
                            <input class="form-check-input" name="receive_notifications" type="checkbox" value="" id="receive_notifications"
                                @foreach($permissions as $perm)
                                    @if($perm->name == 'receive_notifications')
                                        checked
                                        @break
                                    @endif
                                @endforeach
                            >
                            <label class="form-check-label" for="receive_notifications">
                                Odbieranie powiadomień z produkcji
                            </label>
                        </div>
                    </div>


                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
@stop


