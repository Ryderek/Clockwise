@extends('adminlte::page')

@section('title', 'Pracownicy')

@section('content_header')
    <h1>Pracownicy</h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@section('content')
    <table class="table table-hover table-noborder bg-white">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary">Imie (imiona) i nazwisko</th>
                <th scope="col" class="bg-primary">Data urodzenia</th>
                <th scope="col" class="bg-primary">Adres kontaktowy</th>
                <th scope="col" class="bg-primary">E-mail</th>
                <th scope="col" class="bg-primary">Grupa</th>
                <th scope="col" class="bg-primary">Zaktualizowano</th>
                <th scope="col" class="text-end bg-primary text-right" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td scope="col" style="border-top-left-radius: 0.25rem;">{{ $user->id }}</td>
                    <td scope="col">{{ $user->name }}</td>
                    <td scope="col">{{ substr($user->birthDate, 0, 10); }}</td>
                    <td scope="col">{{ $user->address }}</td>
                    <td scope="col">{{ $user->email }}</td>
                    <td scope="col">
                        @foreach($groups as $group)
                            @if($user->groupId == $group->id)
                                {{ $group->name }}
                            @endif
                        @endforeach
                    </td>
                    <td scope="col">{{ $user->updated_at }}</td>
                    <td scope="col" class="text-right">
                        <a style="color: inherit;" title="Usuń wybrane narzędzie" data-bs-toggle="modal" data-bs-target="#removeEmployeeModal" data-bs-deleteEmployeeId="{{ $user->id }}" data-bs-removeEmployeeName="{{ $user->name }}">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a style="color: inherit; padding-left: 15px;" title="Edytuj wybranego pracownika" href="{{ route('employee', ["id" => $user->id ]) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('layouts.partials.pagination')
@stop

@include('admin.users.partials.delete-user-modal')

@section('css')
    <style>
    </style>
@stop

@section('js')<script>
    var removeEmployeeModal = document.getElementById('removeEmployeeModal')
    removeEmployeeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var deleteEmployeeId = button.getAttribute('data-bs-deleteEmployeeId')
        var removeEmployeeName = button.getAttribute('data-bs-removeEmployeeName')
        var removeEmployeeSpan = removeEmployeeModal.querySelector('#removeEmployeeName')
        var removeEmployeeIdInput = removeEmployeeModal.querySelector('#deleteEmployeeId')

        removeEmployeeSpan.textContent = removeEmployeeName
        removeEmployeeIdInput.value = deleteEmployeeId
    })

</script>
@stop