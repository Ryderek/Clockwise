@extends('adminlte::page')

@section('title', 'Role')

@section('content_header')
    <h1>Role</h1>
@stop

@if(isset($_GET['errorMessage']))
    @section('error_content')<?php print_r($_GET['errorMessage']); ?>@stop
@endif

@if(isset($_GET['successMessage']))
    @section('success_content')<?php print_r($_GET['successMessage']); ?>@stop
@endif

@section('content')

    <table class="table table-hover table-noborder bg-white">
        <thead>
            <tr class="bg-gradient">
                <th scope="col" class="bg-primary" style="border-top-left-radius: 0.25rem;">#</th>
                <th scope="col" class="bg-primary">Nazwa</th>
                <th scope="col" class="bg-primary">Proces</th>
                <th scope="col" class="bg-primary">Nazwa systemowa (slug)</th>
                <th scope="col" class="bg-primary">Utworzono</th>
                <th scope="col" class="bg-primary">Zaktualizowano</th>
                <th scope="col" class="text-end bg-primary" style="border-top-right-radius: 0.25rem;">Akcja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <th scope="row">{{ $role->roleId  }}</th>
                    <td>{{ $role->roleName }}</td>
                    <td>{{ $role->roleProcess }}</td>
                    <td>{{ $role->roleSlug }}</td>
                    <td>{{ $role->created_at }}</td>
                    <td>{{ $role->updated_at }}</td>
                    <td>
                        <a style="color: inherit;" title="Usuń wybrane narzędzie" data-bs-toggle="modal" data-bs-target="#removeRoleModal" data-bs-deleteRoleId="{{ $role->roleId }}" data-bs-removeRoleName="{{ $role->roleName }}">
                            <i class="fas fa-trash"></i>
                        </a>
                        <a style="color: inherit;" title="Edytuj wybrane narzędzie" href="{{ route('role.edit', ["id" => $role->roleId ]) }}"> &nbsp; &nbsp; 
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('layouts.partials.pagination')
    @include('admin.roles.partials.delete-modal')
@stop

@section('css')
    <style>
        .table-noborder{
            border-radius: 0.25rem;
        }
        .table-noborder th,
        .table-noborder td{
            border: none!important;
        }
    </style>
@stop

@section('js')
    <script>
        var removeRoleModal = document.getElementById('removeRoleModal')
        removeRoleModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var deleteRoleId = button.getAttribute('data-bs-deleteRoleId')
            var removeRoleName = button.getAttribute('data-bs-removeRoleName')
            var removeRoleSpan = removeRoleModal.querySelector('#removeRoleName')
            var removeRoleIdInput = removeRoleModal.querySelector('#deleteRoleId')

            removeRoleSpan.textContent = removeRoleName
            removeRoleIdInput.value = deleteRoleId
        })

    </script>
@stop